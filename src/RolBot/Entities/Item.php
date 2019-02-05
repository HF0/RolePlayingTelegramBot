<?php
namespace RolBot\Entities;

use \RedBeanPHP\R as R;
use \RedBeanPHP\OODBBean;
use Slim\Exception\MethodNotAllowedException;

class Item
{
    protected $bean;

    public function __construct(OODBBean $bean)
    {
        if (!$bean) {
            throw new \InvalidArgumentException("Error creating item");
        }
        $this->bean = $bean;
    }

    public function getBean()
    {
        return $this->bean;
    }

    public function getItem()
    {
        return $this->getBean()->item;
    }

    public function setItem($item)
    {
        $this->getBean()->item = $item;
        R::store($this->getBean());
    }

    public function getQuantity()
    {
        return $this->getBean()->quantity;
    }

    public function setQuantity($quantity)
    {
        $this->getBean()->quantity = $quantity;
        R::store($this->getBean());
    }

    public function getCharacter()
    {
        return $this->getBean()->character;
    }

    public function setCharacter($character)
    {
        $this->getBean()->character = $character;
        R::store($this->getBean());
    }

}
