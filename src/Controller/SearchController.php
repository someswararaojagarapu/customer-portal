<?php

namespace App\CustomerPortal\Controller;

use App\CustomerPortal\Dto\Request\SearchQuery;
use App\CustomerPortal\Exception\NotFoundException;
use App\CustomerPortal\Service\CacheFilterInformationService;
use App\CustomerPortal\Service\ServerInformationService;
use App\CustomerPortal\Manager\FileReaderManager;
use App\CustomerPortal\Service\ServerInfoValidationService;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        private readonly ServerInformationService $serverInformationService,
        private readonly ServerInfoValidationService $serverInfoValidationService
    ) {}

    #[Route('/server/filter/list', name: 'server_filter_list', methods: 'GET')]
    public function filterList(int $filterExpirationTime): JsonResponse {
        try {
            $response = $this->cacheFilterInformationService->getFilterResultFromRedis($filterExpirationTime);

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
    ): JsonResponse {
        try {
            $query = $this->serverInformationService->getQuery($searchQuery);
            $resolverErrors = $this->serverInfoValidationService->checkRequestPayloadOptions($query);
            if (!empty($resolverErrors)) {
                return new JsonResponse($resolverErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $serverInfoJson = $this->fileReaderManager->readJson();
            $response = $this->serverInformationService->getServerInformationResult($query, $serverInfoJson);

            return new JsonResponse($response, Response::HTTP_OK);
        } catch (NotFoundException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
