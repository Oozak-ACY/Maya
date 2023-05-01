<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Repository\RecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\ProduitRecherche;
use App\Form\ProduitRechercheType;   
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProduitController extends AbstractController
{



#[Route('/produit', name: 'produit')]
public function index(Request $request, ProduitRepository $repository, SessionInterface $session): Response
    {
        // créer l'objet et le formulaire de recherche
        $produitRecherche = new ProduitRecherche();
        $formRecherche = $this->createForm(ProduitRechercheType::class, $produitRecherche);
        $formRecherche->handleRequest($request);
        if ($formRecherche->isSubmitted() && $formRecherche->isValid()) {
            $produitRecherche = $formRecherche->getData();
            // cherche les produits correspondant aux critères, triés par libellé
            // requête construite dynamiquement alors il est plus simple d'utiliser le querybuilder
            $lesProduits = $repository->findAllByCriteria($produitRecherche);
            // mémoriser les critères de sélection dans une variable de session
            $session->set('ProduitCriteres', $produitRecherche);
        } else {
            // lire les produits
            if ($session->has("ProduitCriteres")) {

                $produitRecherche = $session->get("ProduitCriteres");
                $lesProduits = $repository->findAllByCriteria($produitRecherche);
                $formRecherche = $this->createForm(ProduitRechercheType::class, $produitRecherche);
                $formRecherche->setData($produitRecherche);
            } else {
                $lesProduits = $repository->findAllOrderByLibelle();
            }
        }
        
        return $this->render('produit/index.html.twig', [
            'formRecherche' => $formRecherche->createView(),
            'lesProduits' => $lesProduits,
        ]);
    }

    #[Route('/produit/ajouter', name: 'produit_ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager, Produit $produit = null): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // cas où le formulaire d'ajout a été soumis par l'utilisateur et est valide
            $produit = $form->getData();
            // on met à jour la base de données 
            $entityManager->persist($produit);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le produit ' . $produit->getLibelle() . ' a été ajouté.'
            );
            return $this->redirectToRoute('produit');
        } else {
            // cas où l'utilisateur a demandé l'ajout, on affiche le formulaire d'ajout
            return $this->render('produit/ajouter.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }
    
    #[Route('/produit/modifier/{id<\d+>}', name: 'produit_modifier')]

    public function modifier(Request $request, EntityManagerInterface $entityManager, Produit $produit = null): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // cas où le formulaire  a été soumis par l'utilisateur et est valide
            //pas besoin de "persister" l'entité : en effet, l'objet a déjà été retrouvé à partir de Doctrine ORM.
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le produit '.$produit->getLibelle().' a été modifié.'
            );

            return $this->redirectToRoute('produit');
        }
        // cas où l'utilisateur a demandé la modification, on affiche le formulaire pour la modification
        return $this->render('produit/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produit/supprimer/{id<\d+>}', name: 'produit_supprimer')]

    public function supprimer(Produit $produit, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('action-item'.$produit->getId(), $request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le produit '.$produit->getLibelle().' a été supprimé.'
            );

            return $this->redirectToRoute('produit');
        }
    }

    #[Route('/produit/recettesProduit', name: 'recettes_produit')]

    public function recettesProduit(Request $request, RecetteRepository $repository): Response
    {
        // récupérer la valeur de idProduit envoyée
        $idProduit = $request->request->get('idProduit');
        // chercher les recettes correspondantes
        $lesRecettes = $repository->findNameByProduit($idProduit);
        // retourner une réponse encodée en JSON
        $response = new Response(json_encode($lesRecettes));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    // /**
//  * @Route("/produit/complet/creer", name="produit_complet_creer")
//  */
// public function creerProduitComplet()
// {
//     // créer une catégorie
//     $categorie = new Categorie();
//     $categorie->setLibelle('Fruits');

//     // créer un produit
//     $produit = new Produit();
//     $produit->setLibelle('mirabelle');
//     $produit->setPrix(2.50);

//     // mettre en relation le produit avec la catégorie
//     $produit->setCategorie($categorie);

//     // persister les objets
//     $entityManager = $this->getDoctrine()->getManager();
//     $entityManager->persist($categorie);
//     $entityManager->persist($produit);
//     // exécutez les requêtes
//     $entityManager->flush();

//     // retourner une réponse
//     return new Response(
//         'Nouveau produit enregistré avec l\'id : '.$produit->getId()
//         .' et nouvelle catégorie enregistrée avec id: '.$categorie->getId()
//     );
// }



}
