<?php
namespace RolBot\Repository;

use \RedBeanPHP\R as R;
use RolBot\Entities\Character;

class TelegramUserRepository
{
	const TABLE_NAME = 'telegramuser';

	public function isRegisteredUserFromId($userid)
	{
		$res = R::count(self::TABLE_NAME, ' userid = ? ', array($userid));
		return $res > 0;
	}

	public function isRegisteredUserFromName($name)
	{
		$res = R::count(self::TABLE_NAME, ' name = ? ', array($name));
		return $res > 0;
	}

	public function isMasterFromUserIdString($useridString)
	{
		if (!is_string($useridString) || !ctype_digit($useridString)) {
			return false;
		}
		$userid = (int)$useridString;
		return $this->isMaster($userid);
	}

	public function useridExistsWithName($userid, $name)
	{
		$res = R::count(
			self::TABLE_NAME,
			' userid = ? and name = ?',
			array($userid, $name)
		);
		return $res > 0;
	}

	public function isMaster($userid)
	{
		$users = $this->getByUserId($userid);
		foreach ($users as $user) {
			if (boolval($user->ismaster)) {
				return true;
			}
		}
		return false;
	}

	public function getall()
	{
		$data = R::getAll('SELECT * FROM ' . self::TABLE_NAME);
		foreach ($data as &$user) {
			$user['ismaster'] = boolval($user['ismaster']);
		}
		return $data;
	}

	// non unique vlaue
	public function getByUserId($userid)
	{
		$res = R::findAll(self::TABLE_NAME, ' userid = ? ', array($userid));
		return $res;
	}

	// unique value
	public function getByName($name)
	{
		$res = R::findOne(self::TABLE_NAME, ' name = ? ', array($name));
		return $res;
	}

	public function userUsed($user)
	{
		$countCharacter = R::count(Character::CHARACTER_TABLE, ' control = ? ', [$user]);
		return $countCharacter > 0;
	}

	public function delete($name)
	{
		if ($this->userUsed($name)) {
			throw new \InvalidArgumentException("User is used");
		}
		$user = $this->getByName($name);
		if ($user) {
			R::trash($user);
		}
		return true;
	}

	public function add($userid, $name, $isMaster, $description)
	{
		if (!is_integer($userid)) {
			throw new \InvalidArgumentException("Userid must be a number");
		}
		if ($this->isRegisteredUserFromName($name)) {
			throw new \InvalidArgumentException("User already exists");
		}
		$table = R::dispense(self::TABLE_NAME);
		$table->userid = $userid;
		$table->ismaster = $isMaster;
		$table->name = $name;
		$table->description = $description;
		$id = R::store($table);
	}

}
