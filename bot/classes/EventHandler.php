<?php

class EventHandler extends \danog\MadelineProto\EventHandler
{
    use AppName\abilities\cluster,
        AppName\abilities\awaitings;

    public function __construct($MadelineProto)
    {
        parent::__construct($MadelineProto);
        $this->cluster = new cluster();
        $this->awaitings = new awaitings();
        yield $this->updateInfo();
    }
    public function onUpdateBotCallbackQuery($update)
    {
        yield $this->messages->setTyping(['peer' => $update, 'action' => ['_' => 'sendMessageTypingAction']]);

        $markups = ['_' => 'replyInlineMarkup', 'rows' => [
            ['_' => 'keyboardButtonRow', 'buttons' => [
                [
                    '_' => 'keyboardButtonCallback',
                    'text' => $this->info['button_again'],
                    'data' => "again",
                ]
            ]]
        ]];
        $options = [
            'peer' => $update,
            'id' => $update['msg_id'],
            'message' => $update['data'],
            'reply_markup' => $markups,
            'parse_mode' => 'HTML',
        ];
        

        if ($update['data'] == "true") {
            $options['message'] = $this->info['info_correct'];
            $options['reply_markup'] = null;
        } elseif ($update['data'] == "again") {
            try {
                $obtain = yield $this->obtain();
            } catch (\Throwable $e) {
                print $e->getMessage();
            }
            $message = "<b>{$obtain['header']},</b><br>{$obtain['text']}";
            $options['message'] = $message;
            $options['reply_markup'] = $obtain['markups'];
        } elseif ($update['data'] == "false") {
            $options['message'] = $this->info['info_inCorrect'];
        }

        try {
            yield $this->messages->editMessage($options);
        } catch (\Throwable $e) {
            print $e->getMessage();
        }
        yield $this->messages->setBotCallbackAnswer(['query_id' => $update["query_id"], 'cache_time' => 0]);
    }
    public function onUpdateNewMessage($update)
    {
        if (isset($update['message']['out']) && $update['message']['out']) {
            return;
        }
        $res = json_encode($update, JSON_PRETTY_PRINT);
        if ($res == '') {
            $res = var_export($update, true);
        }

        $awaitings = yield $this->awaitings->pullWith([
            "user" => $res['message']['from_id']
        ]);

        if ($awaitings['user']['isNew']) {
            $Chat = yield $this->get_info($update);
            $user = $update['message']['from_id'];
            if (!isset($Chat['User']['first_name']) && empty($Chat['User']['first_name'])) $Chat['User']['first_name'] = null;
            if (!isset($Chat['User']['last_name']) && empty($Chat['User']['last_name'])) $Chat['User']['last_name'] = null;
            yield $this->db->query("INSERT INTO users (user, first_name, last_name) VALUES ('$user', '{$Chat['User']['first_name']}', '{$Chat['User']['last_name']}')");
        }

        try {

            $message = $res['message']['message'];

            // Check for entered commands
            if (in_array($message, $this->cluster->commands)) {
                $return = yield $this->cluster->$message();
                          yield $this->handle($update, $return);
            }

        } catch (Exception $e) {
            yield print $e->getMessage()."\r\nThe exception was created on line: " . $e->getLine();
        }

        if ($update['message']['message'] == "/SendMessage" && $this->isOpped($update['message']['from_id'])) {
            yield $this->updateInfo();
            $options = $this->game['options'];
            yield $this->forUsers(function ($peer) use ($options) {
                try {
                    $options['peer'] = $peer;
                    yield $this->messages->sendMedia($options);
                } catch (\Throwable $e) {
                    yield print $e->getMessage();
                }
            });

            return;
        }

        if ($update['message']['message'] == "/showWinner" && $this->isOpped($update['message']['from_id'])) {
            $user = yield $this->forUsers(function ($peer) {
                yield $user = $this->isWaiting($peer['user_id'], "phoneNumber");
                if (!$user['_']) return ['_' => true, 'return' => $user];
            }, true);

            yield $this->forUsers(function ($peer) use ($user) {
                try {
                    $options['peer'] = $peer;
                    $options['parse_mode'] = "HTML";
                    $options['message'] = str_replace("%name%", $user['this'], $this->info['winner_message']);
                    yield $this->messages->sendMessage($options);
                } catch (\Throwable $e) {
                    print $e->getMessage();
                }
            });

            return;
        }

        if ($update['message']['message'] == "/updateInfo" && $this->isOpped($update['message']['from_id'])) {
            yield $this->updateInfo();
            yield $options = [
                'peer' => $update,
                'message' => "Данные были обновлены",
                'parse_mode' => 'HTML',
            ];
            try {
                yield $this->messages->sendMessage($options);
            } catch (\Throwable $e) {
                print $e->getMessage();
            }
            return;
        }

        if (yield $this->isWaiting($update, "phoneNumber")['_']) {
            $user = $update['message']['from_id'];
            $phone = $update['message']['message'];
            yield $this->db->query("UPDATE users set phone = '$phone' WHERE user = '$user'");
            $options = [
                'peer' => $update,
                #'message' => "Ваш телефон ({$update['message']['message']}) был подписан на рассылку! Вам будут приходить интересные сообщения.)",
                'parse_mode' => 'HTML',
            ];
            $start_after = str_replace('%name%', $phone, $this->info['start_after']);
            $options['message'] = $start_after;
            try {
                yield $this->messages->sendMessage($options);
            } catch (\Throwable $e) {
                print $e->getMessage();
            }
            return;
        }

        try {
            if (isset($update['message']['media']) && ($update['message']['media']['_'] == 'messageMediaPhoto' || $update['message']['media']['_'] == 'messageMediaDocument')) {
                $time = microtime(true);
                $file = yield $this->download_to_dir($update, 'photos');
                yield $this->messages->sendMessage(['peer' => $update, 'message' => 'Downloaded to '.$file.' in '.(microtime(true) - $time).' seconds', 'reply_to_msg_id' => $update['message']['id']]);
            }
        } catch (\danog\MadelineProto\RPCErrorException $e) {
            yield $this->messages->sendMessage(['peer' => '@danogentili', 'message' => $e]);
        }
    }
}

