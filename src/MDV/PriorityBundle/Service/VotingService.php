<?php

namespace MDV\PriorityBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use MDV\PriorityBundle\Entity\Issue;
use MDV\PriorityBundle\Entity\Priority;
use MDV\PriorityBundle\Entity\Stakeholder;
use MDV\PriorityBundle\Entity\Vote;
use MDV\PriorityBundle\Repository\IssueRepository;
use MDV\PriorityBundle\Repository\PriorityRepository;
use MDV\PriorityBundle\Repository\SettingsRepository;
use MDV\PriorityBundle\Repository\StakeholderRepository;
use MDV\PriorityBundle\Repository\VoteRepository;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class VotingService
 * @package MDV\PriorityBundle\Service
 */
class VotingService
{
    /** @var SettingsRepository */
    protected $settingsRepository;
    /** @var IssueRepository */
    protected $issueRepository;
    /** @var StakeholderRepository */
    protected $stakeholderRepository;
    /** @var VoteRepository */
    protected $voteRepository;
    /** @var  PriorityRepository */
    protected $priorityRepository;
    /** @var JiraService */
    protected $jiraService;
    /** @var Filesystem */
    protected $fileSystem;
    /** @var string */
    protected $votingOpenFile = 'voting.open';

    /**
     * Constructor
     *
     * @param Registry $doctrineRegistry
     * @param JiraService $jiraService
     */
    public function __construct(
        Registry $doctrineRegistry,
        JiraService $jiraService,
        Filesystem $fileSystem
    ) {
        $this->settingsRepository = $doctrineRegistry->getRepository('MDVPriorityBundle:Settings');
        $this->issueRepository = $doctrineRegistry->getRepository('MDVPriorityBundle:Issue');
        $this->voteRepository = $doctrineRegistry->getRepository('MDVPriorityBundle:Vote');
        $this->stakeholderRepository = $doctrineRegistry->getRepository('MDVPriorityBundle:Stakeholder');
        $this->priorityRepository = $doctrineRegistry->getRepository('MDVPriorityBundle:Priority');
        $this->jiraService = $jiraService;
        $this->fileSystem = $fileSystem;
    }

    /**
     * Handle open voting
     *
     * @return void
     */
    public function handleOpen()
    {
        // Check if voting is already open
        if ($this->fileSystem->exists($this->votingOpenFile)) {
            throw new \BadMethodCallException('Voting is already opened');
        }

        // Retrieve all issues up for vote
        $issueKeys = $this->jiraService->retrieveUpForVote();

        // Delete all issues not up for vote anymore
        $this->issueRepository->deleteOtherKeys($issueKeys);

        // Find all issues in our storage and compare them with JIRA issues
        /** @var Issue[] $issues */
        $issues = $this->issueRepository->findAll();
        foreach($issues as $issue) {
            $key = array_search($issue->getJiraKey(), $issueKeys);
            if (false !== $key) {
                unset($issueKeys[$key]);
            }
        }

        // Insert issues not in our storage
        foreach($issueKeys as $key) {
            $issue = new Issue();
            $issue->setJiraKey($key);
            $issue->setSummary($this->jiraService->getSummary($key));
            $this->issueRepository->persist($issue);
        }
        $this->issueRepository->flush();

        // Get total votes
        $totalVotesPrevious = $this->voteRepository->getTotalVotes();

        // Set total allowed votes per stakeholder
        $stakeholders = $this->stakeholderRepository->findAll();
        $addedVotes = (10 * count($stakeholders) - $totalVotesPrevious) / count($stakeholders);
        /** @var Stakeholder $stakeholder */
        foreach($stakeholders as $stakeholder) {
            $previousVotes = 0;
            foreach($stakeholder->getVotes() as $vote) {
                $previousVotes += $vote->getVote();
            }
            $stakeholder->setAllowedVotes(floor($addedVotes * $stakeholder->getRatio() + $previousVotes));
            $this->stakeholderRepository->persist($stakeholder);
        }
        $this->stakeholderRepository->flush();

        // Open voting by placing file
        $this->fileSystem->touch($this->votingOpenFile);

        return;
    }

    /**
     * Close voting
     *
     * @return void
     */
    public function handleClose()
    {
        $issues = $this->issueRepository->findAll();
        $priorities = [];
        $sumTotalValue = $sumRisk = $sumCost = 0;

        // Hydrate fields from JIRA + make sums
        foreach($issues as $issue) {
            $priority = $this->jiraService->hydratePriority($issue);
            $priority->setPositiveValue($this->voteRepository->getTotalVotes($issue));
            $priority->setTotalValue((int)$priority->getPositiveValue() + (int)$priority->getNegativeValue());

            $sumTotalValue += (int)$priority->getTotalValue();
            $sumRisk += (int)$priority->getRisk();
            $sumCost += (int)$priority->getCost();

            $priorities[] = $priority;
        }

        /** @var Priority $priority */
        foreach($priorities as $priority) {
            $totalValuePercentage = ($sumTotalValue > 0 ? (int)$priority->getTotalValue()/ $sumTotalValue * 100 : 0);
            $priority->setTotalValuePercentage($totalValuePercentage);

            $costPercentage = ($sumCost > 0 ? (int)$priority->getCost() / $sumCost * 100 : 0);
            $priority->setCostPercentage($costPercentage);

            $riskPercentage = ($sumRisk > 0 ? (int)$priority->getRisk() / $sumRisk * 100 : 0);
            $priority->setRiskPercentage($riskPercentage);

            $priority->setPriority(
                $priority->getTotalValuePercentage() /
                ($priority->getCostPercentage() + $priority->getRiskPercentage() * 0.5)
            );
            $this->priorityRepository->persist($priority);
        }

        $this->priorityRepository->flush();

        $this->fileSystem->remove($this->votingOpenFile);

        return;
    }

    /**
     * Check if voting is open
     *
     * @return bool
     */
    public function isOpen()
    {
        return $this->fileSystem->exists($this->votingOpenFile);
    }

    /**
     * @param Stakeholder $stakeholder
     * @param $votes
     */
    public function hydrateVotes(Stakeholder $stakeholder, $votes)
    {
        if ($stakeholder->getAllowedVotes() !== array_sum($votes)) {
            throw new \RuntimeException('Amount of votes not allowed');
        }

        foreach($votes as $key => $value) {
            $issue = $this->issueRepository->findOneBy(['jiraKey' => $key]);
            $vote = $this->voteRepository->findOneBy([
                'issue' => $issue,
                'stakeholder' => $stakeholder
            ]);

            if (null === $vote) {
                $vote = new Vote();
                $vote->setIssue($issue);
                $vote->setStakeholder($stakeholder);
            }

            $vote->setVote($value);
            $this->voteRepository->persist($vote);
        }
        $this->voteRepository->flush();
    }
}
