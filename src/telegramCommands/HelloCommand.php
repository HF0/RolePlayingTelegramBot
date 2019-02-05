<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use RolBot\Commands\RolCommand;
use Longman\TelegramBot\Request;
use RolBot\Config\Configuration;

class HelloCommand extends RolCommand
{
    protected $name = 'hello';

    public function execute()
    {
        $message = $this->getMessage();
        $chat = $message->getChat();
        $chat_id = $chat->getId();
        $name = $message->getFrom()->getFirstName();

        $text = trim($message->getText(true));

        $greet = 'Hello ' . $name . '. I am a rol bot. ';
        $adminUrl = Configuration::get('root_url') . '/admin/';
        $greet .= "You can manage me at ${adminUrl}";
        $data = [
            'chat_id' => $chat_id,
            'text' => $greet,
        ];

        return Request::sendMessage($data);
    }
}
