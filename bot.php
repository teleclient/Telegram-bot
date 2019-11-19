<?php namespace AppName\bot;

function cluster($handleMessage) {
    yield $handleMessage("/jopa");
};


include 'bot/launcher.php';

?>