<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Request;
use RolBot\Commands\RolCommand;
use RolBot\Entities\FightGroup;
use RolBot\Telegram\Mappers\FighterMapper;
use RolBot\Entities\Character;

class PlayerCommand extends RolCommand
{
    protected $name = 'player';

    public function execute()
    {
        $message = $this->getMessage();
        $characterName = trim($message->getText(true));

        $data = [
            'chat_id' => $message->getChat()->getId(),
            'parse_mode' => 'MARKDOWN'
        ];
        if (!$characterName) {
            $pjlistbean = Character::getAll();
            $data['text'] = "CHARACTERS\n";
            $data['text'] .= FighterMapper::pjlistArrayToString($pjlistbean, true);

            try {
                $fightGroup = FightGroup::createFromEnabledFightGroup();
                $data['text'] .= "\n\nNPCs ({$fightGroup->getName()})\n";
                $npcs = $fightGroup->getAllNpc();
                $data['text'] .= FighterMapper::fighterListToString($npcs, true, true);
            } catch (\InvalidArgumentException $e) {
                $data['text'] .= "NPCs\n";
                $data['text'] .= "No active fight\n";
            }
        } else {
            $character = Character::getByName($characterName);
            if ($character) {
                $data['text'] = FighterMapper::characterToString($character);
            } else {
                $data['text'] = '❌';
            }
        }
        return Request::sendMessage($data);
    }

}
