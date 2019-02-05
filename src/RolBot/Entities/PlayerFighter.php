<?php
namespace RolBot\Entities;

use \RedBeanPHP\R as R;
use \RedBeanPHP\OODBBean;
use Slim\Exception\MethodNotAllowedException;

class PlayerFighter implements Fighter
{
    protected $characterStatusBean;
    protected $character;

    public function __construct(OODBBean $characterStatusBean)
    {
        if (!$characterStatusBean) {
            throw new \InvalidArgumentException("Error creating playerfighter");
        }
        $this->characterStatusBean = $characterStatusBean;
        $this->character = new Character($this->characterStatusBean->rolcharacter);
    }

    public function isEnabled()
    {
        return boolval($this->getBean()->enabled);
    }

    public function getLevel()
    {
        return $this->character->getLevel();
    }

    public function setLevel()
    {
        throw new MethodNotAllowedException();
    }

    public function setEnabled($enabled)
    {
        $this->getBean()->enabled = $enabled;
        R::store($this->getBean());
    }

    public function setRoundOver($roundOver)
    {
        $this->getBean()->round_over = $roundOver;
        R::store($this->getBean());
    }

    public function getRoundOver()
    {
        return boolval($this->getBean()->round_over);
    }

    public function getName()
    {
        return $this->character->getName();
    }

    public function setName($name)
    {
        $this->getBean()->name = $name;
        R::store($this->getBean());
    }

    public function setAttack($attack)
    {
        $this->getBean()->attack = $attack;
        R::store($this->getBean());
    }

    public function setDefense($defense)
    {
        $this->getBean()->defense = $defense;
        R::store($this->getBean());
    }

    public function setDescription($description)
    {
        $this->getBean()->description = $description;
        R::store($this->getBean());
    }

    public function setMaxlife($maxlife)
    {
        $this->getBean()->maxlife = $maxlife;
        R::store($this->getBean());
    }

    public function setDexterity($dexterity)
    {
        $this->getBean()->dexterity = $dexterity;
        R::store($this->getBean());
    }

    public function getControl()
    {
        return $this->character->getControl();
    }

    public function getBean()
    {
        return $this->characterStatusBean;
    }

    public function getLife()
    {
        return $this->character->getLife();
    }

    public function setLife($life)
    {
        $this->character->setLife($life);
    }

    public function getDescription()
    {
        return $this->character->getDescription();
    }

    public function getMaxLife()
    {
        return $this->character->getMaxLife();
    }

    public function isNpc()
    {
        return false;
    }

    public function getDefense()
    {
        return $this->character->getDefense();
    }

    public function getDexterity()
    {
        return $this->character->getDexterity();
    }

    public function getAttack()
    {
        return $this->character->getAttack();
    }
}
