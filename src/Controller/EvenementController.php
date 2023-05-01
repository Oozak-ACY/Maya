<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'evenement')]
    public function index(EvenementRepository $repository): Response
    {
        // créer l'objet et le formulaire
        $evenement = new Evenement();
        $formCreation = $this->createForm(EvenementType::class, $evenement);

        // lire les évènements
        $lesEvenements = $repository->findAll();

        return $this->render('evenement/index.html.twig', [
            'controller_name' => 'EvenementController',
            'formCreation' => $formCreation->createView(),
            'lesEvenements' => $lesEvenements,
        ]);
    }

    #[Route('/evenement/ajouter', name: 'evenement_ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager, Evenement $evenement=null): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // cas où le formulaire d'ajout a été soumis par l'utilisateur et est valide
            $evenement = $form->getData();
            // on met à jour la base de données 
            $entityManager->persist($evenement);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'L\'évènement ' . $evenement->getTitre() . ' a été ajouté.'
            );
            return $this->redirectToRoute('evenement');
        } else {
            // cas où l'utilisateur a demandé l'ajout, on affiche le formulaire d'ajout
            return $this->render('evenement/ajouter.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    #[Route('/evenement/modifier/{id<\d+>}', name: 'evenement_modifier')]
    public function modifier(Request $request, EntityManagerInterface $entityManager, Evenement $evenement = null): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // cas où le formulaire  a été soumis par l'utilisateur et est valide
            //pas besoin de "persister" l'entité : en effet, l'objet a déjà été retrouvé à partir de Doctrine ORM.
            $entityManager->flush();
            $this->addFlash(
                'success',
                'L\'évènement '.$evenement->getTitre().' a été modifié.'
            );

            return $this->redirectToRoute('evenement');
        }
        // cas où l'utilisateur a demandé la modification, on affiche le formulaire pour la modification
        return $this->render('evenement/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/evenement/supprimer/{id<\d+>}', name: 'evenement_supprimer')]
    public function supprimer(Request $request, EntityManagerInterface $entityManager, Evenement $evenement): Response
    {
        if ($this->isCsrfTokenValid('action-item'.$evenement->getId(), $request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'L\'évènement '.$evenement->getTitre().' a été supprimé.'
            );
            return $this->redirectToRoute('evenement');
        }
    }

}
