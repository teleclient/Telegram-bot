<?php

include "useful.php";

new tyre("start", function () {
    include "start.php";
});

tyre::begin(function () {
    console("> Magitued Project thanks you for using our Open Source application. Please, enjoy it!")->paint("LIGHTGREEN");
});

?>