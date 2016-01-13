<?php

namespace MDV\PriorityBundle\Entity;

/**
 * Priority
 */
class Priority
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Issue
     */
    private $issue;

    /**
     * @var int
     */
    private $positiveValue;

    /**
     * @var int
     */
    private $negativeValue;

    /**
     * @var int
     */
    private $totalValue;

    /**
     * @var float
     */
    private $totalValuePercentage;

    /**
     * @var int
     */
    private $cost;

    /**
     * @var float
     */
    private $costPercentage;

    /**
     * @var int
     */
    private $risk;

    /**
     * @var float
     */
    private $riskPercentage;

    /**
     * @var float
     */
    private $priority;


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
     * Set issue
     *
     * @param Issue $issue
     *
     * @return Priority
     */
    public function setIssue($issue)
    {
        $this->issue = $issue;

        return $this;
    }

    /**
     * Get issue
     *
     * @return Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }

    /**
     * Set positiveValue
     *
     * @param integer $positiveValue
     *
     * @return Priority
     */
    public function setPositiveValue($positiveValue)
    {
        $this->positiveValue = $positiveValue;

        return $this;
    }

    /**
     * Get positiveValue
     *
     * @return int
     */
    public function getPositiveValue()
    {
        return $this->positiveValue;
    }

    /**
     * Set negativeValue
     *
     * @param integer $negativeValue
     *
     * @return Priority
     */
    public function setNegativeValue($negativeValue)
    {
        $this->negativeValue = $negativeValue;

        return $this;
    }

    /**
     * Get negativeValue
     *
     * @return int
     */
    public function getNegativeValue()
    {
        return $this->negativeValue;
    }

    /**
     * Set totalValue
     *
     * @param integer $totalValue
     *
     * @return Priority
     */
    public function setTotalValue($totalValue)
    {
        $this->totalValue = $totalValue;

        return $this;
    }

    /**
     * Get totalValue
     *
     * @return int
     */
    public function getTotalValue()
    {
        return $this->totalValue;
    }

    /**
     * Set totalValuePercentage
     *
     * @param float $totalValuePercentage
     *
     * @return Priority
     */
    public function setTotalValuePercentage($totalValuePercentage)
    {
        $this->totalValuePercentage = $totalValuePercentage;

        return $this;
    }

    /**
     * Get totalValuePercentage
     *
     * @return float
     */
    public function getTotalValuePercentage()
    {
        return $this->totalValuePercentage;
    }

    /**
     * Set cost
     *
     * @param integer $cost
     *
     * @return Priority
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return int
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set costPercentage
     *
     * @param float $costPercentage
     *
     * @return Priority
     */
    public function setCostPercentage($costPercentage)
    {
        $this->costPercentage = $costPercentage;

        return $this;
    }

    /**
     * Get costPercentage
     *
     * @return float
     */
    public function getCostPercentage()
    {
        return $this->costPercentage;
    }

    /**
     * Set risk
     *
     * @param integer $risk
     *
     * @return Priority
     */
    public function setRisk($risk)
    {
        $this->risk = $risk;

        return $this;
    }

    /**
     * Get risk
     *
     * @return int
     */
    public function getRisk()
    {
        return $this->risk;
    }

    /**
     * Set riskPercentage
     *
     * @param float $riskPercentage
     *
     * @return Priority
     */
    public function setRiskPercentage($riskPercentage)
    {
        $this->riskPercentage = $riskPercentage;

        return $this;
    }

    /**
     * Get riskPercentage
     *
     * @return float
     */
    public function getRiskPercentage()
    {
        return $this->riskPercentage;
    }

    /**
     * Set priority
     *
     * @param float $priority
     *
     * @return Priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return float
     */
    public function getPriority()
    {
        return $this->priority;
    }
}

