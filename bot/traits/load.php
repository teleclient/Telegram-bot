<?php namespace AppName\traits;

function load()
{
    foreach (scandir(__FILE__) as $file) {
        include $file;
    }
}


?>