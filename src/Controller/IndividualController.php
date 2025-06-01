<?php

namespace App\Controller;

use App\Repository\IndividualRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class IndividualController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $client,
        private IndividualRepository $individualRepository,
    ) {}

    #[Route('/individual/{slug}', name: 'app_individual_details', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $individual = $this->individualRepository->findOneBy(['slug' => $request->get('slug')]);

        return $this->render('individual.html.twig', [
            'individual' => $individual,
        ]);
    }
}
