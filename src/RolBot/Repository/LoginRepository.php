<?php
namespace RolBot\Repository;

use \RedBeanPHP\R as R;
use RolBot\Config\Configuration;

class LoginRepository
{
	const USER_SESSION_PROPERTY_NAME = 'USER';

	public function loginOk($user, $password)
	{
		$correctUser = Configuration::get('admin_user');
		$correctPassword = Configuration::get('admin_password');
		return !is_null($correctUser) && !is_null($correctPassword) &&
			strcasecmp($user, $correctUser) === 0 &&
			strcasecmp($password, $correctPassword) === 0;
	}

	public function authorize($user, $password)
	{
		$login_ok = $this->loginOk($user, $password);
		$result = array("ok" => $login_ok);
		$_SESSION[self::USER_SESSION_PROPERTY_NAME] = "AdminUser";
		return $result;
	}

	public function logout()
	{
		if ($this->isLoggedIn()) {
			unset($_SESSION[self::USER_SESSION_PROPERTY_NAME]);
		}
		$result = array("ok" => true);
		return $result;
	}

	public static function isLoggedIn()
	{
		return isset($_SESSION[self::USER_SESSION_PROPERTY_NAME]);
	}

}
