<?php

include "useful.php";

$database = new DataBase();
$database->connect();
$result = $database->query("SELECT * FROM questions WHERE id = '3'");
$default['setQuestions'] = $result->fetch_assoc();

$result = $database->query("SELECT name, content FROM info");
while ($row = $result->fetch_assoc()) {
    $default['setInfo'][$row['name']] = $row['content'];
}

class auth
{
    public function __construct($settings = false) {
        $this->settings = $settings;
        $this->username = $settings['username'];
        $this->private = "asjd08h1u982hdu";
        
        $this->auth();
    }

    public function auth()
    {
        #if ($this->settings) {
            //$exsits = $this->checkUser($settings['user']);
            if (!$this->isAuthed()) $this->setAuth($this->settings);
                else {
                    $this->username = $_COOKIE['login'];
                }
        #}
    }

    public function checkUser($user)
    {
        $exsits = $database->exists([
            "table" => "users",
            "columns" => [
                [
                    "_" => "username",
                    "value" => $user['username'],
                ],
                [
                    "_" => "password",
                    "value" => $user['password'],
                ],
            ],
        ]);

        return $exsits;
    }

    public function isAuthed()
    {
        return isset($_COOKIE['login'], $_COOKIE['auth']);
    }

    public function setAuth($user)
    {
        if (empty($this->settings)) {
            print "У вас нет прав.";
        } else {
            setcookie("login", $user['username']);
            setcookie("auth", $this->encode($user));
        }
    }

    public function encode($data)
    {
        if (!isset($this->private)) throw new Exception("Error: not recieved keys", 1);

        $string = join(":", $data);
        $key1 = md5($string);
        $key2 = md5($this->private);

        return md5($key1.$key2);
    }
}

function showTable($table, &$database)
{
    $i = 0;
    $result = $database->query("SELECT * FROM $table");
    print "<table>";
    while ($row = $result->fetch_assoc()) {
        print "<tr>";
        foreach ($row as $key => $value) {
            if ($i == 0) {
                print "<td>$key</td>";
            } else print "<td>$value</td>";
        }
        print "</tr>";
        $i++;
    }
    print "</table>";
}

$auth = new auth();

if (isset($_GET['user'], $_GET['pass'])) {
    if ($_GET['user'] == "Admin" && $_GET['pass'] == "h7d812g") {
        $auth = new auth([
            "username" => $_GET['user'],
            "password" => $_GET['pass'],
        ]);
        header('location: /');
    }
}


#showTable("questions", $database);

if ($auth->isAuthed() && isset($_GET['p'])) include "templates/".$_GET['p'].".php";
    else
if ($auth->isAuthed()) include "templates/setQuestions.php";


?>