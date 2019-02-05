<?php
namespace RolBot\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use RolBot\Entities\FightGroup;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * There must be an active fight group (it does not matter if it has already finished)
 */
abstract class InFightCommand extends RolCommand
{
    protected $fightGroup;

    protected function preExecuteRolBot()
    {
        $answer = parent::preExecuteRolBot();
        if (!is_null($answer)) {
            return $answer;
        }

        try {
            $this->fightGroup = FightGroup::createFromEnabledFightGroup();
        } catch (\InvalidArgumentException $e) {
            $message = $this->getMessage();
            $error_message = [
                'chat_id' => $message->getChat()->getId(),
                'text' => "❌" . $e->getMessage()
            ];
            return Request::sendMessage($error_message);
        }
    }
}
