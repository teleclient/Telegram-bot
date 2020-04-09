<?php

class EventHandler extends \danog\MadelineProto\EventHandler
{
    private $awaitings;
    private $info;
    private $HandlersArray;
    public function __construct($MadelineProto)
    {
        parent::__construct($MadelineProto);
        $db = new \Magitued\abilities\DataBase(Magitued_BOT_DIR . "/database.json");
        $stringer = new \Magitued\abilities\stringer(Magitued_BOT_DIR . "/strings");
        $this->awaitings = new \Magitued\abilities\awaitings($db);
        $this->HandlersArray = \Magitued\bot\foreground\cluster::$HandlersArray;
        $this->info = $stringer->cat("bot");
    }
    public function commands(object $closure)
    {
        try {
            yield $closure->__invoke();
        } catch (Exception $e) {
            print $e->getMessage();
        }
    }
    public function onUpdateNewMessage($update)
    {
        if (isset($update['message']['out']) && $update['message']['out']) {
            return;
        }

        global $time;
        $time = microtime(true);
        
        yield $this->commands(function () use (&$handleMessage, &$update) {
            global $time;
            $message = $update["message"]["message"];
            if (isset($this->HandlersArray[$message])) {
                $HandlerArray = $this->HandlersArray[$message];
                isset($HandlerArray["eval"]) ? true : $HandlerArray["eval"] = function () { };
                yield $HandlerArray["eval"]($HandlerArray, $update, $this);
                yield $this->message([
                    "peer" => $update['message']['from_id'],
                    "message" => $HandlerArray["message"] . ' in ' . (microtime(true) - $time).' seconds',
                    'parse_mode' => 'HTML',
                ]);
            }
        });

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

        if (gettype($closure) == "object")
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
    public function message(array $options)
    {
        if (!isset($options['method'])) $options['method'] = null;
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
}



?>