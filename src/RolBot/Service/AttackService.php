<?php
namespace RolBot\Service;

use \RedBeanPHP\R as R;
use RolBot\Entities\FightGroup;
use RolBot\Repository\TelegramUserRepository;
use RolBot\Utils\Dice;
use RolBot\Entities\AttackResult;
use RedBeanPHP\BeanHelper;

class AttackService
{
    private $turepository;

    public function __construct()
    {
        $this->turepository = new TelegramUserRepository();
    }

    public function currentPlayerAttack(FightGroup $fightGroup, $targetname)
    {
        $attackResult = new AttackResult();
        $attackResult->setTarget($fightGroup->getFighter($targetname));
        if (!$attackResult->getTarget()) {
            throw new \InvalidArgumentException("Character does not exist ");
        }
        $attackResult->setAttacker($fightGroup->getNextPlayer());
        if (!$attackResult->getAttacker()) {
            throw new \InvalidArgumentException("Target could not be found");
        }
        if ($attackResult->getAttacker()->getLife() <= 0) {
            throw new \InvalidArgumentException("Attacker is dead");
        }
        // first attack
        $firstAttackDiceSides = 20;
        $firstAttackNumber = Dice::throwDice($firstAttackDiceSides);
        $firstAttackResultString = Dice::diceResultStringFromInt($firstAttackDiceSides, $firstAttackNumber);
        $attackResult->setFirstAttack($firstAttackResultString);

        if ($firstAttackNumber <= 2 && !$attackResult->getAttacker()->isNpc()) {
            $attackResult->setFirstAttackMode("Blunt");
            $this->pifiaAttack($fightGroup, $attackResult);
        } else if ($firstAttackNumber >= 18 && !$attackResult->getAttacker()->isNpc()) {
            $attackResult->setFirstAttackMode("Epic attack");
            $this->epicAttack($fightGroup, $attackResult);
        } else {
            $attackResult->setFirstAttackMode("Normal attack");
            $this->normalAttack($fightGroup, $attackResult);
        }
        return $attackResult;
    }

    private function pifiaAttack(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $blunderDiceSides = 20;
        $blunderNumber = Dice::throwDice($blunderDiceSides);
        $blunderResultString = Dice::diceResultStringFromInt($blunderDiceSides, $blunderNumber);
        $attackResult->setSecondAttack($blunderResultString);
        $attackResult->setIsBlunder();

        $blunder = $this->getBlunder($blunderNumber);
        $handleFunction = $blunder['handler'];
        $this->$handleFunction($fightGroup, $attackResult);
        $attackResult->setMessage($blunder['description']);
    }

    private function epicAttack(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $epicDiceSides = 20;
        $epicResult = Dice::throwDice($epicDiceSides);
        $epicResultString = Dice::diceResultStringFromInt($epicDiceSides, $epicResult);
        $attackResult->setSecondAttack($epicResultString);
        $attackResult->setIsBlunder();

        $epicAttack = $this->getEpic($epicResult);
        $handleFunction = $epicAttack['handler'];
        $this->$handleFunction($fightGroup, $attackResult);
        $attackResult->setMessage($epicAttack['description']);
    }

    private function normalAttack(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $secondAttackDiceSides = 6;
        $normalAttackNumber = Dice::throwDice($secondAttackDiceSides);
        $normalAttackResultString = Dice::diceResultStringFromInt($secondAttackDiceSides, $normalAttackNumber);
        $attackResult->setSecondAttack($normalAttackResultString);

        $damage = $attackResult->getAttacker()->getAttack() - $attackResult->getTarget()->getDefense() + $normalAttackNumber;
        $damage = max(0, $damage);
        $attackResult->setDamage($damage);
        $attackResult->setIsNormal();

        $attackResult->setTargetLifeBefore($attackResult->getTarget()->getLife());
        $lifeafter = max($attackResult->getTarget()->getLife() - $damage, 0);
        $attackResult->getTarget()->setLife($lifeafter);
    }

    private function handle1_5(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $attackResult->setTarget($attackResult->getAttacker());
        $attackResult->setDamage(1);

        $attackResult->setTargetLifeBefore($attackResult->getTarget()->getLife());
        $lifeafter = max($attackResult->getTarget()->getLife() - $attackResult->getDamage(), 0);
        $attackResult->getTarget()->setLife($lifeafter);
    }

    private function handle6_9(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $attackResult->setTarget($fightGroup->getRandomPartnerCharacter($attackResult->getAttacker()));
        $attackResult->setDamage(3);

        $attackResult->setTargetLifeBefore($attackResult->getTarget()->getLife());
        $lifeafter = max($attackResult->getTarget()->getLife() - $attackResult->getDamage(), 0);
        $attackResult->getTarget()->setLife($lifeafter);
    }

    private function handle10_16(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $attackResult->setTarget($attackResult->getAttacker());
        $attackResult->setDamage(5);

        $attackResult->setTargetLifeBefore($attackResult->getTarget()->getLife());
        $lifeafter = max($attackResult->getTarget()->getLife() - $attackResult->getDamage(), 0);
        $attackResult->getTarget()->setLife($lifeafter);
    }

    private function handle17_17(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $attackResult->setDamage(0);
        $attackResult->setTargetLifeBefore($attackResult->getTarget()->getLife());
    }

    private function handle18_18(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $attackResult->setDamage(0);
        $attackResult->setTargetLifeBefore($attackResult->getTarget()->getLife());
    }

