<?php
namespace RolBot\Telegram\Mappers;
use RolBot\Entities\FightGroup;

class FightGroupMapper
{
    public static function listToArray(array $fightGroups) {
        $result = array();
        foreach($fightGroups as $fightGroup ) {
            array_push($result, FightGroupMapper::toArray($fightGroup));
        }
        return $result;
    }
    public static function toArray(FightGroup $fightGroup) {
        $result = array();
        $result['name'] = $fightGroup->getName();
        $result['description'] = $fightGroup->getDescription();
        $result['numenemies'] = $fightGroup->getNumEnemies();
        $result['round'] = $fightGroup->getRound();
        $result['enabled'] = $fightGroup->isEnabled();
        $fightStatus = $fightGroup->getFightStatus();
        $result['status'] = array(
            'isfinished' => $fightStatus->isFinished(),
            'description' => $fightStatus->getDescription()
        );
        $next = $fightGroup->getNextPlayer();
        $result['next'] = array(
            'name' => $next ? $next->getName() : "No one",
            'isnpc' => $next ? $next->isNpc() : "",
            'control' => $next ? $next->getControl() : ""
        );
        return $result;
    }
}
