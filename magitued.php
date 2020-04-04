<?php

include "useful.php";

$tyre = new tyre();
$tyre->addBid("start", function () {
    include "start.php";
});


$await = function () {
    sleep(5);
};

$promise = new promise();
$promise->then(function () {
    //print 123;
});

$pid = pcntl_fork();
if ($pid == -1) {
     die('could not fork');
} else if ($pid) {
     // we are the parent
     pcntl_wait($status); // Protect against Zombie children
} else {
     // we are the child
    sleep(5);
     $promise->resolve();
}

print $promise->status;

?>