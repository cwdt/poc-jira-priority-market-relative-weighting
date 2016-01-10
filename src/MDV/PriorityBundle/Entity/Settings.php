<?php

namespace MDV\PriorityBundle\Entity;

/**
 * Settings
 */
class Settings
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var bool
     */
    private $open;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set open
     *
     * @param boolean $open
     *
     * @return Settings
     */
    public function setOpen($open)
    {
        $this->open = $open;

        return $this;
    }

    /**
     * Get open
     *
     * @return bool
     */
    public function getOpen()
    {
        return $this->open;
    }
}

