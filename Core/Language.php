<?php
namespace Core {
    class Language
    {
        private static $translations = [];
        private static $currentLocale = 'en';

        public static function init()
        {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Priority: 1. URL param, 2. Session, 3. Config default
            if (isset($_GET['lang'])) {
                self::$currentLocale = $_GET['lang'] === 'es' ? 'es' : 'en';
                $_SESSION['lang'] = self::$currentLocale;
            } elseif (isset($_SESSION['lang'])) {
                self::$currentLocale = $_SESSION['lang'];
            } else {
                self::$currentLocale = \Core\Config::get('site.language', 'en');
            }

            self::load(self::$currentLocale);
        }

        public static function load($locale)
        {
            $file = dirname(__DIR__) . "/config/lang/{$locale}.php";
            if (file_exists($file)) {
                self::$translations = require $file;
            } else {
                // Fallback to English if file doesn't exist
                $fallbackFile = dirname(__DIR__) . "/config/lang/en.php";
                if (file_exists($fallbackFile)) {
                    self::$translations = require $fallbackFile;
                }
            }
        }

        public static function get($key, $placeholders = [])
        {
            $keys = explode('.', $key);
            $value = self::$translations;

            foreach ($keys as $k) {
                if (is_array($value) && isset($value[$k])) {
                    $value = $value[$k];
                } else {
                    return $key;
                }
            }

            if (is_string($value) && $placeholders) {
                foreach ($placeholders as $placeholder => $val) {
                    $value = str_replace(':' . $placeholder, $val, $value);
                }
            }

            return $value;
        }

        public static function getLocale()
        {
            return self::$currentLocale;
        }
    }
}
namespace {
    if (!function_exists('__')) {
        function __($key, $placeholders = []) {
            return \Core\Language::get($key, $placeholders);
        }
    }
}
