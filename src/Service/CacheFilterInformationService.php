<?php

namespace App\CustomerPortal\Service;

use App\CustomerPortal\Manager\FileReaderManager;
use App\CustomerPortal\Redis\RedisKeys;
use Predis\Connection\ConnectionException;

class CacheFilterInformationService
{
    public function __construct(
        private readonly RedisAdapterService $redisAdapterService,
        private readonly FilterInformationService $filterInformationService,
        private readonly FileReaderManager $fileReaderManager,
    ) {
    }

    public function setFilterResultToRedis(int $filterExpirationTime): array
    {
        try {
            $serverInfoJson = $this->fileReaderManager->readJson();
            $filterResult = $this->filterInformationService->getFilterResult($serverInfoJson);
            $filterKeyFromRedis = RedisKeys::FILTER_INFORMATION_KEY_REDIS;
            $redisConnection = $this->redisAdapterService->getRedisConnection();
            $serializedFilterResult = json_encode($filterResult);
            $redisConnection->setex($filterKeyFromRedis, $filterExpirationTime, $serializedFilterResult);

            return $filterResult;
        } catch (ConnectionException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getFilterResultFromRedis(
        int $filterExpirationTime
    ): array {
        $filterKeyFromRedis = RedisKeys::FILTER_INFORMATION_KEY_REDIS;
        try {
            $redisConnection = $this->redisAdapterService->getRedisConnection();
            if ($redisConnection->exists($filterKeyFromRedis)) {
                $filterResultFromCache = $redisConnection->get($filterKeyFromRedis);

                if (empty($filterResultFromCache) || $filterResultFromCache === '[]') {
                    return $this->setFilterResultToRedis($filterExpirationTime);
                }

                return json_decode($filterResultFromCache, true);
            } else {
                return $this->setFilterResultToRedis($filterExpirationTime);
            }
        } catch (ConnectionException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
