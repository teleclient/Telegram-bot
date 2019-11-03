<?php

class cluster
{
    public function __construct() {
        print "Commands cluster has publicted";
        $commands = get_class_methods($this);
        unset($commands[0]);
        $this->commands = $commands;
    }

    function __start($update)
    {
        $options = [
            'message' => $this->info['start_before'],
            'parse_mode' => 'HTML',
        ];
        
        $Chat = yield $this->get_info($update);
        $name = yield $this->isWaiting($update, "phoneNumber");
        if (!$name['_']) {
            $start_after = str_replace('%name%', $name['this'], $this->info['start_after']);
            $options['message'] = $start_after;
        }

        return $options;
    }
}

?>