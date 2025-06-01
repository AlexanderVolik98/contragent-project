<?php

namespace App\Controller;

use Dadata\DadataClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class IndexController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $client,
    ) {}

    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('home.html.twig');
    }
}
