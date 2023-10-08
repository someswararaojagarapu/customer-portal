<?php

namespace App\CustomerPortal\Tests\Unit;

use App\CustomerPortal\Manager\FileReaderManager;
use App\CustomerPortal\Service\CacheFilterInfoService;
use App\CustomerPortal\Service\FilterInformationService;
use App\CustomerPortal\Service\RedisAdapterService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CacheFilterInfoServiceTest extends WebTestCase
{
    private RedisAdapterService $redisAdapterService;
    private FilterInformationService $filterInformationService;
    private FileReaderManager $fileReaderManager;
    private CacheFilterInfoService $cacheFilterInfoService;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->redisAdapterService = $this->container->get(RedisAdapterService::class);
        $this->fileReaderManager = $this->container->get(FileReaderManager::class);
        $this->filterInformationService = $this->container->get(FilterInformationService::class);

        $this->cacheFilterInfoService = new CacheFilterInfoService(
            $this->redisAdapterService,
            $this->filterInformationService,
            $this->fileReaderManager
        );
    }

    public function testGetFilterResultFromRedis(): void
    {
        // call CacheFilterInfo Service
        $serviceResult =$this->cacheFilterInfoService->getFilterResultFromRedis($_ENV['FILTER_EXPIRATION_TIME']);
        $this->assertIsArray($serviceResult);
        $this->assertEquals(FilterInformationService::STORAGE_OPTIONS, $serviceResult['Storage']);
        $this->assertEquals(FilterInformationService::RAM_OPTIONS, $serviceResult['Ram']);
        $this->assertEquals(FilterInformationService::HARD_DISK_OPTIONS, $serviceResult['HardDiskTypes']);
        $this->assertEquals($this->getLocations(), $serviceResult['Location']);
    }

    public function getLocations():array
    {
        return [
                "AmsterdamAMS-01",
                "Washington D.C.WDC-01",
                "San FranciscoSFO-12",
                "SingaporeSIN-11",
                "DallasDAL-10",
                "FrankfurtFRA-10",
                "Hong KongHKG-10"
            ];
    }
}
