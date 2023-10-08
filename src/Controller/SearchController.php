<?php

namespace App\CustomerPortal\Controller;

use App\CustomerPortal\Dto\Request\SearchQuery;
use App\CustomerPortal\Exception\NotFoundException;
use App\CustomerPortal\Service\CacheFilterInfoService;
use App\CustomerPortal\Service\CacheServerInfoDataService;
use App\CustomerPortal\Service\ServerInformationService;
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
        private readonly CacheFilterInfoService $cacheFilterInfoService,
        private readonly ServerInformationService $serverInformationService,
        private readonly ServerInfoValidationService $serverInfoValidationService,
        private readonly CacheServerInfoDataService $cacheServerInfoDataService
    ) {}

    #[Route('/server/filter/list', name: 'server_filter_list', methods: 'GET')]
    public function filterList(int $filterExpirationTime): JsonResponse {
        try {
            $response = $this->cacheFilterInfoService->getFilterResultFromRedis($filterExpirationTime);

            return new JsonResponse($response, Response::HTTP_OK);
        } catch (NotFoundException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/server/information/list', name: 'server_information_list', methods: 'POST')]
    public function serverInfoList(
        #[MapRequestPayload(serializationContext: [])] SearchQuery $searchQuery,
        int $filterExpirationTime
    ): JsonResponse {
        try {
            $query = $this->serverInformationService->getQuery($searchQuery);
            $resolverErrors = $this->serverInfoValidationService->checkRequestPayloadOptions($query);
            if (!empty($resolverErrors)) {
                return new JsonResponse($resolverErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $inputData = $this->cacheServerInfoDataService->getServerInfoDataFromRedis($filterExpirationTime);
            $response = $this->serverInformationService->getServerInformationResult($query, $inputData);

            return new JsonResponse($response, Response::HTTP_OK);
        } catch (NotFoundException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
