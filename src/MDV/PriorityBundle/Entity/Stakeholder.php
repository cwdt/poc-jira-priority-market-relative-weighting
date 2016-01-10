<?php

namespace MDV\PriorityBundle\Entity;

/**
 * Stakeholder
 */
class Stakeholder
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $ratio;

    /**
     * @var Vote[]
     */
    private $votes;

    /** @var  int */
    private $allowedVotes;


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
     * Set name
     *
     * @param string $name
     *
     * @return Stakeholder
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set ratio
     *
     * @param float $ratio
     *
     * @return Stakeholder
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;

        return $this;
    }

    /**
     * Get ratio
     *
     * @return float
     */
    public function getRatio()
    {
        return $this->ratio;
    }

    /**
     * @return Vote[]
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param Vote[] $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }

    /**
     * @return int
     */
    public function getAllowedVotes()
    {
        return $this->allowedVotes;
    }

    /**
     * @param int $allowedVotes
     */
    public function setAllowedVotes($allowedVotes)
    {
        $this->allowedVotes = $allowedVotes;
    }
}

