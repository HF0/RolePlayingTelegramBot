<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Request;

use RolBot\Commands\RolCommand;
use RolBot\Commands\InFightCommand;

class FightCommand extends InFightCommand
{
    protected $name = 'fight';

    public function execute()
    {
        $message = $this->getMessage();
        $param = trim($message->getText(true));

        $data = [
            'chat_id' => $message->getChat()->getId(),
            'parse_mode' => 'MARKDOWN',
        ];

        $data['text'] = "Name: {$this->fightGroup->getName()} \n";
        $data['text'] .= "Description: {$this->fightGroup->getDescription()} \n";
        $data['text'] .= "Round: {$this->fightGroup->getRound()}\n";
        $fightStatus = $this->fightGroup->getFightStatus();
        if ($fightStatus->isFinished()) {
            $data['text'] .= "Status: FINISHED\n";
            $data['text'] .= "Result: *{$fightStatus->getDescription()}*\n";
        } else {
            $next = $this->fightGroup->getNextPlayer();
            $data['text'] .= "Turn: {$next->getName()} ({$next->getControl()})\n";
        }
        return Request::sendMessage($data);
    }

}
