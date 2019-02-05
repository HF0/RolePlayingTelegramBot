<?php
namespace RolBot\Entities;

use \RedBeanPHP\R as R;
use \RedBeanPHP\OODBBean;
use RolBot\Repository\TelegramUserRepository;

class Character
{
    const CHARACTER_TABLE = 'rolcharacter';

    protected $characterBean;

    public function __construct(OODBBean $characterBean)
    {
        if (!$characterBean) {
            throw new \InvalidArgumentException("Error creating character");
        }
        $this->characterBean = $characterBean;
    }

    private static function mapPropertiesToInt($properties, &$item)
    {
        foreach ($properties as $property) {
            $item[$property] = intval($item[$property]);
        }
    }

    public static function delete($name)
    {
        $fileDeleted = false;
        $entry = R::findOne(self::CHARACTER_TABLE, 'name = ? ', [$name]);
        if ($entry) {
            R::trash($entry);
            $fileDeleted = true;
        }
        return $fileDeleted;
    }

    public static function deleteIfNotUsed($name)
    {
        $entry = R::findOne(self::CHARACTER_TABLE, 'name = ? ', [$name]);
        if ($entry) {
            $inGroup = R::findOne(FightGroup::CHARACTER_STATUS_TABLE_NAME, 'rolcharacter_id = ? ', [$entry->getID()]);
            if (!$inGroup) {
                R::trash($entry);
                $fileDeleted = true;
            } else {
                throw new \InvalidArgumentException("The character is used and cannot be deleted");
            }
        }
    }

    public static function add(
        $name,
        $life,
        $description,
        $attack,
        $defense,
        $dexterity,
        $level,
        $username
    ) {
        if (!is_integer($life)) {
            throw new \InvalidArgumentException('Life must be an integer');
        }
        if ($life <= 0) {
            throw new \InvalidArgumentException("Life must be positive");
        }
        if ($attack < 0) {
            throw new \InvalidArgumentException("Attack must be positive");
        }
        if ($defense < 0) {
            throw new \InvalidArgumentException("Defense must be positive");
        }
        if ($dexterity < 0) {
            throw new \InvalidArgumentException("Dexterity must be positive");
        }
        if ($level <= 0 || $level > 300) {
            throw new \InvalidArgumentException("Level must be positive");
        }
        $userrepository = new TelegramUserRepository();
        if (!$userrepository->isRegisteredUserFromName($username)) {
            throw new \InvalidArgumentException("Incorrect user");
        }

        if (Character::exists($name)) {
            throw new \InvalidArgumentException("Character already exists");
        }
        $table = R::dispense(self::CHARACTER_TABLE);
        $table->name = $name;
        $table->life = $life;
        $table->level = $level;
        $table->description = $description;
        $table->attack = $attack;
        $table->defense = $defense;
        $table->dexterity = $dexterity;
        $table->maxlife = $life;
        $table->control = $username;
        $id = R::store($table);
    }

    public static function getAll()
    {
        $data = R::findAll(self::CHARACTER_TABLE);
        foreach ($data as &$item) {
            Character::mapPropertiesToInt(['life', 'level', 'attack', 'dexterity', 'defense', 'maxlife'], $item);
        }
        return $data;
    }

    public static function exists($name)
    {
        $count = R::count(self::CHARACTER_TABLE, ' name = ? ', [$name]);
        return $count > 0;
    }

    public static function getByName($name)
    {
        $entry = R::findOne(self::CHARACTER_TABLE, 'name = ? ', [$name]);
        if (!$entry) {
            throw new \InvalidArgumentException("Character not found");
        }
        return new Character($entry);
    }

    public function getLevel()
    {
        return intval($this->getBean()->level);
    }

    public function getName()
    {
        return $this->getBean()->name;
    }

    public function getControl()
    {
        return $this->getBean()->control;
    }

    public function setControl($control)
    {
        $this->getBean()->control = $control;
        R::store($this->getBean());
    }

    public function getBean()
    {
        return $this->characterBean;
    }

    public function getLife()
    {
        return intval($this->getBean()->life);
    }

    public function setLife($life)
    {
        if ($life > $this->getMaxLife()) {
            throw new \InvalidArgumentException("Life higher than max life");
        }
        $this->getBean()->life = $life;
        R::store($this->getBean());
    }

    public function setLevel($level)
    {
        $this->getBean()->level = $level;
        R::store($this->getBean());
    }

    public function getDescription()
    {
        return $this->getBean()->description;
    }

    public function setDescription($description)
    {
        $this->getBean()->description = $description;
        R::store($this->getBean());
    }

    public function getMaxLife()
    {
        return intval($this->getBean()->maxlife);
    }

    public function setMaxlife($maxlife)
    {
        $this->getBean()->maxlife = $maxlife;
        R::store($this->getBean());
    }

    public function isNpc()
    {
        return false;
    }

    public function getDefense()
    {
        return intval($this->getBean()->defense);
    }

    public function setDefense($defense)
    {
        $this->getBean()->defense = $defense;
        R::store($this->getBean());
    }

    public function getDexterity()
    {
        return intval($this->getBean()->dexterity);
    }

    public function setDexterity($dexterity)
    {
        $this->getBean()->dexterity = $dexterity;
        R::store($this->getBean());
    }

    public function getAttack()
    {
        return intval($this->getBean()->attack);
    }

    public function setAttack($attack)
    {
        $this->getBean()->attack = $attack;
        R::store($this->getBean());
    }
}
