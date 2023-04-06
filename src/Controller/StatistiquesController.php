<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategorieRepository;

class StatistiquesController extends AbstractController
{
    #[Route('/statistiques', name: 'statistiques')]
    public function index(): Response
    {
        return $this->render('statistiques/index.html.twig', [
            'controller_name' => 'StatistiquesController',
        ]);
    }

    #[Route('/statistiques/produits', name: 'statistiques_produits')]
    public function statProduitByCategorie (CategorieRepository $repository) {
        $lesCategories =  $repository->findProduitByCategorie();
        // rendre La vue
        return $this->render('statistiques/index.html.twig',[
            'lesCategories' => $lesCategories,
        ]);
    }
}
