<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;


class ProduitController extends AbstractController
{
    #[Route('/produit/complet/creer', name: 'produit_complet_creer')]
    public function creerProduitComplet()
    {
        // créer une catégorie
        $categorie = new Categorie();
        $categorie->setLibelle('Fruits');

        // créer un produit
        $produit = new Produit();
        $produit->setLibelle('mirabelle');
        $produit->setPrix(2.50);

        // mettre en relation le produit avec la catégorie
        $produit->setCategorie($categorie);

        // persister les objets
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($categorie);
        $entityManager->persist($produit);
        // exécutez les requêtes
        $entityManager->flush();

        // retourner une réponse
        return new Response(
            'Nouveau produit enregistré avec l\'id : '.$produit->getId()
            .' et nouvelle catégorie enregistrée avec id: '.$categorie->getId()
        );
    }


    #[Route('/produit', name: 'produit')]
    public function index(): Response
    {
        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
        ]);
    }
    
}
