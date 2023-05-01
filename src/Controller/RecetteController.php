<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RecetteRepository;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;


class RecetteController extends AbstractController
{
    #[Route('/recette', name: 'recette')]
    #[Route('/recette/demandermodification/{id<\d+>}', name: 'recette_demandermodification')]
    public function index(RecetteRepository $repository, $id = null): Response
    {
        // créer l'objet et le formulaire de création
        $recette = new Recette();
        $formCreation = $this->createForm(RecetteType::class, $recette);

        // si 2e route alors $id est renseigné et on  crée le formulaire de modification
        if ($id != null) {
            $recetteModif = $repository->find($id);   // la catégorie à modifier
            $formModificationView = $this->createForm(RecetteType::class, $recetteModif)->createView();
        } else {
            $formModificationView = null;
        }
        
        // lire les recettes
        $lesRecettes = $repository->findAll();
        return $this->render('recette/index.html.twig', [
            'formCreation' => $formCreation->createView(),
            'lesRecettes' => $lesRecettes,
            'formModification' => $formModificationView,
            'idRecetteModif' => $id,
        ]);
    }

    #[Route('/recette/ajouter', name: 'recette_ajouter')]

    public function ajouter(Request $request, EntityManagerInterface $entityManager, RecetteRepository $repository, Recette $recette = null)
    {

        $recette = new Recette();
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recette);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'La recette ' . $recette->getNom() . ' a été ajoutée.'
            );
            return $this->redirectToRoute('recette');

        } else {
            $lesRecettes = $repository->findAll();
            return $this->render('recette/index.html.twig', [
                'formCreation' => $form->createView(),
                'lesRecettes' => $lesRecettes,
                'formModification' => null,
                'idRecetteModif' => null,
    
            ]);
        }
    }

    #[Route('/recette/modifier/{id<\d+>}', name: 'recette_modifier')]
    public function modifier(Request $request, EntityManagerInterface $entityManager, RecetteRepository $repository, Recette $recette = null, $id = null)
    {
        //  Symfony 4 est capable de retrouver la catégorie à l'aide de Doctrine ORM directement en utilisant l'id passé dans la route
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // va effectuer la requête d'UPDATE en base de données
            // pas besoin de "persister" l'entité car l'objet a déjà été retrouvé à partir de Doctrine ORM.
            $entityManager->flush();
            $this->addFlash(
                'success',
                'La recette '.$recette->getNom().' a été modifiée.'
            );
            // rediriger vers l'affichage des catégories qui comprend le formulaire pour l"ajout d'une nouvelle catégorie
            return $this->redirectToRoute('recette');

        } else {
            // affichage de la liste des catégories avec le formulaire de modification et ses erreurs
            // créer l'objet et le formulaire de création
            $recette = new Recette();
            $formCreation = $this->createForm(RecetteType::class, $recette);
            // lire les catégories
            $lesRecettes = $repository->findAll();
            // rendre la vue
            return $this->render('recette/index.html.twig', [
                'formCreation' => $formCreation->createView(),
                'lesRecettes' => $lesRecettes,
                'formModification' => $form->createView(),
                'idRecetteModif' => $id,
            ]);
        }
    }
    
    #[Route('/recette/supprimer/{id<\d+>}', name: 'recette_supprimer')]

    public function supprimer(Request $request, EntityManagerInterface $entityManager, Recette $recette = null)
    {
        // vérifier le token
        if ($this->isCsrfTokenValid('action-item'.$recette->getId(), $request->get('_token'))) {
            // supprimer la recette
            $entityManager->remove($recette);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'La recette ' . $recette->getNom() . ' a été supprimée.'
            );
        }
        return $this->redirectToRoute('recette');
    }




}
