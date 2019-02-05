<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;

use RolBot\Commands\RolCommand;
use RolBot\Utils\Dice;

class DiceCommand extends RolCommand
{
	protected $name = 'dice';

	public function execute()
	{
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$text = trim($message->getText(true));

		if ($text === '') {
			$keyboard = new Keyboard(
				['/dice 1d2', '/dice 1d6', '/dice 1d10'],
				['/dice 1d20', '/dice 1d100']
			);

			$keyboard
				->setResizeKeyboard(true)
				->setOneTimeKeyboard(true)
				->setSelective(false);


			$data = [
				'chat_id' => $chat_id,
				'text' => 'Dice:',
				'reply_markup' => $keyboard,
			];

			return Request::sendMessage($data);
		}

		// parse dado
		// TOFIX: parse result
		$result = "NONE";
		if ($text == '1d2') {
			$result = $text . "=🎲" . rand(1, 2);
		} else if ($text == '1d6') {
			$result = $text . "=🎲" . rand(1, 6);
		} else if ($text == '1d10') {
			$result = $text . "=🎲" . rand(1, 10);
		} else if ($text == '1d20') {
			$result = Dice::d20DiceResultString();
		} else if ($text == '1d100') {
			$result = $text . "=🎲" . rand(1, 100);
		}

		$data = [
			'chat_id' => $chat_id,
			'text' => $result,
			'reply_markup' => Keyboard::remove()
		];

		return Request::sendMessage($data);

	}
}
