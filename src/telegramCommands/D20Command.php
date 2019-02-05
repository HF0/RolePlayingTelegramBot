<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Request;

use RolBot\Commands\RolCommand;
use RolBot\Utils\Dice;

class D20Command extends RolCommand
{
	protected $name = 'd20';

	public function execute()
	{
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$result = Dice::d20DiceResultString();
		$name = $message->getFrom()->getFirstName();
		$result = $result ;
		$data = [
			'chat_id' => $chat_id,
			'text' => $name . ' ' .$result,
		];
		return Request::sendMessage($data);
	}
}
