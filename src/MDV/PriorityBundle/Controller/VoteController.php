<?php

namespace MDV\PriorityBundle\Controller;

use MDV\PriorityBundle\Entity\Issue;
use MDV\PriorityBundle\Entity\Stakeholder;
use MDV\PriorityBundle\Repository\IssueRepository;
use MDV\PriorityBundle\Service\VoteGridService;
use MDV\PriorityBundle\Service\VotingService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class VoteController
 * @package MDV\PriorityBundle\Controller
 */
class VoteController extends Controller
{
    /**
     * Voting screen
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $stakeholder = $this->getDoctrine()->getRepository('MDVPriorityBundle:Stakeholder')->find($request->query->get('me'));

        /** @var VotingService $votingService */
        $votingService = $this->get('mdv.voting.service');
        if (!$votingService->isOpen()) {
            return $this->render('MDVPriorityBundle:Vote:closed.html.twig', [
                'stakeholder' => $stakeholder
            ]);
        }

        if ($request->isMethod('POST')) {
            /** @var VotingService $votingService */
            $votingService = $this->get('mdv.voting.service');
            try {
                $votingService->hydrateVotes($stakeholder, $request->request->get('votes', []));
            } catch (\RuntimeException $e) {
                throw $e;
            }

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Your votes have been saved!')
            ;
        }

        /** @var VoteGridService $gridService */
        $gridService = $this->get('mdv.vote_grid.service');
        return $this->render('MDVPriorityBundle:Vote:index.html.twig', [
            'grid' => $gridService->getGrid(),
            'me' => $stakeholder
        ]);
    }

    /**
     * Settings screen, open or close voting
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function settingsAction(Request $request)
    {
        /** @var VotingService $votingService */
        $votingService = $this->get('mdv.voting.service');

        if ($request->isMethod('POST')) {
            if ($request->request->get('open')) {
                $votingService->handleOpen();
            } else {
                $votingService->handleClose();
            }
        }

        return $this->render('MDVPriorityBundle:Vote:settings.html.twig', [
            'open' => $votingService->isOpen()
        ]);
    }

    /**
     * Priorities screen
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function prioritiesAction()
    {
        /** @var VotingService $votingService */
        $votingService = $this->get('mdv.voting.service');
        if ($votingService->isOpen()) {
            return $this->render('MDVPriorityBundle:Vote:open.html.twig');
        }

        /** @var IssueRepository $issueRepository */
        $issueRepository = $this->getDoctrine()->getRepository('MDVPriorityBundle:Issue');
        $issues = $issueRepository->findAll();
        usort($issues, function (Issue $i1, Issue $i2) {
            return $i1->getPriority()->getPriority() > $i2->getPriority()->getPriority() ? -1 : 1;
        });

        return $this->render('MDVPriorityBundle:Vote:priorities.html.twig', [
            'issues' => $issues
        ]);

    }
}
