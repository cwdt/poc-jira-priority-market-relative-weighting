<?php

namespace MDV\PriorityBundle\Controller;

use MDV\PriorityBundle\Entity\Stakeholder;
use MDV\PriorityBundle\Service\VoteGridService;
use MDV\PriorityBundle\Service\VotingService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class VoteController extends Controller
{
    public function indexAction(Request $request)
    {
        // TODO insert flashmessages (session flashbag)

        $stakeholder = $this->getDoctrine()->getRepository('MDVPriorityBundle:Stakeholder')->find($request->query->get('me'));

        if ($request->isMethod('POST')) {
            /** @var VotingService $votingService */
            $votingService = $this->get('mdv.voting.service');
            try {
                $votingService->hydrateVotes($stakeholder, $request->request->get('votes', []));
            } catch (\RuntimeException $e) {
                throw $e;
            }
        }

        /** @var VoteGridService $gridService */
        $gridService = $this->get('mdv.vote_grid.service');
        return $this->render('MDVPriorityBundle:Default:index.html.twig', [
            'grid' => $gridService->getGrid(),
            'me' => $stakeholder->getId()
        ]);
    }


//        /** @var VotingService $votingService */
//        $votingService = $this->get('mdv.voting.service');
//        $votingService->handleClose();
//        $votingService->handleOpen();
//        die;
}
