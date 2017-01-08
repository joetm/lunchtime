<?php

namespace LunchTime;


class Config
{
	private static $config = null;

	/**
	 * Configuration class
	 *
     * @throws \Exception 			If configuration could not be loaded from config.json
	 */
	public static function get($key) {
		self::load();
		// if key does not exist
		if (!self::$config[$key]) {
			throw new \Exception("Config key " . $key . " not found", 1);
		}
		// key exists
		return self::$config[$key];
	}

	public static function load() {
		if (!self::$config) {
			self::$config = \Noodlehaus\Config::load('config.json');
		}
	}

}
