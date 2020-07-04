<?php

if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}

include 'madeline.php';


include_once '../useful.php';
include 'classes/load.php';

// Commands Cluster
include "../cluster.php";
// Event Handler
include 'EventHandler.php';

$settings = file_get_contents(Magitued_BOT_DIR . "/settings.json");
$settings = json_decode($settings, true);

// define('MADELINE_BRANCH','dev');

#include 'login_helper.php'; 
$MadelineProto = new \danog\MadelineProto\API('sessions/bot.session', $settings);
$MadelineProto->async(true);
$MadelineProto->resetUpdateState();
$MadelineProto->loop(function () use (&$MadelineProto) {
    yield $MadelineProto->start();
    yield $MadelineProto->setEventHandler('\Matter');
    yield $MadelineProto->messages->sendMessage([
        "peer" => 565324826,
        "message" => "Bot's started by Magitued.",
    ]);
});
$MadelineProto->loop();
