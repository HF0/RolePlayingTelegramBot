<?php
namespace RolBot\Commands;

use RolBot\Commands\InFightCommand;
use Longman\TelegramBot\Request;

/**
 * There must be an active and not finished fight
 */
abstract class NotFinishedFightCommand extends InFightCommand
{
    protected function preExecuteRolBot()
    {
        $answer = parent::preExecuteRolBot();
        if (!is_null($answer)) {
            return $answer;
        }
        $fightStatus = $this->fightGroup->getFightStatus();

        if ($fightStatus->isFinished()) {
            $message = $this->getMessage();
            $error_message = [
                'chat_id' => $message->getChat()->getId(),
                'text' => "❌ No active fight"
            ];
            return Request::sendMessage($error_message);
        }
    }
}
