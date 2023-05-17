<?php

namespace App\Controller;

use App\Entity\RacesAnimaux;
use App\Form\RacesAnimauxType;
use App\Repository\RacesAnimauxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RacesAnimauxController extends AbstractController
{
    #[Route('/races_animaux', name: 'races_animaux')]
    #[Route('/races_animaux/demandermodification/{id<\d+>}', name: 'races_animaux_demandermodification')]

    public function index(RacesAnimauxRepository $repository, Request $request, $id = null): Response
    {

        $racesAnimaux = new RacesAnimaux();
        $formCreation = $this->createForm(RacesAnimauxType::class, $racesAnimaux);

        // si 2e route alors $id est renseigné et on  crée le formulaire de modification
        $formModificationView = null;
        if ($id != null) {
            // sécurité supplémentaire, on vérifie le token
            if ($this->isCsrfTokenValid('action-item' . $id, $request->get('_token'))) {
                $racesAnimauxModif = $repository->find($id);   // la catégorie à modifier
                $formModificationView = $this->createForm(RacesAnimauxType::class, $racesAnimauxModif)->createView();
            }
        }


        // si 2e route alors $id est renseigné et on  crée le formulaire de modification
        if ($id != null) {
            $racesAnimauxModif = $repository->find($id);   // la catégorie à modifier
            $formModificationView = $this->createForm(RacesAnimauxType::class, $racesAnimauxModif)->createView();
        } else {
            $formModificationView = null;
        }

        $lesRacesAnimaux = $repository->findAll();
        return $this->render('races_animaux/index.html.twig', [
            'formCreation' => $formCreation->createView(),
            'lesRacesAnimaux' => $lesRacesAnimaux,
            'formModification' => $formModificationView,
            'idRacesAnimauxModif' => $id,
        ]);
    }

    #[Route('/races_animaux/ajouter', name: 'races_animaux_ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager, RacesAnimauxRepository $repository, RacesAnimaux $racesAnimaux = null)
    {
        //  $racesAnimaux objet de la classe Categorie, il contiendra les valeurs saisies dans les champs après soumission du formulaire.
        //  $request  objet avec les informations de la requête HTTP (GET, POST, ...)
        //  $entityManager  pour la persistance des données

        // création d'un formulaire de type CategorieType
        $racesAnimaux = new RacesAnimaux();
        $form = $this->createForm(RacesAnimauxType::class, $racesAnimaux);

        // handleRequest met à jour le formulaire
        //  si le formulaire a été soumis, handleRequest renseigne les propriétés
        //      avec les données saisies par l'utilisateur et retournées par la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // c'est le cas du retour du formulaire
            //         l'objet $racesAnimaux a été automatiquement "hydraté" par Doctrine
            // dire à Doctrine que l'objet sera (éventuellement) persisté
            $entityManager->persist($racesAnimaux);
            // exécuter les requêtes (indiquées avec persist) ici il s'agit de l'ordre INSERT qui sera exécuté
            $entityManager->flush();
            // ajouter un message flash de succès pour informer l'utilisateur
            $this->addFlash(
                'success',
                'La race ' . $racesAnimaux->getLibelle() . ' a été ajoutée.'
            );
            // rediriger vers l'affichage des catégories qui comprend le formulaire pour l"ajout d'une nouvelle catégorie
            return $this->redirectToRoute('races_animaux');
        } else {
            // affichage de la liste des catégories avec le formulaire de création et ses erreurs
            // lire les catégories
            $lesRacesAnimaux = $repository->findAll();
            // rendre la vue
            return $this->render('races_animaux/index.html.twig', [
                'formCreation' => $form->createView(),
                'lesRacesAnimaux' => $lesRacesAnimaux,
                'formModification' => null,
                'idRacesAnimauxModif' => null,
            ]);
        }
    }

    #[Route('//races_animaux/modifier/{id<\d+>}', name: 'races_animaux_modifier')]
   public function modifier(Request $request, EntityManagerInterface $entityManager, RacesAnimauxRepository $repository, RacesAnimaux $racesAnimaux = null, $id = null)
    {
        //  Symfony 4 est capable de retrouver la catégorie à l'aide de Doctrine ORM directement en utilisant l'id passé dans la route
        $form = $this->createForm(RacesAnimauxType::class, $racesAnimaux);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // va effectuer la requête d'UPDATE en base de données
            // pas besoin de "persister" l'entité car l'objet a déjà été retrouvé à partir de Doctrine ORM.
            $entityManager->flush();
            $this->addFlash(
                'success',
                'La race ' . $racesAnimaux->getLibelle() . ' a été modifiée.'
            );
            // rediriger vers l'affichage des catégories qui comprend le formulaire pour l"ajout d'une nouvelle catégorie
            return $this->redirectToRoute('races_animaux');
        } else {
            // affichage de la liste des catégories avec le formulaire de modification et ses erreurs
            // créer l'objet et le formulaire de création
            $racesAnimaux = new RacesAnimaux();
            $formCreation = $this->createForm(RacesAnimauxType::class, $racesAnimaux);
            // lire les catégories
            $lesRacesAnimaux = $repository->findAll();
            // rendre la vue
            return $this->render('races_animaux/index.html.twig', [
                'formCreation' => $formCreation->createView(),
                'lesRacesAnimaux' => $lesRacesAnimaux,
                'formModification' => $form->createView(),
                'idCategorieModif' => $id,
            ]);
        }
    }


    #[Route('/races_animaux/supprimer/{id<\d+>}', name: 'races_animaux_supprimer')]

    public function supprimer(Request $request, EntityManagerInterface $entityManager, RacesAnimaux $racesAnimaux = null)
    {
        // vérifier le token
        if ($this->isCsrfTokenValid('action-item' . $racesAnimaux->getId(), $request->get('_token'))) {
            if ($racesAnimaux->getAnimaux()->count() > 0) {
                $this->addFlash(
                    'warning',
                    'Il existe des animaux pour la race ' . $racesAnimaux->getLibelle() . ', elle ne peut pas être supprimée.'
                );
                return $this->redirectToRoute('races_animaux');
            }
            // supprimer la catégorie
            $entityManager->remove($racesAnimaux);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'La race ' . $racesAnimaux->getLibelle() . ' a été supprimée.'
            );
        }
        return $this->redirectToRoute('races_animaux');
    }

    #[Route('/races_animaux/stat', name: 'races_animaux_stat')]

    public function stat(RacesAnimauxRepository $repository){
        $lesRacesAnimaux = $repository->findStatRace();
        return $this->render('races_animaux/stat.html.twig', [
            'lesRacesAnimaux' => $lesRacesAnimaux,
        ]);
    }
}
