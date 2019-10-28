<?php

$isIt = function ($it) use ($argv) {
    unset($argv[0]);
    $args = $argv;
    foreach ($args as $index => $command) {
        if ($command == "--".$it) {
            return [
                '_' => true,
                'command' => isset($args[$index+1]) && !strstr($args[$index+1], "-") ? $args[$index+1]:null,
            ];
        }
    }
};

include '/var/www/html/useful.php';

$database = new DataBase("/var/www/html/database.json");
$database->connect();

if ($isIt("users")) {
    $result = $database->query("SELECT * FROM users");
    while ($row = $result->fetch_assoc()) {
        console($row['first_name']." (".$row['phone'].") => ")
            -> paint("BLACK", "LIGHTGRAY", true);
            
        console($row['user']." ({$row['extra']})")
            -> paint("BLACK", "LIGHTGRAY");
        
        print PHP_EOL;
    }
}

if ($isIt("op")) {
    $result = $database->query("UPDATE users set extra = 'opped' WHERE user = '{$isIt("op")['command']}'");
    if ($result) {
        console("Права успешно ввыданы!")
        -> paint("WHITE", "GREEN");
    } else {
        console("Права не были ввыданы!")
        -> paint("WHITE", "RED");
    }
}

if ($isIt("deop")) {
    $result = $database->query("UPDATE users set extra = NULL WHERE user = '{$isIt("deop")['command']}'");
    if ($result) {
        console("Права успешно сняты!")
        -> paint("WHITE", "GREEN");
    } else {
        console("Права не были сняты!")
        -> paint("WHITE", "RED");
    }
}

?>