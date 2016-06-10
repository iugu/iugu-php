<?php

if (!function_exists('get_called_class')) {
    class class_tools
    {
        public static $i = 0;
        public static $fl = null;

        public static function get_called_class()
        {
            $bt = debug_backtrace();

            if (self::$fl == $bt[2]['file'].$bt[2]['line']) {
                self::$i++;
            } else {
                self::$i = 0;
                self::$fl = $bt[2]['file'].$bt[2]['line'];
            }

            $lines = file($bt[2]['file']);

            preg_match_all('/([a-zA-Z0-9\_]+)::'.$bt[2]['function'].'/',
          $lines[$bt[2]['line'] - 1],
          $matches);

            return $matches[1][self::$i];
        }
    }

    function get_called_class()
    {
        return class_tools::get_called_class();
    }
}
