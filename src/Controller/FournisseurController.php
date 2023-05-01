<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use App\Form\FournisseurType;
use App\Repository\FournisseurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class FournisseurController extends AbstractController
{
    #[Route('/fournisseur', name: 'fournisseur')]
    public function index(FournisseurRepository $repository): Response
    {
        // créer le formulaire et l'objet
        $fournisseur = new Fournisseur();
        $formCreation = $this->createForm(FournisseurType::class, $fournisseur);

        // lire les fournisseurs
        $lesFournisseurs = $repository->findAll();

        return $this->render('fournisseur/index.html.twig', [
            'controller_name' => 'FournisseurController',
            'formCreation' => $formCreation->createView(),
            'lesFournisseurs' => $lesFournisseurs,
        ]);
    }

    #[Route('/fournisseur/ajouter', name: 'fournisseur_ajouter')]

    public function ajouter(Request $request, EntityManagerInterface $entityManager, Fournisseur $fournisseur=null): Response
    {
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // cas où le formulaire d'ajout a été soumis par l'utilisateur et est valide
            $fournisseur = $form->getData();
            // on met à jour la base de données 
            $entityManager->persist($fournisseur);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le fournisseur ' . $fournisseur->getNom() . ' a été ajouté.'
            );
            return $this->redirectToRoute('fournisseur');
        } else {
            // cas où l'utilisateur a demandé l'ajout, on affiche le formulaire d'ajout
            return $this->render('fournisseur/ajouter.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    #[Route('/fournisseur/modifier/{id<\d+>}', name: 'fournisseur_modifier')]

    public function modifier(Request $request, EntityManagerInterface $entityManager, Fournisseur $fournisseur = null): Response
    {
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // cas où le formulaire  a été soumis par l'utilisateur et est valide
            //pas besoin de "persister" l'entité : en effet, l'objet a déjà été retrouvé à partir de Doctrine ORM.
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le fournisseur '.$fournisseur->getNom() . ' a été modifié.'
            );

            return $this->redirectToRoute('fournisseur');
        }
        // cas où l'utilisateur a demandé la modification, on affiche le formulaire pour la modification
        return $this->render('fournisseur/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/fournisseur/supprimer/{id<\d+>}', name: 'fournisseur_supprimer')]

    public function supprimer(Request $request, EntityManagerInterface $entityManager, Fournisseur $fournisseur): Response
    {
        if ($this->isCsrfTokenValid('action-item'.$fournisseur->getId(), $request->get('_token'))) {
            $entityManager->remove($fournisseur);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le fournisseur ' . $fournisseur->getNom() .  ' a été supprimé.'
            );
            return $this->redirectToRoute('fournisseur');
        }
    }

}
