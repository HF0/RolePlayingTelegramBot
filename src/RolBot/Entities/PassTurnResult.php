<?php

namespace RolBot\Entities;

class PassTurnResult
{

    protected $ok;
    protected $correctUserPasses;

    public function isOk()
    {
        return $this->ok;
    }

    public function setOk($ok)
    {
        $this->ok = $ok;
    }

    public function setCorrectUserPasses($correctUserPasses)
    {
        $this->correctUserPasses = $correctUserPasses;
    }

    public function getCorrectUserPasses()
    {
        return $this->correctUserPasses;
    }

}
