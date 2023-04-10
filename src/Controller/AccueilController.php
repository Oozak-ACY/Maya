<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;

class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'accueil')]
    public function index(CategorieRepository $repository, Request $request, $id = null): Response
    {
        // créer l'objet et le formulaire de création
        $categorie = new Categorie();
        $formCreation = $this->createForm(CategorieType::class, $categorie);

        $formModificationView = null;
        if ($id != null) {
            // sécurité supplémentaire, on vérifie le token
            if ($this->isCsrfTokenValid('action-item'.$id, $request->get('_token'))) {
                $categorieModif = $repository->find($id);   // la catégorie à modifier
                $formModificationView = $this->createForm(CategorieType::class, $categorieModif)->createView();
            }
        }


        // lire les catégories
        $lesCategories = $repository->findAll();
        return $this->render('accueil/index.html.twig', [
            'formCreation' => $formCreation->createView(),
            'lesCategories' => $lesCategories,
            'formModification' => $formModificationView,
            'idCategorieModif' => $id,
        ]);
    }
}
