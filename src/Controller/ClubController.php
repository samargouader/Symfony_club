<?php

namespace App\Controller;

use App\Entity\Joueur;
use App\Form\JoueurType;
use App\Repository\ClubRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClubController extends AbstractController
{

    #[Route('/club/list', name: 'app_club_list')]
    public function clubList(ClubRepository $clubRepository): Response
    {
        $clubs = $clubRepository->findAll();
        return $this->render('club/clubList.html.twig', [
            'clubs' => $clubs,
        ]);
    }

    #[Route('/club/{id}/addPlayer', name: 'app_add_player')]
    public function addPlayer(int $id,Request $request, ClubRepository $clubRepository, ManagerRegistry $doctrine): Response
    {
        $club =$clubRepository->find($id);
        $player = new Joueur();
        $player->setClub($club);
        $form = $this->createForm(JoueurType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em=$doctrine->getManager();
            $em->persist($player);
            $em->flush();
            return $this->redirectToRoute('app_club_list');
        }

        return $this->render('club/addPlayer.html.twig', ['form'=>$form->createView(), 'club' => $club]);
    }

    #[Route('/club/{id}/removePlayer', name: 'app_remove_player')]
    public function removePlayer(int $id, ClubRepository $clubRepository, ManagerRegistry $doctrine): Response
    {
        $club = $clubRepository->find($id);

        $player = $club->getJoueurs()->first();

        if ($player) {
            $em = $doctrine->getManager();
            $em->remove($player);
            $em->flush(); 
        }

        return $this->redirectToRoute('app_club_list');
    }
}
