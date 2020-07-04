<?php namespace Magitued\bot\foreground;

// Setup Settings
\useful::setUp([
    "notice" => on,
]);

// Commands Handlers
cluster::AddHandler([
    "command" => "/hahui",
    "message" => "Good day, Sir!",
    "eval" => cluster::Eval(function (&$thisArray) {
        $thisArray["message"] .= " - You got zalupa, Sir.";
        print_r($thisArray);
    }),
]);

cluster::AddHandler([
    "command" => "jopa",
    "message" => "Anal, then.",
]);

cluster::AddHandler([
    "command" => "/stop",
    "message" => "Bot has been stopped",
    "eval" => function (&$thisArray, &$UserDataArray, &$MadelineProto) {
        print_r($UserDataArray);
        print_r(yield $MadelineProto->getFullInfo($UserDataArray["message"]["from_id"])->user->username);
    },
    "able" => [
        +79610870907,
    ],
]);

?> 