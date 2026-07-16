<?php

namespace Core;

class Config
{
    private static $config = [];

    public static function load($file)
    {
        if (file_exists($file)) {
            self::$config = require $file;
        }
    }

    public static function get($key, $default = null)
    {
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public static function set($key, $value)
    {
        $keys = explode('.', $key);
        $config = &self::$config;

        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }

    public static function all()
    {
        return self::$config;
    }
}
