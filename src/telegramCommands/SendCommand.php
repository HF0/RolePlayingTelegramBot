<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Request;
use RolBot\Commands\RolCommand;
use RolBot\Telegram\Bot\BotRequestProcessor;
use RolBot\Config\Configuration;

class SendCommand extends RolCommand
{
    protected $name = 'send';
    protected $requireMaster = true;
    
    public function execute()
    {
        $message = $this->getMessage();
        $text = trim($message->getText(true));
        $chatId = Configuration::get('group_id');
        return BotRequestProcessor::sendMessageToGroup($chatId, $text);
    }
}
