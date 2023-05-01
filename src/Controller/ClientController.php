<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'client')]
    public function index(ClientRepository $repository): Response
    {
        // créer le formulaire et l'objet
        $client = new Client();
        $formCreation = $this->createForm(ClientType::class, $client);

        // lire les clients
        $lesClients = $repository->findAll();

        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
            'formCreation' => $formCreation->createView(),
            'lesClients' => $lesClients,
        ]);
    }

    #[Route('/client/ajouter', name: 'client_ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager, Client $client=null): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // cas où le formulaire d'ajout a été soumis par l'utilisateur et est valide
            $client = $form->getData();
            // on met à jour la base de données 
            $entityManager->persist($client);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le client ' . $client->getNom() . ' ' .$client->getPrenom() . ' a été ajouté.'
            );
            return $this->redirectToRoute('client');
        } else {
            // cas où l'utilisateur a demandé l'ajout, on affiche le formulaire d'ajout
            return $this->render('client/ajouter.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }


    #[Route('/client/modifier/{id<\d+>}', name: 'client_modifier')]
    public function modifier(Request $request, EntityManagerInterface $entityManager, Client $client = null): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // cas où le formulaire  a été soumis par l'utilisateur et est valide
            //pas besoin de "persister" l'entité : en effet, l'objet a déjà été retrouvé à partir de Doctrine ORM.
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le client '.$client->getNom() . ' ' . $client->getPrenom() . ' a été modifié.'
            );

            return $this->redirectToRoute('client');
        }
        // cas où l'utilisateur a demandé la modification, on affiche le formulaire pour la modification
        return $this->render('client/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/client/supprimer/{id<\d+>}', name: 'client_supprimer')]
    public function supprimer(Request $request, EntityManagerInterface $entityManager, Client $client): Response
    {
        if ($this->isCsrfTokenValid('action-item'.$client->getId(), $request->get('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le client ' . $client->getNom() . ' ' . $client->getPrenom() . ' a été supprimé.'
            );
            return $this->redirectToRoute('client');
        }
    }
}
