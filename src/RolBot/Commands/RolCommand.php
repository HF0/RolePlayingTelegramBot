<?php
namespace RolBot\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use RolBot\Repository\TelegramUserRepository;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * The user must be registered
 */
abstract class RolCommand extends UserCommand
{
    protected $requireRegister = true;
    protected $requireMaster = false;
    protected $telegramUserRepository;

    public function __construct(Telegram $telegram, Update $update = null)
    {
        $this->telegramUserRepository = new TelegramUserRepository();
        parent::__construct($telegram, $update);
    }

    protected function isMasterCorrect()
    {
        $message = $this->getMessage();
        $from = $this->getMessage()->getFrom();
        $id = $from->getId();
        $ismaster = $this->telegramUserRepository->isMaster($id);
        return !$this->requireMaster || $ismaster;
    }

    protected function isRegisteredCorrect()
    {
        $message = $this->getMessage();
        $from = $this->getMessage()->getFrom();
        $id = $from->getId();
        $isregistered = $this->telegramUserRepository->isRegisteredUserFromId($id);
        return !$this->requireRegister || $isregistered;
    }

    protected function canAccess()
    {
        return $this->isRegisteredCorrect() &&
            $this->isMasterCorrect();
    }

    public function preExecute()
    {
        $answer = $this->preExecuteRolBot();
        if (!is_null($answer)) {
            return $answer;
        }
        parent::preExecute();
    }

    // if you don't return anything then everything is ok
    // if you want it to fail then return the answer that will be used
    protected function preExecuteRolBot()
    {
        $canAccess = $this->canAccess();
        if (!$canAccess) {
            return Request::emptyResponse();
        }
    }

}
