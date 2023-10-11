<?php

namespace App\CustomerPortal\Tests\Unit;

use App\CustomerPortal\Manager\FileReaderManager;
use App\CustomerPortal\Service\CacheServerInfoDataService;
use App\CustomerPortal\Service\RedisAdapterService;
use App\CustomerPortal\Service\ServerInformationService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CacheServerInfoDataServiceTest extends WebTestCase
{
    private RedisAdapterService $redisAdapterService;
    private FileReaderManager $fileReaderManager;
    private ServerInformationService $serverInformationService;
    private CacheServerInfoDataService $cacheServerInfoDataService;
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->redisAdapterService = $this->container->get(RedisAdapterService::class);
        $this->fileReaderManager = $this->container->get(FileReaderManager::class);
        $this->serverInformationService = $this->container->get(ServerInformationService::class);
        $this->cacheServerInfoDataService = new CacheServerInfoDataService(
            $this->redisAdapterService,
            $this->fileReaderManager,
            $this->serverInformationService
        );
    }

    public function testGetServerInfoDataFromRedis(): void
    {
        // call CacheServerInfoData Service
        $serviceResult = $this->cacheServerInfoDataService->getServerInfoDataFromRedis($_ENV['FILTER_EXPIRATION_TIME']);
        $this->assertIsArray($serviceResult);
        $this->assertEquals('Dell R210Intel Xeon X3440', $serviceResult[0]['Model']);
        $this->assertEquals('16GBDDR3', $serviceResult[0]['RAM']);
        $this->assertEquals('16GB', $serviceResult[0]['RamValue']);
        $this->assertEquals('2x2TBSATA2', $serviceResult[0]['HDD']);
        $this->assertEquals('4096', $serviceResult[0]['Storage']);
        $this->assertEquals('SATA', $serviceResult[0]['HardDiskType']);
        $this->assertEquals('AmsterdamAMS-01', $serviceResult[0]['Location']);
        $this->assertEquals('€49.99', $serviceResult[0]['Price']);
    }

    public function getLocations(): array
    {
        return [
            'AmsterdamAMS-01',
            'Washington D.C.WDC-01',
            'San FranciscoSFO-12',
            'SingaporeSIN-11',
            'DallasDAL-10',
            'FrankfurtFRA-10',
            'Hong KongHKG-10'
        ];
    }
}
