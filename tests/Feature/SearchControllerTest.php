<?php

namespace App\CustomerPortal\Tests\Feature;

use App\CustomerPortal\Service\FilterInformationService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SearchControllerTest extends WebTestCase
{
    private const FILTER_API_URL = '/api/server/filter/list';

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testFilterList(): void {
        $this->client->request(
            'GET',
            self::FILTER_API_URL
        );
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        // Validate a successful response and some content
        $this->assertResponseIsSuccessful();
        $this->assertIsString($response->getContent());
        $apiResult = json_decode($response->getContent(), true);
        $this->assertIsArray($apiResult);
        $this->assertEquals(FilterInformationService::STORAGE_OPTIONS, $apiResult['Storage']);
        $this->assertEquals(FilterInformationService::RAM_OPTIONS, $apiResult['Ram']);
        $this->assertEquals(FilterInformationService::HARD_DISK_OPTIONS, $apiResult['HardDiskTypes']);
        $this->assertEquals($this->getLocations(), $apiResult['Location']);
    }
    public function getLocations(): array
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
