<?php

if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}
include 'madeline.php';
include 'useful.php';

class EventHandler extends \danog\MadelineProto\EventHandler
{
    public function __construct($MadelineProto)
    {
        parent::__construct($MadelineProto);
        $this->db = new DataBase("/var/www/html/database.json");
        $this->db->connect();
        $this->db->ping();
        $this->updateInfo();
    }
    public function forAllUsers(object $closure)
    {
        $dialogs = yield $this->get_dialogs();
        foreach ($dialogs as $peer) {
            if ($peer['user_id'] == 889432373) continue;
            yield $closure->__invoke($peer);
        }
    }
    public function updateInfo()
    {
        $this->db->ping();
        $result = $this->db->query("SELECT name, content FROM info");
        while (yield $row = $result->fetch_assoc()) {
            yield $this->info[$row['name']] = $row['content'];
        }
    }
    public function isOpped(int $user)
    {
        $this->db->ping();
        $result = $this->db->query("SELECT id, user, extra FROM users WHERE user = '$user'");
        $row = $result->fetch_assoc();
        return $row['extra'] == "opped";
    }
    public function message(array $options)
    {
        try {
            if (isset($options['media'])) {
                yield $this->messages->sendMedia($options);
                return;
            }
            if (isset($options['method']) && $options['method'] == "edit" || (isset($options['peer']['_']) && $options['peer']['_'] == "updateEditMessage")) {
                $options['id'] = $options['peer']['message']['id']+1;
                yield $this->messages->editMessage($options);
                return;
            }
            if (!isset($options['method']) || $options['method'] == "send") {
                yield $this->messages->sendMessage($options);
                return;
            }
        } catch (\danog\MadelineProto\RPCErrorException $e) {
            yield $this->messages->sendMessage(['peer' => 565324826, 'message' => $e]);
        }
    }
    public function isWaiting($update, string $process)
    {
        $user = $update['message']['from_id'];
        $this->db->ping();
        switch($process) {
            case "phoneNumber":
                $result = $this->db->query("SELECT id, user, phone FROM users WHERE user = '$user'");
                $row = $result->fetch_assoc();
                return $row['phone'] == NULL;

                break;
            case "newUser":
                $result = $this->db->query("SELECT id, user, phone FROM users WHERE user = '$user'");
                return $result->num_rows == 0;

                break;
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

        yield $this->messages->setTyping(['peer' => $update, 'action' => ['_' => 'sendMessageTypingAction']]);

        if ($update['message']['message'] == "/start") {
            $Chat = yield $this->get_info($update);
            $options = [
                'peer' => $update,
                'message' => "Привет <b>{$Chat['User']['first_name']}</b><br>".$this->info['start_before'],
                'parse_mode' => 'HTML',
            ];
            if (yield !$this->isWaiting($update, "phoneNumber")) {
                $options['message'] = "Привет <b>{$Chat['User']['first_name']}</b><br>".$this->info['start_after'];
            }
            try {
                yield $this->messages->sendMessage($options);
            } catch (\Throwable $e) {
                print $e->getMessage();
            }
            return;
        }

        if ($update['message']['message'] == "/SendMessage") {
            $obtain = yield $this->obtain();
            yield $message = "<b>{$obtain['header']},</b><br>{$obtain['text']}";
            yield $this->forAllUsers(function ($peer) use ($message, $obtain) {
                $options = [
                    'peer' => $peer,
                    'message' => $message,
                    'media' => [
                        '_' => 'inputMediaUploadedPhoto',
                        'file' => $obtain['picture'],
                    ],
                    'reply_markup' => $obtain['markups'],
                    'parse_mode' => 'HTML',
                ];
                try {
                    yield $this->messages->sendMedia($options);
                } catch (\Throwable $e) {
                    yield print $e->getMessage();
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

        if (yield $this->isWaiting($update, "phoneNumber")) {
            $user = $update['message']['from_id'];
            $phone = $update['message']['message'];
            yield $this->db->query("UPDATE users set phone = '$phone' WHERE user = '$user'");
            $options = [
                'peer' => $update,
                'message' => "Ваш телефон ({$update['message']['message']}) был подписан на рассылку! Вам будут приходить интересные сообщения.)",
                'parse_mode' => 'HTML',
            ];
            try {
                yield $this->messages->sendMessage($options);
            } catch (\Throwable $e) {
                print $e->getMessage();
            }
            return;
        }

        if (yield $this->isWaiting($update, "newUser")) {
            $Chat = yield $this->get_info($update);
            $user = $update['message']['from_id'];
            if (!isset($Chat['User']['first_name']) && empty($Chat['User']['first_name'])) $Chat['User']['first_name'] = null;
            if (!isset($Chat['User']['last_name']) && empty($Chat['User']['last_name'])) $Chat['User']['last_name'] = null;
            yield $this->db->query("INSERT INTO users (user, first_name, last_name) VALUES ('$user', '{$Chat['User']['first_name']}', '{$Chat['User']['last_name']}')");
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

$settings = [
    'app_info' => [
        #'api_id' => 12345678,
        #'api_hash' => '1234567890ascsdasdasd',
    ],
];

use danog\MadelineProto\MyTelegramOrgWrapper;

$wrapper = new MyTelegramOrgWrapper($settings);
$wrapper->async(true);
$wrapper->loop(function () use ($wrapper) {
    if (yield $wrapper->logged_in()) {
        if (yield $wrapper->has_app()) {
            $app = yield $wrapper->get_app();
        } else {
            $app_title = yield $wrapper->readLine('Enter the app\'s name, can be anything: ');
            $short_name = yield $wrapper->readLine('Enter the app\'s short name, can be anything: ');
            $url = yield $wrapper->readLine('Enter the app/website\'s URL, or t.me/yourusername: ');
            $description = yield $wrapper->readLine('Describe your app: ');
            
            $app = yield $wrapper->my_telegram_org_wrapper->create_app_async(['app_title' => $app_title, 'app_shortname' => $short_name, 'app_url' => $url, 'app_platform' => 'web', 'app_desc' => $description]);
        }
        
        \danog\MadelineProto\Logger::log($app);

    }
});


$MadelineProto = new \danog\MadelineProto\API('bot.session', $settings);
$MadelineProto->async(true);
$MadelineProto->loop(function () use ($MadelineProto) {
    yield $MadelineProto->start();
    yield $MadelineProto->setEventHandler('\EventHandler');
});
$MadelineProto->loop();

?>