    private function handle19_20(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $attackResult->setTarget($attackResult->getAttacker());

        $blunderDamageDiceSides = 20;
        $blunderDamage = Dice::throwDice($blunderDamageDiceSides);
        $attackResult->setDamage($blunderDamage);

        $attackResult->setTargetLifeBefore($attackResult->getTarget()->getLife());
        $lifeafter = max($attackResult->getTarget()->getLife() - $attackResult->getDamage(), 0);
        $attackResult->getTarget()->setLife($lifeafter);
    }

    protected $blunderList = array(
        array(
            "min" => 1,
            "max" => 5,
            "description" => "You are not focused and you fall. -1 hp",
            "handler" => "handle1_5"
        ),

        array(
            "min" => 6,
            "max" => 9,
            "description" => "In the heat of the battle you attack your partner. -3 hp",
            "handler" => "handle6_9"
        ),

        array(
            "min" => 10,
            "max" => 16,
            "description" => "You hit yourself. -5 hp",
            "handler" => "handle10_16"
        ),

        array(
            "min" => 17,
            "max" => 17,
            "description" => "You fall in front of the strongest opponent. The opponent next attack will go against you",
            "handler" => "handle17_17"
        ),

        array(
            "min" => 18,
            "max" => 18,
            "description" => "You weapon drops next to an opponent. You cannot attack until this opponent dies",
            "handler" => "handle18_18"
        ),

        array(
            "min" => 19,
            "max" => 20,
            "description" => "Self-attack. -1d20 hp",
            "handler" => "handle19_20"
        )
    );


    protected $epicList = array(
        array(
            "min" => 1,
            "max" => 5,
            "description" => "You hit the opponent hard. -9 hp",
            "handler" => "handleEpic1_5"
        ),

        array(
            "min" => 6,
            "max" => 9,
            "description" => "You hit really hard. -12 hp",
            "handler" => "handleEpic6_9"
        ),

        array(
            "min" => 10,
            "max" => 14,
            "description" => "Good strike! -15 hp",
            "handler" => "handleEpic10_14"
        ),

        array(
            "min" => 15,
            "max" => 15,
            "description" => "You feel powerful. Your damage increases 1D20",
            "handler" => "handleEpic15_15"
        ),

        array(
            "min" => 16,
            "max" => 16,
            "description" => "Incredible attack! -1D30 hp",
            "handler" => "handleEpic16_16"
        ),

        array(
            "min" => 17,
            "max" => 17,
            "description" => "Your armor shines as you strike your opponent. -20 hp",
            "handler" => "handleEpic17_17"
        ),

        array(
            "min" => 18,
            "max" => 18,
            "description" => "You are surrounded by a aura of strenght. Your weapon shines as you strike. -25 hp",
            "handler" => "handleEpic18_18"
        ),

        array(
            "min" => 19,
            "max" => 19,
            "description" => "You unleash an incredible power in the attack.  -30 hp",
            "handler" => "handleEpic19_19"
        ),

        array(
            "min" => 20,
            "max" => 20,
            "description" => "Flawless attack. You kill the opponent instantly",
            "handler" => "handleEpic20_20"
        )
    );

    private function handleEpic1_5(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $this->doFixedDamage($fightGroup, $attackResult, 9);
    }

    private function handleEpic6_9(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $this->doFixedDamage($fightGroup, $attackResult, 12);
    }

    private function handleEpic10_14(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $this->doFixedDamage($fightGroup, $attackResult, 15);
    }

    private function handleEpic15_15(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $epicDamageDiceSides = 20;
        $epicDamage = Dice::throwDice($epicDamageDiceSides);
        $attackResult->setDamage($epicDamage);

        $attackResult->setTargetLifeBefore($attackResult->getTarget()->getLife());
        $lifeafter = max($attackResult->getTarget()->getLife() - $attackResult->getDamage(), 0);
        $attackResult->getTarget()->setLife($lifeafter);
    }

    private function doFixedDamage(FightGroup $fightGroup, AttackResult &$attackResult, $damage)
    {
        $attackResult->setDamage($damage);
        $attackResult->setTargetLifeBefore($attackResult->getTarget()->getLife());
        $lifeafter = max($attackResult->getTarget()->getLife() - $attackResult->getDamage(), 0);
        $attackResult->getTarget()->setLife($lifeafter);
    }

    private function handleEpic16_16(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $epicDamageDiceSides = 30;
        $epicDamage = Dice::throwDice($epicDamageDiceSides);
        $attackResult->setDamage($epicDamage);

        $attackResult->setTargetLifeBefore($attackResult->getTarget()->getLife());
        $lifeafter = max($attackResult->getTarget()->getLife() - $attackResult->getDamage(), 0);
        $attackResult->getTarget()->setLife($lifeafter);
    }

    private function handleEpic17_17(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $this->doFixedDamage($fightGroup, $attackResult, 20);
    }

    private function handleEpic18_18(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $this->doFixedDamage($fightGroup, $attackResult, 25);
    }

    private function handleEpic19_19(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $this->doFixedDamage($fightGroup, $attackResult, 30);
    }

    private function handleEpic20_20(FightGroup $fightGroup, AttackResult &$attackResult)
    {
        $this->doFixedDamage($fightGroup, $attackResult, $attackResult->getTarget()->getLife());
    }

    private function getEpic($d20Result)
    {
        foreach ($this->epicList as $epic) {
            if ($d20Result >= $epic['min'] && $d20Result <= $epic['max']) {
                return $epic;
            }
        }
    }

    private function getBlunder($d20Result)
    {
        foreach ($this->blunderList as $blunder) {
            if ($d20Result >= $blunder['min'] && $d20Result <= $blunder['max']) {
                return $blunder;
            }
        }
    }

}
