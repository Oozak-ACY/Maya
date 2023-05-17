<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AccueilRepository;
use App\Entity\Accueil;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpClient\HttpClient;
use DateTime;
use DateTimeZone;


class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'accueil')]
    public function index(CategorieRepository $repository): Response
    {

        $client = HttpClient::create();
        $urlAPI = $_SERVER['URL_API_OWM'];
        $reponse = $client->request('GET', $urlAPI);
        $donnees = json_decode($reponse->getContent());
        date_default_timezone_set('Europe/Paris');
        $dateJour = new DateTime('now');

        $lesCategories = $repository->findAll();

        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'lesCategories' => $lesCategories,
            'donnees' => $donnees,
            'dateJour' => $dateJour,
        ]);
    }

}
