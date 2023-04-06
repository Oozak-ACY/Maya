<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Fournisseur;
use App\Form\FournisseurType;
use App\Repository\FournisseurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FournisseurController extends AbstractController
{
    #[Route('/fournisseur', name: 'fournisseur')]
    /**
     *  @Route("/fournisseur/demandermodification/{id<\d+>}", name="fournisseur_demandermodification")
     */
    public function index(FournisseurRepository $repository, Request $request, $id = null): Response
    {
        // créer l'objet et le formulaire de création
        $fournisseur = new Fournisseur();
        $formCreation = $this->createForm(FournisseurType::class, $fournisseur);

        // si 2e route alors $id est renseigné et on  crée le formulaire de modification
        $formModificationView = null;
        if ($id != null) {
            // sécurité supplémentaire, on vérifie le token
            if ($this->isCsrfTokenValid('action-item' . $id, $request->get('_token'))) {
                $fournisseurModif = $repository->find($id);   // le fournisseur à modifier
                $formModificationView = $this->createForm(FournisseurType::class, $fournisseurModif)->createView();
            }
        } else {
            $formModificationView = null;
        }


        //lire les fournisseur
        $lesFournisseurs = $repository->findAll();
        return $this->render('fournisseur/index.html.twig', [
            'formCreation' => $formCreation->createView(),
            'lesFournisseurs' => $lesFournisseurs,
            'formModification' => $formModificationView,
            'idFournisseurModif' => $id,
        ]);
    }

    /**
     * @Route("/fournisseur/ajouter", name="fournisseur_ajouter")
     */
    public function ajouter(Fournisseur $fournisseur = null, Request $request, EntityManagerInterface $entityManager, FournisseurRepository $repository)
    {
        //  $fournisseur objet de la classe Fournisseur, il contiendra les valeurs saisies dans les champs après soumission du formulaire.
        //  $request  objet avec les informations de la requête HTTP (GET, POST, ...)
        //  $entityManager  pour la persistance des données

        // création d'un formulaire de type FournisseurType
        $fournisseur = new Fournisseur();
        $form = $this->createForm(FournisseurType::class, $fournisseur);

        // handleRequest met à jour le formulaire
        //  si le formulaire a été soumis, handleRequest renseigne les propriétés
        //      avec les données saisies par l'utilisateur et retournées par la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // c'est le cas du retour du formulaire
            //         l'objet $fournisseur a été automatiquement "hydraté" par Doctrine
            // dire à Doctrine que l'objet sera (éventuellement) persisté
            $entityManager->persist($fournisseur);
            // exécuter les requêtes (indiquées avec persist) ici il s'agit de l'ordre INSERT qui sera exécuté
            $entityManager->flush();
            // ajouter un message flash de succès pour informer l'utilisateur
            $this->addFlash(
                'success',
                'Le fournisseur ' . $fournisseur->getName() . ' a été ajoutée.'
            );
            // rediriger vers l'affichage des fournisseurs qui comprend le formulaire pour l"ajout d'une nouvelle catégorie
            return $this->redirectToRoute('fournisseur');
        } else {
            // affichage de la liste des fournisseurs avec le formulaire de création et ses erreurs
            // lire les fournisseur
            $lesFournisseurs = $repository->findAll();
            // rendre la vue
            return $this->render('fournisseur/index.html.twig', [
                'formCreation' => $form->createView(),
                'lesFournisseurs' => $lesFournisseurs,
                'formModification' => null,
                'idFournisseurModif' => null,
            ]);
        }
    }

    /**
     * @Route("/fournisseur/modifier/{id<\d+>}", name="fournisseur_modifier")
     */
    public function modifier(Fournisseur $fournisseur = null, $id = null, Request $request, EntityManagerInterface $entityManager, FournisseurRepository $repository)
    {
        //  Symfony 4 est capable de retrouver le fournsseur  à l'aide de Doctrine ORM directement en utilisant l'id passé dans la route
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // va effectuer la requête d'UPDATE en base de données
            // pas besoin de "persister" l'entité car l'objet a déjà été retrouvé à partir de Doctrine ORM.
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le fournisseur ' . $fournisseur->getName() . ' a été modifiée.'
            );
            // rediriger vers l'affichage des catégories qui comprend le formulaire pour l"ajout d'une nouvelle catégorie
            return $this->redirectToRoute('fournisseur');
        } else {
            // affichage de la liste des catégories avec le formulaire de modification et ses erreurs
            // créer l'objet et le formulaire de création
            $fournisseur = new Fournisseur();
            $formCreation = $this->createForm(FournisseurType::class, $fournisseur);
            // lire les catégories
            $lesFournisseurs = $repository->findAll();
            // rendre la vue
            return $this->render('fournisseur/index.html.twig', [
                'formCreation' => $formCreation->createView(),
                'lesFournisseurs' => $lesFournisseurs,
                'formModification' => $form->createView(),
                'idCategorieModif' => $id,
            ]);
        }
    }

    /**
     * @Route("/fournisseur/supprimer/{id<\d+>}", name="fournisseur_supprimer")
     */
    public function supprimer(Fournisseur $fournisseur = null, Request $request, EntityManagerInterface $entityManager)
    {
        // vérifier le token
        if ($this->isCsrfTokenValid('action-item' . $fournisseur->getId(), $request->get('_token'))) {

            // supprimer la catégorie
            $entityManager->remove($fournisseur);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'La catégorie ' . $fournisseur->getName() . ' a été supprimée.'
            );
        }
        return $this->redirectToRoute('fournisseur');
    }
}
