<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\SearchService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SubjectController extends AbstractController
{
    public function __construct(
        private readonly SearchService $searchService,
    ) {}

    #[Route('/api/subject', name: 'app_subject')]
    public function index(Request $request): Response
    {
        $identifier = trim((string)$request->query->get('identifier', ''));
        $page = $request->query->getInt('page', 1);
        $perPage = $request->query->getInt('perPage', 10);
        $params = $request->query->all();

        if ($identifier === '') {
            return $this->json(['error' => 'Parameter "identifier" is required'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->searchService->search($identifier, $params, $page, $perPage);
        if ($result['total'] === 0) {
            return $this->json('subject not found', Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'items'      => $result['items'],
            'pagination' => [
                'currentPage' => $page,
                'perPage'     => $perPage,
                'totalItems'  => $result['total'],
                'totalPages'  => (int)ceil($result['total'] / $perPage),
            ],
        ]);
    }
}
