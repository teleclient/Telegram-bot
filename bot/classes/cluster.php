<?php namespace Magitued\bot\foreground;

class cluster
{
    static public $currentHandlerArray;
    static public $HandlersArray;
    static public function Eval(object $evaluation) : object
    {
        //return $evaluation.__invoke(self::$currentHandlerArray);
        return $evaluation;
    }
    static public function AddHandler(array $HandlerArray) : void
    {
        self::$HandlersArray[$HandlerArray["command"]] = &$HandlerArray;
    }
}

?>