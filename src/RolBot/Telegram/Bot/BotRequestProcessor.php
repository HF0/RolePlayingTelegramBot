<?php
namespace RolBot\Telegram\Bot;

use Longman\TelegramBot\TelegramLog;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;
use RolBot\Config\Configuration;
use Longman\TelegramBot\Request;

class BotRequestProcessor
{
    private $telegram;
    private $botUsername;

    public function __construct($botApiKey, $botUsername)
    {
        $this->telegram = new Telegram($botApiKey, $botUsername);
        $this->botUsername = $botUsername;
    }

    public static function sendMessageToGroup($chatId, $message)
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $message
        ];
        $result = Request::sendMessage($data);
        return $result->isOk();
    }

    public function setWebHook($hookUrl)
    {
        try {
            $result = $this->telegram->setWebhook($hookUrl);
            if ($result->isOk()) {
                return $result->getDescription();
            } else {
                return "Error setting webhook";
            }
        } catch (TelegramException $e) {
            return $e->getMessage();
        }
    }

    public function unsetWebHook()
    {
        try {
            $result = $this->telegram->deleteWebhook();
            if ($result->isOk()) {
                return $result->getDescription();
            } else {
                return "Error unsetting webhook";
            }
        } catch (TelegramException $e) {
            TelegramLog::error($e);
        }
    }

    public function run()
    {
        try {
            $commandsPath = [__DIR__ . '/../../../telegramCommands/'];

            $logFolder = Configuration::get('log_folder_path');
            if (Configuration::get('enableBotErrorLog')) {
                TelegramLog::initErrorLog($logFolder . "/bot_log_error.txt");
            }
            if (Configuration::get('enableBotDebugLog')) {
                TelegramLog::initDebugLog($logFolder . "/bot_log_debug.txt");
            }
            if (Configuration::get('enableBotUpdateLog')) {
                TelegramLog::initUpdateLog($logFolder . "/bot_log_update.txt");
            }
            $this->telegram->addCommandsPaths($commandsPath);
            return $this->telegram->handle();
        } catch (TelegramException $e) {
            TelegramLog::error($e->getMessage());
            return false;
        }
    }
}