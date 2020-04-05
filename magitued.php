<?php

include_once "useful.php";

new tyre("start", function () {
    console("> Magitued is working...");
    include "start.php";
});

tyre::begin(function () {
    console("> Magitued Project thanks you for using our Open Source application. Please, enjoy it!")->paint("LIGHTGREEN");
});

?>