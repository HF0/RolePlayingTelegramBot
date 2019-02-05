<?php
namespace RolBot\Config;

use RolBot\Config\Configuration;
use \RedBeanPHP\R as R;

class Db
{
	public static function setup($host, $dbName, $user, $password)
	{
		$connectionString = "mysql:host={$host};dbname={$dbName}";
		R::setup($connectionString, $user, $password);
		R::setAutoResolve(true);        //Recommended as of version 4.2
        // comment to allow changes in the database
		R::freeze(true);
	}
}
