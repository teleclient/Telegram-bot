<?php namespace AppName\abilities;

class cluster
{
    use \AppName\abilities\stringer;

    public function __construct() {
        print "Commands cluster has publicted";
        $commands = get_class_methods($this);
        unset($commands[0]);
        $this->commands = $commands;
        $this->stringer = new stringer(APPNAME_BOT_DIR."/strings");
    }

    private function info() {
        return $this->stringer->cat("bot");
    }

    function start($update, $extra)
    {
        $options = [
            'message' => $this->info['start_before'],
            'parse_mode' => 'HTML',
        ];
        
        $name = yield $this->isWaiting($update, "phoneNumber");
        if (!$name['_'])
            $options['message'] = $this->info->message->before;

        return $options;
    }

    function begin($update, $extra)
    {
        $this->start($update, $extra);
    }

    function SendMessage()
    {
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
}
