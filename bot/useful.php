<?php

ini_set('error_display', 1);
error_reporting(E_ALL);

difine("APPNAME_BOT_DIR", "/home/koto/Telegram/bot/");

/**
     * Pretty colourful version of print, prints text into console
     * 
     * 1st arg is background;
     * 
     * 2nd arg is foreground;
     * 
     * e.g: BLACK+WHITE
     * 
     * Background colours: Black, Red, GREEN, YELLOW, BLUE, MAGENTA, CYAN, LIGHTGRAY
     * 
     * foreground colours: Black, DARKGRAY, RED, LIGHTRED, GREEN, LIGHTGREEN, BROWN, YELLOW, BLUE, LIGHTBLUE, MAGENTA, LIGHTMAGENTA, CAYN, LIGHTCYAN, LIGHTGRAY, WHITE
     */
class log
{
    public $string;
    public $print;
    private $settings = [
        0 => [
            'where' => 'foreground',
            'default' => "1;32;",
        ],
        1 => [
            'where' => 'background',
            'default' => "48m",
        ],
    ];
    private $foreground = [
        'BLACK' => "0;30;",
        'DARKGREY' => "1;30;",
        'RED' => "0;31;",
        'LIGHTRED' => "0;31;",
        'GREEN' => "0;32;",
        'LIGHTGREEN' => "1;32;",
        'BROWN' => "0;33;",
        'YELLOW' => "1;33;",
        'BLUE' => "0;34;",
        'LIGHTBLUE' => "1;34;",
        'MAGENTA' => "0;35;",
        'LIGHTMAGENTA' => "1;35;",
        'CYAN' => "0;36;",
        'LIGHTCYAN' => "1;36;",
        'LIGHTGRAY' => "0;37;",
        'WHITE' => "1;37;",
    ];
    private $background = [
        'BLACK' => "40m",
        'RED' => "41m",
        'GREEN' => "42m",
        'YELLOW' => "43m",
        'BLUE' => "44m",
        'MAGENTA' => "45m",
        'CYAN' => "46m",
        'LIGHTGRAY' => "47m",
    ];

    public function __construct($text, $echo = false)
    {
        $this->string = &$text;
        $this->paint();
    }

    public function formatColours(array $args)
    {
        foreach ($this->settings as $digit => $surface) {
            if (isset($args[$digit]))
                if (isset(($this->{$surface['where']})[$args[$digit]])) {
                    $colours[$surface['where']] = ($this->{$surface['where']})[$args[$digit]];
                    continue;
                }
            $colours[$surface['where']] = $surface['default'];
        }

        $colours['_'] = "\e[".$colours['foreground'].$colours['background']; // uncompleted string
        return $colours;
    }

    public function paint(string $background = null, string $foreground = null, $inline = false)
    {
        $colours = $this->formatColours([$background, $foreground]);
        $this->print = $colours['_']." ".$this->string." "."\e[0m";
        if (isset($foreground) || isset($background))
            if ($inline) $this->logln(); else $this->log();
        else return $this->print;
    }

    public function log($text = false)
    {
        if ($text) print($text.PHP_EOL); else print($this->print.PHP_EOL);
    }

    public function logln($text = false)
    {
        if ($text) print($text); else print($this->print);
    }
}
/**
 * This function resembles JavaScript function 'console'
 */
function console(string $text) : object
{
    return new log($text);
}


class useful
{
    public $print;
    public function __construct()
    {
        console("Hello, I'm constructed and ready to help you because I'm the most useful class ever!")
        ->paint("BLACK", "LIGHTGRAY");
    }
    
    public function date_to_words(string $date, $lang = null, $letters = null) {
        $date = date_parse_from_format("Y-m-d", $date);
        $month = [
            "1" => "Января",
            "2" => "Февраля",
            "3" => "Марта",
            "4" => "Апреля",
            "5" => "Мая",
            "6" => "Июня",
            "7" => "Июля",
            "8" => "Августа",
            "9" => "Сентября",
            "10" => "Октября",
            "11" => "Ноября",
            "12" => "Декабря",
        ][$date["month"]];
        
        return [
            "_" => "{$date["day"]}-{$month}-{$date["year"]}",
            "day" => $date["day"],
            "month" => $month,
            "year" => $date["year"],
        ];
    }
}

$useful = new useful();

class DataBase
{
    public $useful;
    public $config;
    private $DataBase;
    private $config_file = "database.json"; // It's niether a straight path to file or just a file
    private $ping_loops = 0;
    
    /**
     * @param object|array|null $mysqli 
     */
    public function __construct($mysqli = null)
    {
        switch (gettype($mysqli)) {
            case "object":
                $this->DataBase = &$mysqli;
                break;
            case "array":
                $this->setSettings($mysqli);
                break;
            default:
                print_r($this->getSettings());
                break;
        }
    }

    public function getSettings() : array
    {
        if (file_exists($this->config_file)) {
            $file = file_get_contents($this->config_file);
            $config = json_decode($file, true);
        } else throw new Exception("Error: config file doesn't exist in '{$this->config_file}'");
        console("DataBase settings were successfully recieved")->paint("WHITE", "GREEN");
        return $this->config = $config;
    }

    public function setSettings(array $settings) : void
    {
        $this->config = $settings;
        $data = json_encode($settings, JSON_PRETTY_PRINT);
        if (file_put_contents($this->config_file, $data)) console("DataBase settings were successfully set up")->paint("WHITE", "GREEN");
    }

    public function connect()
    {
        try {
            $this->DataBase = new mysqli($this->config['hostname'], $this->config['username'], $this->config['password'], $this->config['database']);
            if ($this->DataBase->connect_errno) {
                $error = "Failed to connect to MySQL: (" . $this->DataBase->connect_errno . ") " . $this->DataBase->connect_error;
                console($error)->paint("WHITE", "RED");
            }
        } catch (\Exception $e) {
            console($e->getMessage())->paint("WHITE", "RED");
        }
    }

    public function reconnect()
    {
        if (!$this->DataBase || $this->DataBase->close()) {
            console("Reconnection...")->paint("BLACK", "LIGHTGRAY", true);
            $this->connect();
            print "     ";
            if (!$this->DataBase->connect_errno) console("OK")->paint("BLACK", "LIGHTGRAY");
        }
        else return false;
    }

    public function query($query, $close = false)
    {
        try {
            if (!$this->DataBase) $this->ping();
            if ($query != false) $result = $this->DataBase->query($query);
            if (isset($this->DataBase->errno) && $this->DataBase->errno) {
                throw new Exception("Failed to send a query to MySQL: (" . $this->DataBase->errno . ") " . $this->DataBase->error);
            }
            if ($close) $this->DataBase->close(); else $this->ping();
            if (isset($result)) return $result; else return false;
        } catch (\Exception $e) {
            console($e->getMessage())->paint("WHITE", "RED");
        }
    }
    
    public function ping(int $loops = 1000)
    {
        if ($this->DataBase && @$this->DataBase->ping()) {
            if ($this->ping_loops % 5 == 0) console("Connection pinged - OK")->paint("WHITE", "CYAN");
            if ($this->ping_loops > $loops) {
                $this->reconnect();
                $this->ping_loops = 0;
            }
            $this->ping_loops++;
            return true;
        } else {
            console("Connection pinged - ERROR: ".$this->DataBase->error)->paint("WHITE", "RED");
            $this->connect(); // here is not 'reconnect' because it is already disconnected
            return false;
        }
    }
}

?>