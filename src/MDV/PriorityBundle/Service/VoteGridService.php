<?php

namespace MDV\PriorityBundle\Service;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\PersistentCollection;
use MDV\PriorityBundle\Entity\Issue;
use MDV\PriorityBundle\Entity\Stakeholder;
use MDV\PriorityBundle\Entity\Vote;
use MDV\PriorityBundle\Repository\IssueRepository;

/**
 * Class VoteGridService
 * @package MDV\PriorityBundle\Service
 */
class VoteGridService
{
    /** @var IssueRepository */
    protected $issueRepository;
    protected $stakeHolderRepository;

    /**
     * @param Registry $doctrineRegistry
     */
    public function __construct(Registry $doctrineRegistry)
    {
        $this->issueRepository = $doctrineRegistry->getRepository('MDVPriorityBundle:Issue');
        $this->stakeHolderRepository = $doctrineRegistry->getRepository('MDVPriorityBundle:Stakeholder');
    }

    /**
     * @return array
     */
    public function getGrid()
    {
        $issueGrid = [];
        /** @var Issue $issue */
        foreach($this->issueRepository->findAll() as $issue) {
            $issueGrid[$issue->getId()]['key'] = $issue->getJiraKey();
            $issueGrid[$issue->getId()]['summary'] = $issue->getSummary();

            /** @var PersistentCollection $votes */
            $votes = $issue->getVotes();

            /** @var Stakeholder $stakeholder */
            foreach ($this->stakeHolderRepository->findAll() as $stakeholder) {
                $vote = $votes->matching(Criteria::create()->where(Criteria::expr()->eq('stakeholder', $stakeholder)))->first();
                $vote = $vote ? $vote->getVote() : 0;
                $issueGrid[$issue->getId()]['stakeholders'][$stakeholder->getId()] = $vote;
            }
        }

        $stakeholderGrid = [];
        foreach($this->stakeHolderRepository->findAll() as $stakeholder) {
            $stakeholderGrid[$stakeholder->getId()] = [
                'name' => $stakeholder->getName(),
                'allowedVotes' => $stakeholder->getAllowedVotes()
            ];
        }

        return ['stakeholders' => $stakeholderGrid, 'issues' => $issueGrid];
    }
}