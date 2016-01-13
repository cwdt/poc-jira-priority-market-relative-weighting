<?php

namespace MDV\PriorityBundle\Service;

use Doctrine\ORM\EntityRepository;
use JiraApiBundle\Service\IssueService;
use JiraApiBundle\Service\SearchService;
use MDV\PriorityBundle\Entity\Issue;
use MDV\PriorityBundle\Entity\Priority;

/**
 * Class JiraService
 * @package MDV\PriorityBundle\Service
 */
class JiraService
{
    /** TODO make configurable */
    const COST_FIELD = 'customfield_10002';
    const RISK_FIELD = 'customfield_10010';
    const NEGATIVE_VALUE_FIELD = 'customfield_10011';

    /**
     * @var SearchService
     */
    private $searchService;

    /** @var IssueService */
    private $issueService;

    /** @var array */
    private $issueCache;

    /**
     * @param SearchService $searchService
     * @param IssueService $issueService
     */
    public function __construct(
        SearchService $searchService,
        IssueService $issueService
    ) {
        $this->searchService = $searchService;
        $this->issueService = $issueService;
    }

    /**
     * Retrieve issues currently up for vote
     *
     * @return array issue keys
     */
    public function retrieveUpForVote()
    {
        $issues = $this->searchService->search([
            'jql' => 'project = POR AND issuetype in (Bug, Story) AND status = "To Do" AND "Up for vote" = Yes'
        ]);

        $list = [];
        foreach($issues['issues'] as $issue) {
            $list[] = $issue['key'];
            $this->issueCache[$issue['key']] = $issue;
        }

        return $list;
    }

    /**
     * @param $key
     * @return array
     */
    public function getSummary($key)
    {
        if (!isset($this->issueCache[$key])) {
            $this->issueCache[$key] = $this->issueService->get($key);
        }

        if (!$this->issueCache[$key]) {
            throw new \BadMethodCallException('Issue not found');
        }

        return $this->issueCache[$key]['fields']['summary'];
    }

    /**
     * @param Issue $issue
     * @return Priority
     */
    public function hydratePriority(Issue $issue)
    {
        $jiraIssue = $this->issueService->get($issue->getJiraKey());

        // TODO check if issue still exists

        if (!$priority = $issue->getPriority()) {
            $priority = new Priority();
        }
        $priority->setIssue($issue);
        $priority->setCost((int)$jiraIssue['fields'][self::COST_FIELD]['value']);
        $priority->setNegativeValue((int)$jiraIssue['fields'][self::NEGATIVE_VALUE_FIELD]['value']);
        $priority->setRisk((int)$jiraIssue['fields'][self::RISK_FIELD]['value']);
        return $priority;
    }
}
