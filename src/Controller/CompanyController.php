<?php

namespace App\Controller;

use App\Helper\InfoBlocksHelper;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CompanyController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $client,
    ) {}

    #[Route('/company/{slug}', name: 'app_company_details', methods: ['GET'])]
    public function index(string $slug, CompanyRepository $companyRepository): Response
    {
        $company = $companyRepository->findOneBy(['slug' => $slug]);

        if (!$company) {
            throw $this->createNotFoundException('Компания не найдена');
        }

        $infoBlocks = InfoBlocksHelper::orderInfoBlocks($company);

        return $this->render('company.html.twig', [
            'company' => $company, 'infoBlocks' => $infoBlocks
        ]);
    }
}
