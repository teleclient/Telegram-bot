<?php

function HandleMessage() {
    print $argv;
}

function generator() {
    yield 123;
    yield 123;
    yield 123;
    yield 123;
}

foreach (generator() as $value) {
    print $value . PHP_EOL;
}


?>