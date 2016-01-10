<?php

namespace MDV\PriorityBundle\Service;

use Doctrine\ORM\EntityRepository;
use JiraApiBundle\Service\IssueService;
use JiraApiBundle\Service\SearchService;

/**
 * Class JiraService
 * @package MDV\PriorityBundle\Service
 */
class JiraService
{
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
}
