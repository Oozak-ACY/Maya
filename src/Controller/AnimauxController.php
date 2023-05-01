<?php

namespace App\Controller;

use App\Repository\AnimauxRepository;
use App\Entity\Animaux;
use App\Entity\RacesAnimaux;
use \DateTimeInterface;
use App\Form\AnimauxType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class AnimauxController extends AbstractController
{

    #[Route('/animaux', name: 'animaux')]

    public function index(AnimauxRepository $repository): Response
    {
        // créer l'objet et le formulaire de création
        $animaux = new Animaux();
        $formCreation = $this->createForm(AnimauxType::class, $animaux);

        // lire les animaux
        $lesAnimaux = $repository->findAll();
        return $this->render('animaux/index.html.twig', [
            'formCreation' => $formCreation->createView(),
            'lesAnimaux' => $lesAnimaux,
        ]);
    }

    #[Route('/animaux/ajouter', name: 'animaux_ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager, Animaux $animaux=null): Response
    {
        $form = $this->createForm(AnimauxType::class, $animaux);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // cas où le formulaire d'ajout a été soumis par l'utilisateur et est valide
            $animaux = $form->getData();
            // on met à jour la base de données 
            $entityManager->persist($animaux);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'L\'animal ' . $animaux->getNom() . ' a été ajouté.'
            );
            return $this->redirectToRoute('animaux');
        } else {
            // cas où l'utilisateur a demandé l'ajout, on affiche le formulaire d'ajout
            return $this->render('animaux/ajouter.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    #[Route('/animaux/modifier/{id<\d+>}', name: 'animaux_modifier')]
    public function modifier(Request $request, EntityManagerInterface $entityManager, Animaux $animaux = null): Response
    {
        $form = $this->createForm(AnimauxType::class, $animaux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // cas où le formulaire  a été soumis par l'utilisateur et est valide
            //pas besoin de "persister" l'entité : en effet, l'objet a déjà été retrouvé à partir de Doctrine ORM.
            $entityManager->flush();
            $this->addFlash(
                'success',
                'L\'animal '.$animaux->getNom().' a été modifié.'
            );

            return $this->redirectToRoute('animaux');
        }
        // cas où l'utilisateur a demandé la modification, on affiche le formulaire pour la modification
        return $this->render('animaux/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/animaux/supprimer/{id<\d+>}', name: 'animaux_supprimer')]
    public function supprimer(Request $request, EntityManagerInterface $entityManager, Animaux $animaux): Response
    {
        if ($this->isCsrfTokenValid('action-item'.$animaux->getId(), $request->get('_token'))) {
            $entityManager->remove($animaux);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'L\'animal '.$animaux->getNom().' a été supprimé.'
            );
            return $this->redirectToRoute('animaux');
        }
    }





}
