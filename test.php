<?php

include "useful.php";
include "bot/classes/database.php";

$db = new \AppName\abilities\DataBase("bot/database.json");
print_r($db);

?>