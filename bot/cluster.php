<?php

class cluster
{
    use \AppName\traits\stringer;

    public function __construct() {
        print "Commands cluster has publicted";
        $commands = get_class_methods($this);
        unset($commands[0]);
        $this->commands = $commands;
        $this->stringer = new stringer(APPNAME_BOT_DIR."/strings");
    }

    private function info($which) {
        return $this->stringer->cat[$which];
    }

    function start($update, $extra)
    {
        $options = [
            'message' => $this->info['start_before'],
            'parse_mode' => 'HTML',
        ];
        
        $name = yield $this->isWaiting($update, "phoneNumber");
        if (!$name['_']) {
        $start_after = str_replace('%name%', $name['this'], $this->info('bot')->start_after);
            $options['message'] = $start_after;
        }

        return $options;
    }

    function begin($update, $extra)
    {
        $this->start($update, $extra);
    }
}

?>