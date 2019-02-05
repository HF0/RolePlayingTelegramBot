<?php

namespace RolBot\Entities;

class FightStatus
{

    protected $finished = true;
    protected $description = "";

    public function isFinished()
    {
        return $this->finished;
    }

    public function setFinished($finished)
    {
        $this->finished = $finished;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
