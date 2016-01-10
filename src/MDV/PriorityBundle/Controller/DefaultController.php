<?php

namespace MDV\PriorityBundle\Controller;

use MDV\PriorityBundle\Service\VoteGridService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
//        /** @var VotingService $votingService */
//        $votingService = $this->get('mdv.voting.service');
//        $votingService->handleClose();
//        $votingService->handleOpen();
//        die;

        /** @var VoteGridService $gridService */
        $gridService = $this->get('mdv.vote_grid.service');
        return $this->render('MDVPriorityBundle:Default:index.html.twig', [
            'grid' => $gridService->getGrid(),
            'me' => 2,
        ]);
    }
}
