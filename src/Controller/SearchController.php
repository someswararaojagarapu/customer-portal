<?php

namespace App\CustomerPortal\Controller;

use App\CustomerPortal\Dto\Request\SearchQuery;
use App\CustomerPortal\Service\ServerInformationService;
use App\CustomerPortal\Manager\FileReaderManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class SearchController extends AbstractController
{
    public function __construct(
        private readonly ServerInformationService $serverInformationService
    ) {
    }

    #[Route('/server/information/list', name: 'server_information_list', methods: 'GET')]
    public function index(
        Request $request,
        FileReaderManager $fileReaderManager,
        #[MapRequestPayload(serializationContext: [])] SearchQuery $searchQuery
    ): Response {
        $query = $this->serverInformationService->getQuery($searchQuery);
        $serverInfoJson = $fileReaderManager->readJson();
        $response = $this->serverInformationService->getServerInformationResult($query);

        return new Response($response['body'] ?? '', $response['statusCode'], $response['headers']);
    }
}