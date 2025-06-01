<?php

namespace App\Controller;

use App\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SearchController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $client,
    ) {}

    #[Route('/search', name: 'app_search_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $ident = trim($request->query->get('ident', ''));

        $regions = $this->entityManager->getRepository(Region::class)->findAll();

        return $this->render('search.html.twig', ['regions' => json_encode($regions), 'ident' => $ident]);
    }
}
