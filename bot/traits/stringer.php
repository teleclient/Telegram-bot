<?php namespace AppName\traits;

/**
 * Stringer simplyfize data maintaining
 * 
 * Enter the path to folder where there are files connected to string has call-name and defined for.
 * 
 * Each file should be in JSON fromat.
 */
trait stringer
{
    private $folder;
    private $files;
    public function __construct(string $folder = "strings") {
        self::folder = $folder;
        self::readDirectory($folder);
    }
    private static function readJSON($file_name, $isArray = false)
    {
        $file_path = self::folder . "/" . $file_name . ".json";
        $file_data = json_decode($file_path . ".json", $isArray);
        if (!$isArray) self::files[$file_name] = $file_data;
        return $file_data;
    }
    private static function readDirectory($folder)
    {
        $files_in_dir = scandir($folder);
        foreach ($files_in_dir as $file_name) {
            $file = explode(".", $file_name);
            if ($file[1] == "json") {
                self::readJSON($file_name);
            }
        }
    }
    static function cat($which) : object
    {
        return self::files[$which];
    }
    static function alter($file, array $changes)
    {
        $reads = self::readJSON($file, true);
        $changes = array_merge($reads, $changes);

        $file_path = self::folder . "/" . $file . ".json";
        file_put_contents($file_path, $changes);
    }
}


?>