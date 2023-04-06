<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Evenement;
use App\Form\EvenementType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EvenementRepository;

class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'evenement')]
    #[Route('/evenement/demandermodification/{id<\d+>}', name: 'evenement_demandermodification')]
    public function index(EvenementRepository $repository, Request $request, $id = null): Response
    {

        // créer l'objet et le formulaire de création
        $evenement = new Evenement();
        $formCreation = $this->createForm(EvenementType::class, $evenement);

        // si 2e route alors $id est renseigné et on  crée le formulaire de modification
        $formModificationView = null;
        if ($id != null) {
            // sécurité supplémentaire, on vérifie le token
            if ($this->isCsrfTokenValid('action-item'.$id, $request->get('_token'))) {
                $evenementModif = $repository->find($id);   // la catégorie à modifier
                $formModificationView = $this->createForm(EvenementType::class, $evenementModif)->createView();
            }
        }

        
        // lire les evenements
        $lesEvenements = $repository->findAll();
        return $this->render('evenement/index.html.twig', [
            'formCreation' => $formCreation->createView(),
            'lesEvenements' => $lesEvenements,
            'formModification' => $formModificationView,
            'idEvenementModif' => $id,
        ]);

    }

    #[Route('/evenement/ajouter', name: 'evenement_ajouter')]
    public function ajouter(evenement $evenement = null, Request $request, EntityManagerInterface $entityManager, evenementRepository $repository)
    {
        //  $evenement objet de la classe evenement, il contiendra les valeurs saisies dans les champs après soumission du formulaire.
        //  $request  objet avec les informations de la requête HTTP (GET, POST, ...)
        //  $entityManager  pour la persistance des données

        // création d'un formulaire de type EvenementType
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);

        // handleRequest met à jour le formulaire
        //  si le formulaire a été soumis, handleRequest renseigne les propriétés
        //      avec les données saisies par l'utilisateur et retournées par la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // c'est le cas du retour du formulaire
            //         l'objet $evenement a été automatiquement "hydraté" par Doctrine
            // dire à Doctrine que l'objet sera (éventuellement) persisté
            $entityManager->persist($evenement);
            // exécuter les requêtes (indiquées avec persist) ici il s'agit de l'ordre INSERT qui sera exécuté
            $entityManager->flush();
            // ajouter un message flash de succès pour informer l'utilisateur
            $this->addFlash(
                'success',
                'L\'evenement ' . $evenement->gettitre() . ' a été ajouté'
            );
            // rediriger vers l'affichage des catégories qui comprend le formulaire pour l"ajout d'une nouvelle catégorie
            return $this->redirectToRoute('evenement');

        } else {
    // affichage de la liste des catégories avec le formulaire de création et ses erreurs
            // lire les catégories
            $lesEvenements = $repository->findAll();
            // rendre la vue
            return $this->render('evenement/index.html.twig', [
                'formCreation' => $form->createView(),
                'lesEvenements' => $lesEvenements,
                'formModification' => null,
                'idEvenementModif' => null,
            ]);
        }
    }

    #[Route('/evenement/modifier/{id<\d+>}', name: 'evenement_modifier')]
    public function modifier(evenement $evenement = null, $id = null, Request $request, EntityManagerInterface $entityManager, evenementRepository $repository)
    {
        //  Symfony 4 est capable de retrouver la evenement à l'aide de Doctrine ORM directement en utilisant l'id passé dans la route
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // va effectuer la requête d'UPDATE en base de données
            // pas besoin de "persister" l'entité car l'objet a déjà été retrouvé à partir de Doctrine ORM.
            $entityManager->flush();
            $this->addFlash(
                'success',
                'L\'evenement ' . $evenement->gettitre() . ' a été modifié.'
            );
            // rediriger vers l'affichage des evenements qui comprend le formulaire pour l"ajout d'une nouvelle evenement
            return $this->redirectToRoute('evenement');

        } else {
            // affichage de la liste des evenements avec le formulaire de modification et ses erreurs
            // créer l'objet et le formulaire de création
            $evenement = new Evenement();
            $formCreation = $this->createForm(EvenementType::class, $evenement);
            // lire les evenements
            $lesEvenements = $repository->findAll();
            // rendre la vue
            return $this->render('evenement/index.html.twig', [
                'formCreation' => $formCreation->createView(),
                'lesEvenements' => $lesEvenements,
                'formModification' => $form->createView(),
                'idEvenementModif' => $id,
            ]);
        }
    }

    #[Route('/evenement/supprimer/{id<\d+>}', name: 'evenement_supprimer')]
    public function supprimer(evenement $evenement = null, Request $request, EntityManagerInterface $entityManager)
    {
        // vérifier le token
        if ($this->isCsrfTokenValid('action-item'.$evenement->getId(), $request->get('_token'))) {
            // supprimer la catégorie
            $entityManager->remove($evenement);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'L\'evenement ' . $evenement->gettitre() . ' a été supprimé.'
            );
        }
        return $this->redirectToRoute('evenement');
    }
}