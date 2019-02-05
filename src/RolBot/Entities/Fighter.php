<?php
namespace RolBot\Entities;

interface Fighter
{
    public function getName();
    public function setName($name);
    public function getLife();
    public function setLife($life);

    public function isNpc();

    public function getAttack();
    public function setAttack($attack);
    public function getDefense();
    public function setDefense($defense);
    public function getDexterity();
    public function setDexterity($dexterity);
    public function getDescription();
    public function setDescription($description);
    public function getMaxLife();
    public function setMaxlife($maxlife);
    public function getControl();

    public function isEnabled();
    public function getLevel();
    public function setEnabled($enabled);

    public function setRoundOver($roundOver);
    public function getRoundOver();
}
