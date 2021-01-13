<?php

namespace Invertus\dpdBaltics\DTO;

class DPDProductInstall
{
    private $id;

    private $name;

    private $delay;

    private $isPudo;

    private $isCod;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param mixed $delay
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }

    /**
     * @return mixed
     */
    public function getIsPudo()
    {
        return $this->isPudo;
    }

    /**
     * @param mixed $isPudo
     */
    public function setIsPudo($isPudo)
    {
        $this->isPudo = $isPudo;
    }

    /**
     * @return mixed
     */
    public function getIsCod()
    {
        return $this->isCod;
    }

    /**
     * @param mixed $isCod
     */
    public function setIsCod($isCod)
    {
        $this->isCod = $isCod;
    }
}
