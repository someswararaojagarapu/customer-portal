<?php

namespace App\CustomerPortal\Service;

use App\CustomerPortal\Manager\FileReaderManager;
use App\CustomerPortal\Redis\RedisKeys;
use Predis\Connection\ConnectionException;

class CacheServerInfoDataService
{
    public function __construct(
        private readonly RedisAdapterService $redisAdapterService,
        private readonly FileReaderManager $fileReaderManager,
        private readonly ServerInformationService $serverInformationService
    ) {
    }

    public function setServerInfDataToRedis(int $filterExpirationTime): array
    {
        try {
            $serverInfoJson = $this->fileReaderManager->readJson();
            $data = json_decode($serverInfoJson, true);
            $serverInfoDataResult = $this->serverInformationService->prepareInputData($data);
            $serverInfoDataKeyFromRedis = RedisKeys::SERVER_INFORMATION_DATA_KEY_REDIS;
            $redisConnection = $this->redisAdapterService->getRedisConnection();
            $serializedFilterResult = json_encode($serverInfoDataResult);
            $redisConnection->setex($serverInfoDataKeyFromRedis, $filterExpirationTime, $serializedFilterResult);

            return $serverInfoDataResult;
        } catch (ConnectionException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getServerInfoDataFromRedis(
        int $filterExpirationTime
    ): array {
        $serverInfoDataKeyFromRedis = RedisKeys::SERVER_INFORMATION_DATA_KEY_REDIS;
        try {
            $redisConnection = $this->redisAdapterService->getRedisConnection();
            if ($redisConnection->exists($serverInfoDataKeyFromRedis)) {
                $serverInfoDataFromCache = $redisConnection->get($serverInfoDataKeyFromRedis);

                if (empty($serverInfoDataFromCache) || $serverInfoDataFromCache === '[]') {
                    return $this->setServerInfDataToRedis($filterExpirationTime);
                }

                return json_decode($serverInfoDataFromCache, true);
            } else {
                return $this->setServerInfDataToRedis($filterExpirationTime);
            }
        } catch (ConnectionException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
