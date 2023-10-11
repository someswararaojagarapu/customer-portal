<?php

namespace App\CustomerPortal\Tests\Unit;

use App\CustomerPortal\Dto\Request\SearchQuery;
use App\CustomerPortal\Manager\FileReaderManager;
use App\CustomerPortal\Service\CacheServerInfoDataService;
use App\CustomerPortal\Service\ServerInformationService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServerInformationServiceTest extends WebTestCase
{
    private ServerInformationService $serverInformationService;
    private CacheServerInfoDataService $cacheServerInfoDataService;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->fileReaderManager = $this->container->get(FileReaderManager::class);
        $this->serverInformationService = $this->container->get(ServerInformationService::class);
        $this->cacheServerInfoDataService = $this->container->get(CacheServerInfoDataService::class);
    }

    public function testGetQuery(): void
    {
        $searchQuery = $this->createSearchQueryObject();

        // call ServerInformation Service
        $serviceResult = $this->serverInformationService->getQuery($searchQuery);
        $this->assertIsArray($serviceResult);
        $this->assertEquals('0 to 5000', $serviceResult['storage']);
        $this->assertEquals(['16GB'], $serviceResult['ram']);
        $this->assertEquals('SATA', $serviceResult['hardDiskType']);
        $this->assertEquals('AmsterdamAMS-01', $serviceResult['location']);
    }

    public function testGetServerInformationResult(): void
    {
        $searchQuery = $this->createSearchQueryObject();

        // call ServerInformation Service
        $query = $this->serverInformationService->getQuery($searchQuery);
        $inputData = $this->cacheServerInfoDataService->getServerInfoDataFromRedis($_ENV['FILTER_EXPIRATION_TIME']);
        $serviceResult = $this->serverInformationService->getServerInformationResult($query, $inputData);
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

    public function testPrepareInputData(): void
    {
        $serverInfoJson = $this->fileReaderManager->readJson();
        $data = json_decode($serverInfoJson, true);
        // call ServerInformation Service
        $serviceResult = $this->serverInformationService->prepareInputData($data);
        $this->assertIsArray($serviceResult);
        $this->assertCount(486, $serviceResult);
        $this->assertEquals('Dell R210Intel Xeon X3440', $serviceResult[0]['Model']);
        $this->assertEquals('16GBDDR3', $serviceResult[0]['RAM']);
        $this->assertEquals('16GB', $serviceResult[0]['RamValue']);
        $this->assertEquals('2x2TBSATA2', $serviceResult[0]['HDD']);
        $this->assertEquals('4096', $serviceResult[0]['Storage']);
        $this->assertEquals('SATA', $serviceResult[0]['HardDiskType']);
        $this->assertEquals('AmsterdamAMS-01', $serviceResult[0]['Location']);
        $this->assertEquals('€49.99', $serviceResult[0]['Price']);
    }

    public function createSearchQueryObject(): SearchQuery
    {
        $searchQuery = new SearchQuery();
        $searchQuery->setStorage('0 to 5000');
        $searchQuery->setRam(['16GB']);
        $searchQuery->setHardDiskType('SATA');
        $searchQuery->setLocation('AmsterdamAMS-01');

        return $searchQuery;
    }
}
