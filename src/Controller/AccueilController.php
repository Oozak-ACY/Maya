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
        $cleAPI = "a81b7c2466037a7685c0a2eefc660aa3";
        $ville = "metz";

        $client = HttpClient::create();
        $urlAPI = "http://api.openweathermap.org/data/2.5/weather?q=" . $ville . "&lang=fr&units=metric&APPID=" . $cleAPI;
        $reponse = $client->request('GET', $urlAPI);
        $donnees = json_decode($reponse->getContent());
        $dateJour = new DateTime('now', new DateTimeZone('Europe/Paris'));

        $lesCategories = $repository->findAll();

        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'lesCategories' => $lesCategories,
            'donnees' => $donnees,
            'dateJour' => $dateJour,
        ]);
    }

}
