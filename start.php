<?php

include_once 'useful.php';

// $status = strstr(shell_exec("service tor status"), "is running") == true;

// if ($status || useful::$TorFree) {
    // if (in_array(useful::getGeo(), ["Russia"])) {
        // console("> As your server is whitin the Telegram-Blocked area, establishing connection through a proxy...")->log();
        // shell_exec('proxychains php -r "include \'bot/launcher.php\';" > bot.log 2>&1 &');
    // } else 
    include 'bot/launcher.php';
    // console("> Magitued started successfully, when the bot has started, it will send the message =)")->lnlog();
// } else echo "Tor isn't running";

$Logger->send(123);
$Logger->send("stop");
$Logger->send("stop");
$Logger->send("anal");


$Logger->send("stop");
