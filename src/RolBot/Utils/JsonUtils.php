<?php
namespace RolBot\Utils;

class JsonUtils
{
	public static function getJsonProperty($data, $property_name)
	{
		return property_exists($data, $property_name) ? $data->$property_name : null;
	}
}
