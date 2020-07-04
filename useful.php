<?php

ini_set('error_display', 1);
error_reporting(E_ALL);

define("Magitued_BOT_DIR", __DIR__."/bot");

define("off", false);
define("on", true);

shell_exec("alias Magitued='php magitued.php'");

function Logger(string $file_name = "Logger.txt") {
    $file_handler = fopen($file_name, "a");
    while (true) {
        $yield = yield;
        fwrite($file_handler, $yield . "\n");
        // console("My message is " . $yield)->paint("BLACK", "LIGHTGRAY");
        // usleep(1000000);
    }
    fclose($file_handler);
}

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
        $this->logger = Logger();
        $this->logger->send($text);
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

    public function lnlog($text = false)
    {
        if ($text) print(PHP_EOL.$text.PHP_EOL); else print(PHP_EOL.$this->print.PHP_EOL);
    }
}

function console(string $text) : object
{
    // $Logger->send($text);
    return new log($text);
}


class useful
{
    private static $included = false;
    private static $geo;
    public static $proceedRail;
    public static $TorFree = false;
    public static $settings = [
        "notice" => on,
    ];
    public $print;
    public function __construct()
    {
        self::$included = true;
        console("Hello, I'm constructed and ready to help you because I'm the most useful class ever!")
        ->paint("BLACK", "LIGHTGRAY");
    }
    
    public static function date_to_words(string $date, $lang = null, $letters = null) {
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
    public static function setUp(array $settings) : void
    {
        self::$settings = &$settings;
    }

    public static function getGeo() : string
    {
        console("> Magitued's checking your location...")->logln();
        exec("curl 'https://ipinfo.io/ip' 2>&1", $ip);
        exec("curl 'https://ipvigilante.com/{$ip[3]}' 2>&1", $geo);
        $geo = json_decode($geo[3]);
        if (empty($geo)) {
            exec("curl 'http://api.ipstack.com/{$ip[3]}?access_key=85a5ab5f21fe531734747eaf732a6ee3' 2>&1", $geo);
            $data = json_decode($geo[3]);    
        } else $data = $geo->data;
        console($data->country_name)->log();
        return self::$geo = $data->country_name;
    }
    public static function proceed(string $file) : void
    {
        $step = [
            0 => false,
            1 => true,
            2 => "Trying to connect to Telegram servers...",
            3 => "Successfully connected to Telegram servers.",
            4 => "Magitued Bot has been started!",
            "Blocked" => "As your server is whitin the Telegram-Blocked area, establishing connection through a proxy...",
            "Connected" => "Proxy connection esablished.",
        ][self::$proceedRail];
        while ($step) {
            // Clearing states
            self::$proceedRail = 1;
            usleep(100000);
            // Get size of $file
            $file_size = filesize($file);
            // ifs 
            //if (self::$geo) {
                //if ($file_size > 0) self::$proceedRail = "Blocked";
                // if ($file_size > 1200) self::$proceedRail = "Connected";
            //}
            // if ($file_size > 1400) self::$proceedRail = 2;
            // if ($file_size > 5000) self::$proceedRail = 3;
            // if ($file_size > 20000) self::$proceedRail = 4;
            // if ($step !== true) console($step)->log();
            // if ($file_size > 20000) self::$proceedRail = 0;
        }
    }
}

class promise
{
    private $thenObject;
    public $status;
    public function __construct(object $then = null) {
        $this->status = "pending";
        $this->thenObject = $then != null ? $then : function () {};
    }

    public function resolve() : void
    {
        $this->status = "resolved";
        $this->thenObject->__invoke();
    }

    public function then(object $then) : void
    {
        $this->thenObject = $then;
    }
}


class tyre {
    static private $BidsArray = [];
    public function __construct(string $name, object $execution = null) {
        self::$BidsArray[$name] = $execution;
    }
    static public function begin(object $startFunction = null) : void
    {
        $startFunction->__invoke();
        foreach ($GLOBALS["argv"] as $index => $name) {
            if ($index == 0) {
                console("> Magitued has arranged " . $name)->paint("LIGHTGREEN");
                continue;
            }
            if (isset(self::$BidsArray[$name])) {
                self::$BidsArray[$name]->__invoke();
            }
        }
    }
}