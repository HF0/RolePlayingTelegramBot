<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Request;

use RolBot\Commands\RolCommand;
use RolBot\Entities\FightGroup;
use RolBot\Entities\Character;

class HealCommand extends RolCommand
{
	protected $name = 'heal';
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
		if ($numParams !== 2) {
			$data['text'] = '❌';
			return Request::sendMessage($data);
		}

		$characterName = $params[0];
		$lifeModificationString = $params[1];

		if (!ctype_digit($lifeModificationString)) {
			$data['text'] = '❌';
			return Request::sendMessage($data);
		}

		$lifeModification = (int)$lifeModificationString;
		$lifeModified = false;
		if (Character::exists($characterName)) {
			$character = Character::getByName($characterName);
			$oldLife = $character->getLife();
			$newLife = min($character->getMaxLife(), $character->getLife() + $lifeModification);
			$character->setLife($newLife);
			$lifeModified = true;
		} else {
			$fightGroup = FightGroup::createFromEnabledFightGroup();
			if ($fightGroup) {
				$npcFighter = $fightGroup->getNpcFighter($characterName);
				if ($npcFighter) {
					$oldLife = $npcFighter->getLife();
					$newLife = min($npcFighter->getMaxLife(), $npcFighter->getLife() + $lifeModification);
					$npcFighter->setLife($newLife);
				}
			}
			$lifeModified = $fightGroup && $npcFighter;
		}

		if (!$lifeModified) {
			$data['text'] = '❌';
			return Request::sendMessage($data);
		}
		$data['text'] = "{$character_name} 💚{$oldLife} -> {$newLife}";
		return Request::sendMessage($data);
	}
}
