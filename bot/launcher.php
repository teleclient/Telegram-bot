<?php

if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}
include 'madeline.php';
include 'usefule.php';

\AppName\abilities\load();

use danog\MadelineProto\Stream\MTProtoTransport\ObfuscatedStream;
use danog\MadelineProto\Stream\Proxy\HttpProxy;
use danog\MadelineProto\Stream\Proxy\SocksProxy;

$settings = file_get_contents(APPNAME_BOT_DIR . "/settings.json");
$settings = json_decode($settings, true);

$settings['connection_settings']['all']['proxy'] = ObfuscatedStream::getName();
$settings['connection_settings']['all']['proxy_extra'] = [
    [
        'address'  => '96.44.183.149',
        'port'     =>  55225,
    ],
    [
        'address'  => '96.44.133.110',
        'port'     =>  58690,
    ],
    [
        'address'  => '50.62.59.61',
        'port'     =>  55625,
    ],
    [
        'address'  => '208.97.31.229',
        'port'     =>  53124,
    ],
    [
        'address'  => '66.110.216.105',
        'port'     =>  39431,
    ],
];

include 'login_helper.php'; // may not start without this
$MadelineProto = new \danog\MadelineProto\API('sessions/bot.session', $settings);
$MadelineProto->async(true);
$MadelineProto->loop(function () use ($MadelineProto) {
    yield $MadelineProto->start();
    yield $MadelineProto->setEventHandler('\EventHandler');
});
$MadelineProto->loop();
