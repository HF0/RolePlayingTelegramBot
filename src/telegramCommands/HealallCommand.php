<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Request;

use RolBot\Commands\RolCommand;
use RolBot\Entities\FightGroup;
use RolBot\Entities\Character;

class HealallCommand extends RolCommand
{
	protected $name = 'healall';
	protected $requireMaster = true;

	public function execute()
	{
		$message = $this->getMessage();
		$paramString = trim($message->getText(true));

		$data = [
			'chat_id' => $message->getChat()->getId(),
		];

		$params = explode(' ', $paramString);
		$params = array_filter($params, function ($value) {
			return $value !== '';
		});
		$params = array_values($params);
		$numParams = count($params);
		if ($numParams !== 1) {
			$data['text'] = '❌';
			return Request::sendMessage($data);
		}

		$lifeModificationString = $params[0];
		if (!ctype_digit($lifeModificationString)) {
			$data['text'] = '❌';
			return Request::sendMessage($data);
		}
		$lifeModification = (int)$lifeModificationString;

		$pjlistbean = Character::getAll();
		$result = "";
		foreach ($pjlistbean as $character) {
			$characterName = $character->name;
			$character = Character::getByName($characterName);
			$oldLife = $character->getLife();
			$newLife = min($character->getMaxLife(), $character->getLife() + $lifeModification);
			$result .= "{$characterName} 💚{$oldLife} -> {$newLife}\n";
			$character->setLife($newLife);
		}

		$data['text'] = $result;
		return Request::sendMessage($data);
	}
}
