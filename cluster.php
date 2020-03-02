<?php namespace AppName\bot\cluster;

yield HandleMessage("/jopa", [
    "message" => "Hello there fucking bullshit",
]);

yield HandleMessage("/jopa", function(&$data) {
    if ($data[])
    return [
        "message" => "Hello there fucking bullshit",
    ]
});

$BOT->handle->message("/jopa", function (&$data) {
    
});

?>