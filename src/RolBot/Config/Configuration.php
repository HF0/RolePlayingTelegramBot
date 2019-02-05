<?php
namespace RolBot\Config;

class Configuration
{
	protected static $config = null;

	private static function ini()
	{
		// defaults to main folder
		$dirPath = __DIR__ . '/../../../';
		static::$config = include_once($dirPath . 'config.php');
	}

	public static function get($key)
	{
		if (is_null(static::$config)) {
			static::ini();
		}

		$value = null;
		if (array_key_exists($key, static::$config)) {
			$value = static::$config[$key];
		}
		return $value;
	}
}