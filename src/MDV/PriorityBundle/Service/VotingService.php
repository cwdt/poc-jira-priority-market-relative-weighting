<?php

namespace MDV\PriorityBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use MDV\PriorityBundle\Entity\Issue;
use MDV\PriorityBundle\Entity\Stakeholder;
use MDV\PriorityBundle\Repository\IssueRepository;
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
        $this->stakeholderRepository = $doctrineRegistry->getRepository('MDVPriorityBundle:Stakeholder');;
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
}