class Matter extends EventHandler
{
    public function handle (array &$update, $closure) {
        $options = [ // Default options
            "peer" => &$update,
            "message" => "Default message",
            'parse_mode' => 'HTML',
        ];
        unset($options['_']);

        if (gettype($closure) == object)
            foreach (yield $closure->__invoke() as $key => $value)
                yield $options[$key] = &$value;

        yield $this->message($options);
        throw new Exception("OK");
    }
    public function eachUser(object $closure, $random = false)
    {
        $me = yield $this->get_self();
        $dialogs = yield $this->get_dialogs();

        if ($random) shuffle($dialogs);
        foreach ($dialogs as $peer) {
            if ($peer['user_id'] == $me['id']) continue;
            $action = yield @$closure->__invoke($peer);
            if ($action['_']) return $action['return'];
        }
    }
    public function updateInfo()
    {
        // Updating general information
        $result = $this->db->query("SELECT name, content FROM info");
        while (yield $row = $result->fetch_assoc()) {
            yield $this->info[$row['name']] = $row['content'];
        }

        // Updating game options
        $obtain = yield $this->obtain();
        yield $message = "<b>{$obtain['header']},</b><br>{$obtain['text']}";
        $options = [
            'message' => $message,
            'media' => [
                '_' => 'inputMediaUploadedPhoto',
                'file' => $obtain['picture'],
            ],
            'reply_markup' => $obtain['markups'],
            'parse_mode' => 'HTML',
        ];
        $this->game['options'] = $options;
    }
    public function message(array $options)
    {
        try {
            switch ($options['method']) {
                case "edit":
                    if (!isset($options['id'])) {
                        $options['id'] = $options['peer']['message']['id']+1;
                    }
                    yield $this->messages->editMessage($options);
                    break;

                case "media":
                    yield $this->messages->sendMedia($options);
                    break;
                
                default:
                    yield $this->messages->sendMessage($options);
                    break;
            }
        } catch (\danog\MadelineProto\RPCErrorException $e) {
            yield $this->messages->sendMessage(['peer' => 565324826, 'message' => $e]);
        }
    }
    public function obtain()
    {
        yield $result = $this->db->query("SELECT * FROM questions WHERE id = '3'");
        yield $row = $result->fetch_assoc();

        $header = $row['qtitle'];
        $text = $row['qtext'];
        $picture = empty($row['picture']) ? null:$row['picture'];
        yield $answers = json_decode($row['answers']);

        if ($row['answers'] != "[]") {
            $i = 1;
            foreach ($answers as $key => $value) {
                $buttons[] = [
                    '_' => 'keyboardButtonCallback',
                    'text' => "{$value->title}",
                    'data' => "{$value->correct}",
                ];
                if ($i == count($answers) || ($i > 0 && ($i % 2 == 0))) {
                    $rows[] = ['_' => 'keyboardButtonRow', 'buttons' => $buttons];
                    unset($buttons);
                }
                $i++;
            }
            $markups = ['_' => 'replyInlineMarkup', 'rows' => $rows];
        }

        return [
            "header" => $header,
            "text" => $text,
            "picture" => $picture,
            "markups" => isset($markups) ? $markups:null,
        ];
    }
}



?>