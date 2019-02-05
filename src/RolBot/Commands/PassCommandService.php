<?php

namespace RolBot\Commands;

use RolBot\Entities\FightGroup;
use RolBot\Service\AttackService;
use RolBot\Service\PassTurnService;

class PassCommandService
{

    public function run(FightGroup &$fightGroup, $telegramidstring)
    {
        $text = "";
        try {
            $currentPlayer = $fightGroup->getNextPlayer();
            $previousRound = $fightGroup->getRound();
            $passturnService = new PassTurnService();
            $passturnResult = $passturnService->passTurn($fightGroup, $telegramidstring);

            if ($passturnResult->isOk()) {
                $text .= "{$fightGroup->getName()}: ";

                if ($passturnResult->getCorrectUserPasses()) {
                    $text .= "{$currentPlayer->getName()} passes. Round {$previousRound}\n";
                } else {
                    if ($currentPlayer->isNpc()) {
                        $text .= "{$currentPlayer->getName()} (NPC) passes. Round {$previousRound}\n";
                    } else {
                        $text .= "Master passes on behalf of {$currentPlayer->getName()}. Round {$previousRound}\n";
                    }
                }
                $currentRound = $fightGroup->getRound();
                if ($previousRound !== $currentRound) {
                    $text .= "ROUND {$fightGroup->getRound()} STARTS\n";
                }
                $text .= "Turn: *{$fightGroup->getNextPlayer()->getName()}*\n";
            }
        } catch (\Exception $e) {
            $text = "❌" . $e->getMessage();
        }
        return $text;
    }
}
