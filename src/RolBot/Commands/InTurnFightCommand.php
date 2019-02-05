<?php
namespace RolBot\Commands;

use Longman\TelegramBot\Request;

/**
 * Only admin and users in their turn can execute the command successfuly
 */
abstract class InTurnFightCommand extends NotFinishedFightCommand
{
    protected function preExecuteRolBot()
    {
        $answer = parent::preExecuteRolBot();
        if (!is_null($answer)) {
            return $answer;
        }

        $message = $this->getMessage();
        $from = $this->getMessage()->getFrom();
        $id = $from->getId();

        $is_user_turn = $this->isUserTurn($id);
        if (!$is_user_turn) {
            $message = $this->getMessage();
            $error_message = [
                'chat_id' => $message->getChat()->getId(),
                'text' => "❌ Not your turn..."
            ];
            return Request::sendMessage($error_message);
        }
    }

    private function isUserTurn($telegramid)
    {
        $next = $this->fightGroup->getNextPlayer();
        $real_round_user_name = $next->getControl();
        $is_user_turn = $this->telegramUserRepository->useridExistsWithName($telegramid, $real_round_user_name) ||
            $this->telegramUserRepository->isMaster($telegramid);
        return $is_user_turn;
    }
}
