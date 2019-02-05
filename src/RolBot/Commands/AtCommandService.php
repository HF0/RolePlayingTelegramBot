<?php

namespace RolBot\Commands;

use RolBot\Entities\FightGroup;
use RolBot\Service\AttackService;
use RolBot\Service\PassTurnService;

class AtCommandService
{

    public function run($targetName, FightGroup &$fightGroup, $telegramidstring)
    {
        try {
            if ($fightGroup->getFightStatus()->isFinished()) {
                throw new \UnexpectedValueException("No active fight");
            }
            $fightStatus = $fightGroup->getFightStatus();

            $attackservice = new AttackService();
            $atResult = $attackservice->currentPlayerAttack($fightGroup, $targetName);
            $msg = "";
            if (!$atResult->getAttacker()->isNpc()) {
                $msg .= "{$atResult->getFirstAttack()} {$atResult->getFirstAttackMode()}\n";
            }
            $msg .= "{$atResult->getSecondAttack()}\n";
            if ($atResult->getMessage()) {
                $msg .= "{$atResult->getMessage()}\n";
            }

            if ($atResult->getAttacker()->getName() === $atResult->getTarget()->getName()) {
                $msg .= "{$atResult->getAttacker()->getName()} self inflicts ";
                $msg .= " {$atResult->getDamage()}\n";
            } else {
                $msg .= "{$atResult->getAttacker()->getName()} attacks {$atResult->getTarget()->getName()} ";
                $msg .= "(damage: {$atResult->getDamage()})\n";
            }
            $msg .= "{$atResult->getTarget()->getName()} ❤️ {$atResult->getTargetLifeBefore()} -> {$atResult->getTarget()->getLife()}";
            $msg .= "\n";

            $fightStatus = $fightGroup->getFightStatus();
            if ($fightStatus->isFinished()) {
                $fightresultmessage = strtoupper($fightStatus->getDescription());
                $msg .= "{$fightGroup->getName()} finished: {$fightresultmessage}\n";
            } else {
                $previousRound = $fightGroup->getRound();
                // pass current turn
                $passturnService = new PassTurnService();
                $passturnResult = $passturnService->passTurn($fightGroup, $telegramidstring);
                $next = $fightGroup->getNextPlayer();
                $currentRound = $fightGroup->getRound();
                if ($previousRound !== $currentRound) {
                    $msg .= "ROUND {$currentRound}\n";
                }
                $msg .= "Turn: {$next->getName()}";
            }
        } catch (\Exception $e) {
            $msg = "❌" . $e->getMessage();
        }
        return $msg;
    }
}
