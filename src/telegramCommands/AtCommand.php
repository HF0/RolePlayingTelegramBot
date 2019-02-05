<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Request;

use RolBot\Commands\InTurnFightCommand;
use RolBot\Commands\AtCommandService;

class AtCommand extends InTurnFightCommand
{
    protected $name = 'at';

    public function execute()
    {
        $message = $this->getMessage();
        $from = $message->getFrom();
        $telegramidstring = strval($from->getId());
        $targetName = trim($message->getText(true));
        if (!$targetName) {
            return Request::sendMessage($data);
        }
        $atCommandService = new AtCommandService();
        $resultMessage = $atCommandService->run($targetName, $this->fightGroup, $telegramidstring);
        $data = [
            'chat_id' => $message->getChat()->getId(),
            'text' => $resultMessage
        ];
        return Request::sendMessage($data);
    }
}
