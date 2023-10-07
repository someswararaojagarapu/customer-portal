<?php

namespace App\CustomerPortal\Controller;

use App\CustomerPortal\Dto\Request\SearchQuery;
use App\CustomerPortal\Exception\NotFoundException;
use App\CustomerPortal\Service\CacheFilterInformationService;
use App\CustomerPortal\Service\FilterInformationService;
use App\CustomerPortal\Service\ServerInformationService;
use App\CustomerPortal\Manager\FileReaderManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class SearchController extends AbstractController
{
    public function __construct(
        private readonly FileReaderManager $fileReaderManager,
        private readonly CacheFilterInformationService $cacheFilterInformationService,
        private readonly ServerInformationService $serverInformationService
    ) {

    }

    #[Route('/server/filter/list', name: 'server_filter_list', methods: 'GET')]
    public function filterList(int $filterExpirationTime): JsonResponse {
        try {
            $response = $this->cacheFilterInformationService->getFilterResultFromRedis($filterExpirationTime);
            if (empty($response)) {
                throw new NotFoundException('Resource not found');
            }

            return new JsonResponse($response, Response::HTTP_OK);
        } catch (NotFoundException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/server/information/list', name: 'server_information_list', methods: 'POST')]
    public function index(
        #[MapRequestPayload(serializationContext: [])] SearchQuery $searchQuery
    ): Response {
        $query = $this->serverInformationService->getQuery($searchQuery);
        $serverInfoJson = $this->fileReaderManager->readJson();
        $response = $this->serverInformationService->getServerInformationResult($query);

        return new Response($response['body'] ?? '', $response['statusCode'], $response['headers']);
    }
}