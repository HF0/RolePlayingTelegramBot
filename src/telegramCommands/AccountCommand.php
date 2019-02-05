<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use RolBot\Commands\RolCommand;
use Longman\TelegramBot\Request;
use RolBot\Repository\TelegramUserRepository;

class AccountCommand extends RolCommand
{
    protected $name = 'account';
    protected $requireRegister = false;
    protected $requireMaster = false;

    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $text = trim($message->getText(true));

        $text = "";
        $from = $message->getFrom();
        $id = $from->getId();
        $text .= "ID " . $id . "\n";
        $text .= "Username " . $from->getUsername() . "\n";

        $repository = new TelegramUserRepository();
        $isregistered = $repository->isRegisteredUserFromId($id);
        $text .= "Registered: " . ($isregistered ? "Yes" : "No") . "\n";
        if ($isregistered) {
            $ismaster = $repository->isMaster($id);
            $text .= "Master: " . ($ismaster ? "Yes" : "No") . "\n";
        }
        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
        ];
        return Request::sendMessage($data);
    }
}
