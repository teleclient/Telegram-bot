<?php namespace AppName\traits;

function load()
{
    foreach (scandir(__DIR__) as $file) {
        if ($file == basename(__FILE__) || in_array($file, [".", ".."])) continue;
        print $file.PHP_EOL;
        require $file;
    }
}

?>