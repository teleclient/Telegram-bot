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
    protected $files;
    public function __construct(string $folder = "strings") {
        self::folder = $folder;
        self::readDirectory($folder);
    }
    private static function readJSON($file): object
    {
        $data = json_decode($file);
        self::files[] = $data;
        return $data;
    }
    private static function readDirectory($folder)
    {
        $files_in_dir = scandir($folder);
        foreach ($files_in_dir as $file) {
            if ($file == explode(".", $file)[1]) {
                self::readJSON(self::folder."/".$file);
            }
        }
    }
    static function cat() : object
    {
        # code...
    }
}


?>