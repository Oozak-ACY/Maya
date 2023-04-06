<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Client;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ClientRepository;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'client')]
    #[Route('/client/demandermodification/{id<\d+>}', name: 'client_demandermodification')]
    public function index(ClientRepository $repository, Request $request, $id = null): Response
    {

        // créer l'objet et le formulaire de création
        $client = new Client();
        $formCreation = $this->createForm(ClientType::class, $client);

        // si 2e route alors $id est renseigné et on  crée le formulaire de modification
        $formModificationView = null;
        if ($id != null) {
            // sécurité supplémentaire, on vérifie le token
            if ($this->isCsrfTokenValid('action-item'.$id, $request->get('_token'))) {
                $clientModif = $repository->find($id);   // la catégorie à modifier
                $formModificationView = $this->createForm(ClientType::class, $clientModif)->createView();
            }
        }

        
        // lire les clients
        $lesClients = $repository->findAll();
        return $this->render('client/index.html.twig', [
            'formCreation' => $formCreation->createView(),
            'lesClients' => $lesClients,
            'formModification' => $formModificationView,
            'idClientModif' => $id,
        ]);

    }

    #[Route('/client/ajouter', name: 'client_ajouter')]
    public function ajouter(Client $client = null, Request $request, EntityManagerInterface $entityManager, ClientRepository $repository)
    {
        //  $client objet de la classe client, il contiendra les valeurs saisies dans les champs après soumission du formulaire.
        //  $request  objet avec les informations de la requête HTTP (GET, POST, ...)
        //  $entityManager  pour la persistance des données

        // création d'un formulaire de type clientType
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);

        // handleRequest met à jour le formulaire
        //  si le formulaire a été soumis, handleRequest renseigne les propriétés
        //      avec les données saisies par l'utilisateur et retournées par la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // c'est le cas du retour du formulaire
            //         l'objet $client a été automatiquement "hydraté" par Doctrine
            // dire à Doctrine que l'objet sera (éventuellement) persisté
            $entityManager->persist($client);
            // exécuter les requêtes (indiquées avec persist) ici il s'agit de l'ordre INSERT qui sera exécuté
            $entityManager->flush();
            // ajouter un message flash de succès pour informer l'utilisateur
            $this->addFlash(
                'success',
                'Le client ' . $client->getNom() . ' ' . $client->getPrenom() . ' a été ajouté'
            );
            // rediriger vers l'affichage des catégories qui comprend le formulaire pour l"ajout d'une nouvelle catégorie
            return $this->redirectToRoute('client');

        } else {
    // affichage de la liste des catégories avec le formulaire de création et ses erreurs
            // lire les catégories
            $lesclients = $repository->findAll();
            // rendre la vue
            return $this->render('client/index.html.twig', [
                'formCreation' => $form->createView(),
                'lesclients' => $lesclients,
                'formModification' => null,
                'idclientModif' => null,
            ]);
        }
    }

    #[Route('/client/modifier/{id<\d+>}', name: 'client_modifier')]
    public function modifier(Client $client = null, $id = null, Request $request, EntityManagerInterface $entityManager, ClientRepository $repository)
    {
        //  Symfony 4 est capable de retrouver la client à l'aide de Doctrine ORM directement en utilisant l'id passé dans la route
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // va effectuer la requête d'UPDATE en base de données
            // pas besoin de "persister" l'entité car l'objet a déjà été retrouvé à partir de Doctrine ORM.
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le client ' . $client->getnom() . ' '. $client->getprenom() . ' a été modifié.'
            );
            // rediriger vers l'affichage des clients qui comprend le formulaire pour l"ajout d'une nouvelle client
            return $this->redirectToRoute('client');

        } else {
            // affichage de la liste des clients avec le formulaire de modification et ses erreurs
            // créer l'objet et le formulaire de création
            $client = new Client();
            $formCreation = $this->createForm(ClientType::class, $client);
            // lire les clients
            $lesClients = $repository->findAll();
            // rendre la vue
            return $this->render('client/index.html.twig', [
                'formCreation' => $formCreation->createView(),
                'lesClients' => $lesClients,
                'formModification' => $form->createView(),
                'idClientModif' => $id,
            ]);
        }
    }

    #[Route('/client/supprimer/{id<\d+>}', name: 'client_supprimer')]
    public function supprimer(Client $client = null, Request $request, EntityManagerInterface $entityManager)
    {
        // vérifier le token
        if ($this->isCsrfTokenValid('action-item'.$client->getId(), $request->get('_token'))) {
            // supprimer la catégorie
            $entityManager->remove($client);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le client ' . $client->getnom() . ' '. $client->getprenom() . ' a été supprimé.'
            );
        }
        return $this->redirectToRoute('client');
    }



}
