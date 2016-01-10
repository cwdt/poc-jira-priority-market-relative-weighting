<?php

namespace MDV\PriorityBundle\Entity;

/**
 * Issue
 */
class Issue
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $jiraKey;

    /**
     * @var Vote[]
     */
    private $votes;

    /**
     * @var string
     */
    private $summary;


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
     * Set jiraKey
     *
     * @param string $jiraKey
     *
     * @return Issue
     */
    public function setJiraKey($jiraKey)
    {
        $this->jiraKey = $jiraKey;

        return $this;
    }

    /**
     * Get jiraKey
     *
     * @return string
     */
    public function getJiraKey()
    {
        return $this->jiraKey;
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
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }
}

