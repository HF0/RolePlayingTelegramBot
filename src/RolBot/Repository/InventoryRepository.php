<?php
namespace RolBot\Repository;

use \RedBeanPHP\R as R;
use RolBot\Entities\Item;
use RolBot\Entities\Character;

class InventoryRepository
{
	const TABLE_NAME = 'inventory';

	public function add($quantity, $item, $charactername)
	{
		$itemObj = $this->getItem($item, $charactername);
		if (!$itemObj) {
			if ($quantity > 0) {
				$this->addNew($quantity, $item, $charactername);
			}
		} else {
			$newQuantity = max(0, $itemObj->getQuantity() + $quantity);
			if ($newQuantity == 0) {
				$this->delete($item, $charactername);
			} else {
				$itemObj->setQuantity($newQuantity);
			}
		}
		return $itemObj;
	}

	public function delete($item, $charactername)
	{
		$itemObj = $this->getItem($item, $charactername);
		if ($itemObj) {
			R::trash($itemObj->getBean());
		}
	}

	public function addNew($quantity, $item, $charactername)
	{
		if (!is_integer($quantity)) {
			throw new \InvalidArgumentException("Userid must be a number");
		}
		
		if( !Character::getByName($charactername)) {
			throw new \InvalidArgumentException("Unknown character");
		}
		$table = R::dispense(self::TABLE_NAME);
		$table->quantity = $quantity;
		$table->item = $item;
		$table->charactername = $charactername;
		$id = R::store($table);
	}

	public function getItem($item, $charactername)
	{
		$res = R::findOne(self::TABLE_NAME, ' charactername = ? and item = ?', [$charactername, $item]);
		if ($res) {
			$res = new Item($res);
		}
		return $res;
	}

	public function getAll()
	{
		$data = R::findAll(self::TABLE_NAME);
		$result = [];
		foreach ($data as $item) {
			array_push($result, new Item($item));
		}
		return $result;
	}

	public function getAllFromCharacter($charactername)
	{
		// throws exception
		$character = Character::getByName($charactername);
		
		$data = R::find(self::TABLE_NAME, ' charactername = ? ', [$charactername]);
		$result = [];
		foreach ($data as $item) {
			array_push($result, new Item($item));
		}
		return $result;
	}
}
