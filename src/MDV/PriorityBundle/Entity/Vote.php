<?php

namespace MDV\PriorityBundle\Entity;

/**
 * Vote
 */
class Vote
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $vote;

    /**
     * @var Stakeholder
     */
    private $stakeholder;

    /**
     * @var Issue
     */
    private $issue;


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
     * Set vote
     *
     * @param integer $vote
     *
     * @return Vote
     */
    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return int
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * @return Stakeholder
     */
    public function getStakeholder()
    {
        return $this->stakeholder;
    }

    /**
     * @param Stakeholder $stakeholder
     */
    public function setStakeholder($stakeholder)
    {
        $this->stakeholder = $stakeholder;
    }

    /**
     * @return Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }

    /**
     * @param Issue $issue
     */
    public function setIssue($issue)
    {
        $this->issue = $issue;
    }
}
