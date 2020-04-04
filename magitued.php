<?php

include "useful.php";


console("")->log();
console("Magitued")->log();
console("")->log();

$tyre = new tyre(6);
$tyre->addBid("start", function () {
    include "start.php";
});


?>