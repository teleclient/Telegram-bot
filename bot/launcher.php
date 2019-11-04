<?php

if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}
include 'madeline.php';
include 'useful.php'; // Useful classes and functions
include 'traits/load.php';
    \AppName\traits\load();

include 'cluster.php'; // Commands cluster
include 'EventHandler.php'; // Main event handler

use danog\MadelineProto\Stream\MTProtoTransport\ObfuscatedStream;
use danog\MadelineProto\Stream\Proxy\HttpProxy;
use danog\MadelineProto\Stream\Proxy\SocksProxy;

$settings = file_get_contents(APPNAME_BOT_DIR . "/settings.json");
$settings = json_decode($settings, true);

$settings['connection_settings']['all']['proxy'] = ObfuscatedStream::getName();

include 'login_helper.php'; // may not start without this
$MadelineProto = new \danog\MadelineProto\API('sessions/bot.session', $settings);
$MadelineProto->async(true);
$MadelineProto->loop(function () use ($MadelineProto) {
    yield $MadelineProto->start();
    yield $MadelineProto->setEventHandler('\EventHandler');
});
$MadelineProto->loop();
