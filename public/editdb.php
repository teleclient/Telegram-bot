<?php

include "useful.php";

$database = new DataBase();
$database->connect();

if (isset($_POST['edit_question']) && $_POST['edit_question']) {
    print $question = $_POST['question'];
    print $desc = $_POST['description'];
    print $answers = $_POST['answers'];

    if (isset(parse_url($_POST['picture'])['scheme'])) {
        $picture_name = basename($_POST['picture']);
        exec('curl https://api.tinify.com/shrink --user api:2YwwXfSQDJDYRxVfFlvjmZkbj9F0l3lb --header "Content-Type: application/json" \
--data \'{"source": {"url": "'.$_POST['picture'].'"} }\' ', $data);

        $picture = file_put_contents("/root/Telegram/photos/".$picture_name, file_get_contents(json_decode($data[0])->output->url));

        $picture_url = "/root/Telegram/photos/".$picture_name;
        $pic = ", picture = '$picture_url'";
    } else $pic = null;

    $database->query("UPDATE questions SET qtitle = '$question', qtext = '$desc', answers = '$answers'$pic WHERE id = '3';");
}

if (isset($_POST['edit_info']) && $_POST['edit_info']) {
    print $info_inCorrect = $_POST['info_inCorrect'];
    print $info_correct = $_POST['info_correct'];
    print $button_again = $_POST['button_again'];

    print $start_before = $_POST['start_before'];
    print $start_after = $_POST['start_after'];

    $database->query("UPDATE info SET content = '$info_inCorrect' WHERE name = 'info_inCorrect';");
    $database->query("UPDATE info SET content = '$info_correct' WHERE name = 'info_correct';");
    $database->query("UPDATE info SET content = '$button_again' WHERE name = 'button_again';");

    $database->query("UPDATE info SET content = '$start_before' WHERE name = 'start_before';");
    $database->query("UPDATE info SET content = '$start_after' WHERE name = 'start_after';");
}

?>