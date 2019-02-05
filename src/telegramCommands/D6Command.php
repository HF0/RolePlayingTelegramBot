<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Request;

use RolBot\Commands\RolCommand;
use RolBot\Utils\Dice;

class D6Command extends RolCommand
{
	protected $name = 'd6';

	public function execute()
	{
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$result = Dice::diceResultString(6);
		$name = $message->getFrom()->getFirstName();
		$result = $result ;
		$data = [
			'chat_id' => $chat_id,
			'text' => $name . ' ' .$result,
		];
		return Request::sendMessage($data);
	}
}
