<?php
namespace RolBot\Entities;

class AttackResult
{

    protected $firstAttack;
    protected $firstAttackMode;
    protected $secondAttack;
    protected $targetLifeBefore;

    protected $attacker;
    protected $target;
    protected $damage;
    protected $message = "";

    protected $isBlunder = false;
    protected $isEpic = false;
    protected $isNormal = false;

    public function getTargetLifeBefore()
    {
        return $this->targetLifeBefore;
    }

    public function setTargetLifeBefore($targetLifeBefore)
    {
        $this->targetLifeBefore = $targetLifeBefore;
    }

    public function setFirstAttack($firstAttack)
    {
        $this->firstAttack = $firstAttack;
    }

    public function getFirstAttack()
    {
        return $this->firstAttack;
    }

    public function setSecondAttack($secondAttack)
    {
        $this->secondAttack = $secondAttack;
    }

    public function getSecondAttack()
    {
        return $this->secondAttack;
    }

    public function setFirstAttackMode($firstAttackMode)
    {
        $this->firstAttackMode = $firstAttackMode;
    }

    public function getFirstAttackMode()
    {
        return $this->firstAttackMode;
    }

    public function setAttacker($attacker)
    {
        $this->attacker = $attacker;
    }

    public function getAttacker()
    {
        return $this->attacker;
    }

    public function setTarget($target)
    {
        $this->target = $target;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function getDamage()
    {
        return $this->damage;
    }

    public function setDamage($damage)
    {
        $this->damage = $damage;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function isBlunder()
    {
        return $this->isBlunder;
    }

    public function setIsBlunder()
    {
        $this->isBlunder = true;
        $this->isEpic = false;
        $this->isNormal = false;
    }

    public function isEpic()
    {
        return $this->isEpic;
    }

    public function setIsEpic()
    {
        $this->isBlunder = false;
        $this->isEpic = true;
        $this->isNormal = false;
    }

    public function isNormal()
    {
        return $this->isNormal;
    }

    public function setIsNormal()
    {
        $this->isBlunder = false;
        $this->isEpic = false;
        $this->isNormal = true;
    }
}
