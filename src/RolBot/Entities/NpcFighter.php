<?php
namespace RolBot\Entities;

use \RedBeanPHP\R as R;
use \RedBeanPHP\OODBBean;
use Slim\Exception\MethodNotAllowedException;

class NpcFighter implements Fighter
{

    protected $npcBean;

    public function __construct(OODBBean $npcBean)
    {
        if (!$npcBean) {
            throw new \InvalidArgumentException("Error creating npcfighter");
        }
        $this->npcBean = $npcBean;
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

    public function getLevel()
    {
        return 0;
    }

    public function isEnabled()
    {
        return true;
    }

    public function getControl()
    {
        return "Master";
    }

    public function getName()
    {
        return $this->getBean()->name;
    }

    private function getBean()
    {
        return $this->npcBean;
    }

    public function getLife()
    {
        return intval($this->getBean()->life);
    }

    public function setLife($life)
    {
        if ($life > $this->getMaxLife()) {
            throw new \InvalidArgumentException("Life is higher than max life");
        }
        $this->getBean()->life = $life;
        R::store($this->getBean());
    }

    public function getDescription()
    {
        return $this->getBean()->description;
    }

    public function getMaxLife()
    {
        return intval($this->getBean()->maxlife);
    }

    public function isNpc()
    {
        return true;
    }

    public function getDefense()
    {
        return intval($this->getBean()->defense);
    }

    public function getDexterity()
    {
        return intval($this->getBean()->dexterity);
    }

    public function getAttack()
    {
        return intval($this->getBean()->attack);
    }

    public function setEnabled($enabled)
    {
        throw new MethodNotAllowedException();
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

    public function setDexterity($dexterity)
    {
        $this->getBean()->dexterity = $dexterity;
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
}
