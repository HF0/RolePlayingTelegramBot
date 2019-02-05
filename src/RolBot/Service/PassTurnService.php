<?php

namespace RolBot\Service;

use RolBot\Entities\Fighter;
use RolBot\Entities\PassTurnResult;
use RolBot\Entities\FightGroup;
use RolBot\Repository\TelegramUserRepository;

class PassTurnService
{
    private $turepository;

    public function __construct()
    {
        $this->turepository = new TelegramUserRepository();
    }

    // telegram id of the user who wants to pass turn
    public function passTurn(FightGroup &$fightGroup, $telegramidstring)
    {
        if ($fightGroup->getFightStatus()->isFinished()) {
            throw new \InvalidArgumentException("Fight has finished");
        }

        $passTurnResult = new PassTurnResult();
        $passTurnResult->setOk(false);

        $nextplayer = $fightGroup->getNextPlayer();
        // Correct user passes turn
        if ($this->turepository->useridExistsWithName($telegramidstring, $nextplayer->getControl())) {
            $passTurnResult->setCorrectUserPasses(true);
            $passTurnResult->setOk(true);
        // Master user passes turn
        } else if ($this->turepository->isMasterFromUserIdString($telegramidstring)) {
            $passTurnResult->setCorrectUserPasses(false);
            $passTurnResult->setOk(true);
        }
        if ($passTurnResult->isOk()) {
            $nextplayer->setRoundOver(true);
            $fightGroup->resetTurnIfAllRoundOver();
        }
        return $passTurnResult;
    }

}
