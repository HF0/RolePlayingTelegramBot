<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Request;
use RolBot\Commands\InTurnFightCommand;
use RolBot\Commands\PassCommandService;

class PassCommand extends InTurnFightCommand
{
    protected $name = 'pass';

    public function execute()
    {
        $message = $this->getMessage();
        $from = $message->getFrom();
        $telegramidstring = strval($from->getId());

        $passCommandService = new PassCommandService();
        $resultMessage = $passCommandService->run($this->fightGroup, $telegramidstring);
        $data = [
            'chat_id' => $message->getChat()->getId(),
            'parse_mode' => 'MARKDOWN',
            'text' => $resultMessage
        ];
        return Request::sendMessage($data);
    }

}
