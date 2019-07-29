<?php

namespace App\Console\Commands\Src;

class Path
{

    /**
     * The path where views exists
     */
    CONST VIEWS_PATH = "resources/views/";

    /**
     * The blade file extension
     */
    CONST BLADE_EXT = ".blade.php";

    /**
     * @param string $path
     * @return mixed
     */
    public static function generate ($path)
    {
        return self::VIEWS_PATH . str_replace(".", "/", $path) . self::BLADE_EXT;
    }

    /**
     * @param string $path
     * @return mixed
     */
    protected static function removeFileName ($path)
    {
        return str_replace(strrchr($path, '/'), '', $path);
    }

    /**
     * @param string $path
     */
    public static function generateIntermediateDirectories ($path)
    {
        $path = static::removeFileName($path);
        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
}
