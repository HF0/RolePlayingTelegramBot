<?php
namespace RolBot\Telegram\Mappers;
use RedBeanPHP\OODBBean;
use RolBot\Entities\Fighter;
use RolBot\Telegram\Mappers\FighterMapper;
use RolBot\Entities\Character;


class FighterMapper
{
    public static function pjlistArrayToString($pjlistbean, $showinfo = false, $showdescription = false) {
        $resultstring = "";
        if( count($pjlistbean) === 0 ) {
            $resultstring = 'No players';
        } else {
            foreach( $pjlistbean as $pj) {
                $resultstring .= "*{$pj['name']}*";
                if( $showinfo ) {
                    $resultstring .= "  ⚔️{$pj['attack']}🛡{$pj['defense']} ❤️{$pj['life']}/{$pj['maxlife']}";
                }
                if( $showdescription ) {
                    $resultstring .= " {$pj['description']}";
                }
                $resultstring .="\n";
            }
        }
        return $resultstring;
    }

    public static function fighterListToArray(array $fighterList) {
        $result = array();
        foreach( $fighterList as $fighter ) {
            array_push($result, FighterMapper::toArray($fighter));
        }
        return $result;
    }

    public static function toArray(Fighter $fighter) {
        $fighterArray = array();
        $fighterArray['name'] = $fighter->getName();
        $fighterArray['description'] = $fighter->getDescription();
        $fighterArray['attack'] = $fighter->getAttack();
        $fighterArray['defense'] = $fighter->getDefense();
        $fighterArray['dexterity'] = $fighter->getDexterity();
        $fighterArray['life'] = $fighter->getLife();
        $fighterArray['maxlife'] = $fighter->getMaxLife();
        $fighterArray['round_over'] = $fighter->getRoundOver();
        $fighterArray['control'] = $fighter->getControl();
        $fighterArray['level'] = $fighter->getLevel();
        $fighterArray['enabled'] = $fighter->isEnabled();
        return $fighterArray;
    }

    public static function fighterListToString(array $fighterList, $showinfo = false, $showdescription = false) {
        $resultstring = "";
        if( count($fighterList) === 0 ) {
            $resultstring = 'No players';
        } else {
            foreach( $fighterList as $fighter) {
                $resultstring .= "*{$fighter->getName()}*";
                if( $showinfo ) {
                    $resultstring .= "  ⚔️{$fighter->getAttack()}🛡{$fighter->getDefense()}";
                    $resultstring .= "  ❤️{$fighter->getLife()}/{$fighter->getMaxLife()}";
                }
                if( $showdescription ) {
                    $resultstring .= " {$fighter->getDescription()}";
                }
                $resultstring .="\n";
            }
        }
        return $resultstring;
    }

    public static function characterToString(Character $character) {
        $resultstring = '👤' . $character->getName() . "\n";
        $life = '❤️ ' . $character->getLife() . "/" . $character->getMaxLife();
        if( $character->getLife() <= 0 ) {
            $life = '💀 DEAD';
        }
        $resultstring .= $life . "";
        $resultstring .= "⚔️ " . $character->getAttack();
        $resultstring .= "🛡" . $character->getDefense();
        $resultstring .= "🌟" . $character->getLevel();
        $resultstring .= "🔱" . $character->getDexterity() . "\n";
        $resultstring .= '💬' . $character->getDescription() . "\n";
        return $resultstring;
    }
}
