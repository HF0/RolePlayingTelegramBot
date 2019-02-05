<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use RolBot\Commands\RolCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;

use RolBot\Utils\Dice;
use RolBot\Repository\InventoryRepository;
use RolBot\Repository\TelegramUserRepository;

class InventoryCommand extends RolCommand
{
	protected $name = 'inventory';

	public function execute()
	{
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$from = $message->getFrom();
		$id = $from->getId();
		$text = trim($message->getText(true));

		try {
			$parameters = explode(' ', $text, 4);
			$numParameters = count($parameters);
			if ($numParameters == 1) {
				$character = $parameters[0];
				$inventory = new InventoryRepository();
				$items = $inventory->getAllFromCharacter($character);
				if (count($items) == 0) {
					$text = 'No items';
				} else {
					$text = "INVENTORY\n";
					foreach ($items as $item) {
						$text .= "{$item->getQuantity()}:  {$item->getItem()}\n";
					}
				}
			} else if ($numParameters == 4) {
				if (!$this->telegramUserRepository->isMaster($id)) {
					throw new \InvalidArgumentException();
				}

				$op = $parameters[0];
				$characterName = $parameters[1];
				$quantity = intval($parameters[2]);
				$item = $parameters[3];

				if ($quantity <= 0) {
					throw new \InvalidArgumentException();
				}
				if ($op == 'add') {
					$inventory = new InventoryRepository();
					$inventory->add($quantity, $item, $characterName);
					$text = 'Ok';
				} else if ($op == 'remove') {
					$inventory = new InventoryRepository();
					$inventory->add(-1 * $quantity, $item, $characterName);
					$text = 'Ok';
				} else {
					throw new \InvalidArgumentException();
				}
			} else {
				throw new \InvalidArgumentException();
			}

		} catch (\Exception $e) {
			$text = "❌";
		}
		$data = [
			'chat_id' => $chat_id,
			'text' => $text,
		];
		return Request::sendMessage($data);
	}
}
