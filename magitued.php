<?php

include_once "useful.php";

new tyre("fuck", function () {
    print useful::$TorFree;
});

new tyre("--without-tor", function () {
    useful::$TorFree = true;
});

new tyre("--shit", function () {
    print useful::$TorFree;
});

new tyre("start", function () {
    console("> Magitued is working...");
    include "start.php";
});

tyre::begin(function () {
    console("> Magitued Project thanks you for using our Open Source application. Please, enjoy it!")->paint("LIGHTGREEN");
});

?>