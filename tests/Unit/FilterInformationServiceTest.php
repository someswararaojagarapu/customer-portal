<?php

namespace App\CustomerPortal\Tests\Unit;

use App\CustomerPortal\Service\FilterInformationService;
use App\CustomerPortal\Manager\FileReaderManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FilterInformationServiceTest extends WebTestCase
{
    private FilterInformationService $filterInformationService;
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->fileReaderManager = $this->container->get(FileReaderManager::class);
        $this->filterInformationService = new FilterInformationService();
    }

    public function testGetFilterResult(): void
    {
        $serverInfoJson = $this->fileReaderManager->readJson();
        // call FilterInformation Service
        $serviceResult = $this->filterInformationService->getFilterResult($serverInfoJson);
        $this->assertIsArray($serviceResult);
        $this->assertEquals(FilterInformationService::STORAGE_OPTIONS, $serviceResult['Storage']);
        $this->assertEquals(FilterInformationService::RAM_OPTIONS, $serviceResult['Ram']);
        $this->assertEquals(FilterInformationService::HARD_DISK_OPTIONS, $serviceResult['HardDiskTypes']);
        $this->assertEquals($this->getLocations(), $serviceResult['Location']);
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
