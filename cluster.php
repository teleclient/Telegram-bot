<?php namespace AppName\bot\foreground;

cluster::AddHandler([
    "command" => "/hahui",
    "message" => "Good day, Sir!",
    "eval" => cluster::Eval(function (&$thisArray,) {
        $thisArray["message"] .= " - You got zalupa, Sir.";
        print_r($thisArray);
    }),
]);

cluster::AddHandler([
    "command" => "jopa",
    "message" => "Anal, then."
]);

?> 