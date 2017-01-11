<?php

/**
 * Config class
 *
 * @package   LunchTime
 * @author    J. Oppenlaender
 * @copyright MIT
 * @link      https://github.com/joetm/lunchtime
 * @version   '2017-01'
 *
 */

namespace LunchTime;

/**
 * Configuration class
 */
final class Config
{
    /** @var array $config Configuration */
    private static $config = null;

    /**
     * Configuration class
     *
     * @param string $key Key to look up
     * @return string Configuration value
     *
     * @throws \Exception           If configuration could not be loaded from config.json
     */
    public static function get($key)
    {
        self::load();
        // if key does not exist
        if (!self::$config[$key]) {
            throw new \Exception("Config key " . $key . " not found", 1);
        }
        // key exists
        return self::$config[$key];
    }

    /**
     * Load the whole config
     */
    public static function load()
    {
        if (is_null(self::$config)) {
            self::$config = \Noodlehaus\Config::load('config.json');
        }
    }
}
