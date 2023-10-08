<?php

namespace App\CustomerPortal\Tests\Feature;

use App\CustomerPortal\Service\FilterInformationService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SearchControllerTest extends WebTestCase
{
    private const FILTER_API_URL = '/api/server/filter/list';
    private const SERVER_INFO_LIST = '/api/server/information/list';

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

    /**
     * @test
     * @dataProvider getServerInfoDataProvider
     */
    public function testServerInfoList(
        $requestBody,
        $contentType,
        $expectedResponse,
        $statusCode
    ): void {
        $this->client->request(
            'POST',
            self::SERVER_INFO_LIST,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestBody)
        );
        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertIsString($response->getContent());
        $apiResult = json_decode($response->getContent(), true);
        $this->assertIsArray($apiResult);
        $this->assertEquals(count($expectedResponse), count($apiResult));
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
    public static function getServerInfoDataProvider()
    {
        return [
            'Success scenario' => [
                self::successPayload()['requestPayload'],
                'contentType' => 'application/json',
                self::successPayload()['expectedOutPut'],
                Response::HTTP_OK
            ],
            'Success scenario with wrong location' => [
                self::successScenarioWithWrongLocationPayload()['requestPayload'],
                'contentType' => 'application/json',
                self::successScenarioWithWrongLocationPayload()['expectedOutPut'],
                Response::HTTP_OK
            ],
            'Success scenario with location and storage' => [
                self::successScenarioWithLocationAndStoragePayload()['requestPayload'],
                'contentType' => 'application/json',
                self::successScenarioWithLocationAndStoragePayload()['expectedOutPut'],
                Response::HTTP_OK
            ],
            'Success scenario with ram and storage' => [
                self::successScenarioWithRamAndStoragePayload()['requestPayload'],
                'contentType' => 'application/json',
                self::successScenarioWithRamAndStoragePayload()['expectedOutPut'],
                Response::HTTP_OK
            ],
            'Success scenario with multiple ram and storage' => [
                self::successScenarioWithMultipleRamAndStoragePayload()['requestPayload'],
                'contentType' => 'application/json',
                self::successScenarioWithMultipleRamAndStoragePayload()['expectedOutPut'],
                Response::HTTP_OK
            ],
            'Success scenario with storage ram and hardDiskType' => [
                self::successScenarioWithStorageRamAndHardDiskTypePayload()['requestPayload'],
                'contentType' => 'application/json',
                self::successScenarioWithStorageRamAndHardDiskTypePayload()['expectedOutPut'],
                Response::HTTP_OK
            ],
            'Failure scenario with wrong ram' => [
                self::failurePayloadWithWrongRam()['requestPayload'],
                'contentType' => 'application/json',
                self::failurePayloadWithWrongRam()['expectedOutPut'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            ],
            'Failure scenario with wrong hardDiskType' => [
                self::failurePayloadWithWrongHardDiskType()['requestPayload'],
                'contentType' => 'application/json',
                self::failurePayloadWithWrongHardDiskType()['expectedOutPut'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            ],
        ];
    }

    public static function successPayload(): array
    {
        return [
            'requestPayload' => [
                "storage" => "0 to 2048",
                "ram" => ["16GB"],
                "hardDiskType" => "SATA",
                "location" => "AmsterdamAMS-01"
            ],
            'expectedOutPut' => [
                [
                    "Model" => "Dell R210-IIIntel Xeon E3-1220",
                    "RAM" => "16GBDDR3",
                    "RamValue" => "16GB",
                    "HDD" => "2x1TBSATA2",
                    "Storage" => 2048,
                    "HardDiskType" => "SATA",
                    "Location" => "AmsterdamAMS-01",
                    "Price" => "€59.99"
                ],
                [
                    "Model" => "Dell R210-IIIntel Xeon E3-1270v2",
                    "RAM" => "16GBDDR3",
                    "RamValue" => "16GB",
                    "HDD" => "2x1TBSATA2",
                    "Storage" => 2048,
                    "HardDiskType" => "SATA",
                    "Location" => "AmsterdamAMS-01",
                    "Price" => "€89.99"
                ],
                [
                    "Model" => "DL20G9Intel Xeon E3-1270v5",
                    "RAM" => "16GBDDR4",
                    "RamValue" => "16GB",
                    "HDD" => "2x1TBSATA2",
                    "Storage" => 2048,
                    "HardDiskType" => "SATA",
                    "Location" => "AmsterdamAMS-01",
                    "Price" => "€112.99"
                ],
                [
                    "Model" => "Dell R210-IIIntel Xeon E3-1230v2",
                    "RAM" => "16GBDDR3",
                    "RamValue" => "16GB",
                    "HDD" => "2x500GBSATA2",
                    "Storage" => 1000,
                    "HardDiskType" => "SATA",
                    "Location" => "AmsterdamAMS-01",
                    "Price" => "€119.99"
                ],
                [
                    "Model" => "Dell R210-IIIntel Xeon E3-1220",
                    "RAM" => "16GBDDR3",
                    "RamValue" => "16GB",
                    "HDD" => "2x1TBSATA2",
                    "Storage" => 2048,
                    "HardDiskType" => "SATA",
                    "Location" => "AmsterdamAMS-01",
                    "Price" => "€110.99"
                ],
                [
                    "Model" => "Dell R210-IIIntel Xeon E3-1220",
                    "RAM" => "16GBDDR3",
                    "RamValue" => "16GB",
                    "HDD" => "2x1TBSATA2",
                    "Storage" => 2048,
                    "HardDiskType" => "SATA",
                    "Location" => "AmsterdamAMS-01",
                    "Price" => "€90.99"
                ],
                [
                    "Model" => "Dell R210-IIIntel Xeon E3-1220",
                    "RAM" => "16GBDDR3",
                    "RamValue" => "16GB",
                    "HDD" => "2x1TBSATA2",
                    "Storage" => 2048,
                    "HardDiskType" => "SATA",
                    "Location" => "AmsterdamAMS-01",
                    "Price" => "€224.99"
                ]
                ]
        ];
    }

    public static function successScenarioWithWrongLocationPayload(): array
    {
        return [
            'requestPayload' => [
                "storage" => "0 to 2048",
                "ram" => ["16GB"],
                "hardDiskType" => "SATA",
                "location" => "AmsterdamAMS-02"
            ],
            'expectedOutPut' => []
        ];
    }

    public static function successScenarioWithLocationAndStoragePayload(): array
    {
        return [
            'requestPayload' => [
                "storage" => "0 to 2048",
                "ram" => [],
                "hardDiskType" => "",
                "location" => "SingaporeSIN-11"
            ],
            'expectedOutPut' => [
                [
                    "Model" => "Dell R730XD2x Intel Xeon E5-2650V4",
                    "RAM" => "128GBDDR4",
                    "RamValue" => "128GB",
                    "HDD" => "4x480GBSSD",
                    "Storage" => 1920,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$565.99"
                ],
                [
                    "Model" => "HP DL180G62x Intel Xeon E5620",
                    "RAM" => "32GBDDR3",
                    "RamValue" => "32GB",
                    "HDD" => "2x1TBSATA2",
                    "Storage" => 2048,
                    "HardDiskType" => "SATA",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$228.00"
                ],
                [
                    "Model" => "Huawei RH1288v22x Intel Xeon E5-2650",
                    "RAM" => "8GBDDR3",
                    "RamValue" => "8GB",
                    "HDD" => "2x1TBSATA2",
                    "Storage" => 2048,
                    "HardDiskType" => "SATA",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$269.99"
                ],
                [
                    "Model" => "Huawei RH2288V22x Intel Xeon E5-2620",
                    "RAM" => "32GBDDR3",
                    "RamValue" => "32GB",
                    "HDD" => "2x1TBSATA2",
                    "Storage" => 2048,
                    "HardDiskType" => "SATA",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$239.99"
                ],
                [
                    "Model" => "Dell R730XD2x Intel Xeon E5-2650V3",
                    "RAM" => "128GBDDR4",
                    "RamValue" => "128GB",
                    "HDD" => "2x120GBSSD",
                    "Storage" => 240,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$545.99"
                ],
                [
                    "Model" => "Dell R6302x Intel Xeon E5-2650v3",
                    "RAM" => "128GBDDR4",
                    "RamValue" => "128GB",
                    "HDD" => "2x120GBSSD",
                    "Storage" => 240,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$555.99"
                ],
                [
                    "Model" => "HP DL120G9Intel Xeon E5-1650v3",
                    "RAM" => "64GBDDR4",
                    "RamValue" => "64GB",
                    "HDD" => "2x120GBSSD",
                    "Storage" => 240,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$368.99"
                ],
                [
                    "Model" => "Dell R9304x Intel Xeon E7-4820v3",
                    "RAM" => "64GBDDR4",
                    "RamValue" => "64GB",
                    "HDD" => "2x120GBSSD",
                    "Storage" => 240,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$1328.99"
                ],
                [
                    "Model" => "Dell R9304x Intel Xeon E7-4830v3",
                    "RAM" => "64GBDDR4",
                    "RamValue" => "64GB",
                    "HDD" => "2x120GBSSD",
                    "Storage" => 240,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$1516.99"
                ],
                [
                    "Model" => "Dell R9304x Intel Xeon E7-4850v3",
                    "RAM" => "64GBDDR4",
                    "RamValue" => "64GB",
                    "HDD" => "2x120GBSSD",
                    "Storage" => 240,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$1787.99"
                ],
                [
                    "Model" => "HP DL20 G9Intel Xeon E3-1270v5",
                    "RAM" => "16GBDDR4",
                    "RamValue" => "16GB",
                    "HDD" => "2x1TBSATA2",
                    "Storage" => 2048,
                    "HardDiskType" => "SATA",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$208.00"
                ],
                [
                    "Model" => "Dell R6302x Intel Xeon E5-2630v4",
                    "RAM" => "64GBDDR4",
                    "RamValue" => "64GB",
                    "HDD" => "2x240GBSSD",
                    "Storage" => 480,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$489.99"
                ],
                [
                    "Model" => "RH2288v32x Intel Xeon E5-2650V4",
                    "RAM" => "128GBDDR4",
                    "RamValue" => "128GB",
                    "HDD" => "4x480GBSSD",
                    "Storage" => 1920,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$429.99"
                ],
                [
                    "Model" => "Dell R210-IIIntel Xeon E3-1270v2",
                    "RAM" => "8GBDDR3",
                    "RamValue" => "8GB",
                    "HDD" => "2x1TBSATA2",
                    "Storage" => 2048,
                    "HardDiskType" => "SATA",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$199.99"
                ],
                [
                    "Model" => "Dell R6202x Intel Xeon E5-2620v2",
                    "RAM" => "8GBDDR3",
                    "RamValue" => "8GB",
                    "HDD" => "2x1TBSATA2",
                    "Storage" => 2048,
                    "HardDiskType" => "SATA",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$319.99"
                ],
                [
                    "Model" => "Dell R9304x Intel Xeon E7-4820v3",
                    "RAM" => "64GBDDR4",
                    "RamValue" => "64GB",
                    "HDD" => "2x120GBSSD",
                    "Storage" => 240,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$1953.99"
                ],
                [
                    "Model" => "Dell R9304x Intel Xeon E7-4830v3",
                    "RAM" => "64GBDDR4",
                    "RamValue" => "64GB",
                    "HDD" => "2x120GBSSD",
                    "Storage" => 240,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$2141.99"
                ],
                [
                    "Model" => "Dell R9304x Intel Xeon E7-4850v3",
                    "RAM" => "64GBDDR4",
                    "RamValue" => "64GB",
                    "HDD" => "2x120GBSSD",
                    "Storage" => 240,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$2412.99"
                ],
                [
                    "Model" => "HP DL180G62x Intel Xeon E5620",
                    "RAM" => "32GBDDR3",
                    "RamValue" => "32GB",
                    "HDD" => "2x1TBSATA2",
                    "Storage" => 2048,
                    "HardDiskType" => "SATA",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$719.99"
                ],
                [
                    "Model" => "HP DL180G62x Intel Xeon E5620",
                    "RAM" => "32GBDDR3",
                    "RamValue" => "32GB",
                    "HDD" => "2x1TBSATA2",
                    "Storage" => 2048,
                    "HardDiskType" => "SATA",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$569.99"
                ],
                [
                    "Model" => "Dell R9304x Intel Xeon E7-4820v3",
                    "RAM" => "64GBDDR4",
                    "RamValue" => "64GB",
                    "HDD" => "2x120GBSSD",
                    "Storage" => 240,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$1553.99"
                ],
                [
                    "Model" => "Dell R9304x Intel Xeon E7-4830v3",
                    "RAM" => "64GBDDR4",
                    "RamValue" => "64GB",
                    "HDD" => "2x120GBSSD",
                    "Storage" => 240,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$1741.99"
                ],
                [
                    "Model" => "Dell R9304x Intel Xeon E7-4850v3",
                    "RAM" => "64GBDDR4",
                    "RamValue" => "64GB",
                    "HDD" => "2x120GBSSD",
                    "Storage" => 240,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$2012.99"
                ],
                [
                    "Model" => "Dell R9304x Intel Xeon E7-4820v3",
                    "RAM" => "64GBDDR4",
                    "RamValue" => "64GB",
                    "HDD" => "2x120GBSSD",
                    "Storage" => 240,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$4203.99"
                ],
                [
                    "Model" => "Dell R9304x Intel Xeon E7-4830v3",
                    "RAM" => "64GBDDR4",
                    "RamValue" => "64GB",
                    "HDD" => "2x120GBSSD",
                    "Storage" => 240,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$4391.99"
                ],
                [
                    "Model" => "Dell R9304x Intel Xeon E7-4850v3",
                    "RAM" => "64GBDDR4",
                    "RamValue" => "64GB",
                    "HDD" => "2x120GBSSD",
                    "Storage" => 240,
                    "HardDiskType" => "SSD",
                    "Location" => "SingaporeSIN-11",
                    "Price" => "S$4662.99"
                ]
        ]
        ];
    }

    public static function successScenarioWithRamAndStoragePayload(): array{
        return [
            'requestPayload' => [
                "storage" => "0 to 2048",
                "ram" => ["96GB"],
                "hardDiskType" => "",
                "location" => "AmsterdamAMS-01"
            ],
            'expectedOutPut' => [
                [
                    "Model" => "Dell R6202x Intel Xeon E5-2650",
                    "RAM" => "96GBDDR3",
                    "RamValue" => "96GB",
                    "HDD" => "8x120GBSSD",
                    "Storage" => 960,
                    "HardDiskType" => "SSD",
                    "Location" => "AmsterdamAMS-01",
                    "Price" => "€191.99"
                ]
            ]
        ];
    }

    public static function successScenarioWithMultipleRamAndStoragePayload(): array{
        return [
            'requestPayload' => [
                "storage" => "0 to 2048",
                "ram" => ["96GB", "16GB"],
                "hardDiskType" => "",
                "location" => "1"
            ],
            'expectedOutPut' => []
        ];
    }

    public static function successScenarioWithStorageRamAndHardDiskTypePayload(): array{
        return [
            'requestPayload' => [
                "storage" => "0 to 5000",
                "ram" => [],
                "hardDiskType" => "SATA",
                "location" => "AmsterdamAMS-01"
            ],
            'expectedOutPut' => [
                    [
                        "Model" => "Dell R210Intel Xeon X3440",
                        "RAM" => "16GBDDR3",
                        "RamValue" => "16GB",
                        "HDD" => "2x2TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€49.99"
                    ],
                    [
                        "Model" => "Dell R210-IIIntel Xeon E3-1230v2",
                        "RAM" => "16GBDDR3",
                        "RamValue" => "16GB",
                        "HDD" => "2x2TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€72.99"
                    ],
                    [
                        "Model" => "HP DL120G7Intel G850",
                        "RAM" => "4GBDDR3",
                        "RamValue" => "4GB",
                        "HDD" => "4x1TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€39.99"
                    ],
                    [
                        "Model" => "Dell R210-IIIntel G530",
                        "RAM" => "4GBDDR3",
                        "RamValue" => "4GB",
                        "HDD" => "2x500GBSATA2",
                        "Storage" => 1000,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€35.99"
                    ],
                    [
                        "Model" => "Dell R210-IIIntel Xeon E3-1220",
                        "RAM" => "16GBDDR3",
                        "RamValue" => "16GB",
                        "HDD" => "2x1TBSATA2",
                        "Storage" => 2048,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€59.99"
                    ],
                    [
                        "Model" => "Dell R210-IIIntel Xeon E3-1270v2",
                        "RAM" => "16GBDDR3",
                        "RamValue" => "16GB",
                        "HDD" => "2x1TBSATA2",
                        "Storage" => 2048,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€89.99"
                    ],
                    [
                        "Model" => "HP DL120G7Intel Xeon E3-1230",
                        "RAM" => "16GBDDR3",
                        "RamValue" => "16GB",
                        "HDD" => "4x1TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€84.99"
                    ],
                    [
                        "Model" => "DL20G9Intel Xeon E3-1270v5",
                        "RAM" => "16GBDDR4",
                        "RamValue" => "16GB",
                        "HDD" => "2x1TBSATA2",
                        "Storage" => 2048,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€112.99"
                    ],
                    [
                        "Model" => "Dell R210-IIIntel Xeon E3-1230v2",
                        "RAM" => "16GBDDR3",
                        "RamValue" => "16GB",
                        "HDD" => "2x2TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€72.99"
                    ],
                    [
                        "Model" => "HP DL120G7Intel Xeon E3-1230",
                        "RAM" => "16GBDDR3",
                        "RamValue" => "16GB",
                        "HDD" => "4x1TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€195.99"
                    ],
                    [
                        "Model" => "HP DL120G7Intel G850",
                        "RAM" => "4GBDDR3",
                        "RamValue" => "4GB",
                        "HDD" => "4x1TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€163.99"
                    ],
                    [
                        "Model" => "Dell R210-IIIntel Xeon E3-1230v2",
                        "RAM" => "16GBDDR3",
                        "RamValue" => "16GB",
                        "HDD" => "2x500GBSATA2",
                        "Storage" => 1000,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€119.99"
                    ],
                    [
                        "Model" => "Dell R210-IIIntel G530",
                        "RAM" => "4GBDDR3",
                        "RamValue" => "4GB",
                        "HDD" => "2x500GBSATA2",
                        "Storage" => 1000,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€60.99"
                    ],
                    [
                        "Model" => "Dell R210-IIIntel Xeon E3-1220",
                        "RAM" => "16GBDDR3",
                        "RamValue" => "16GB",
                        "HDD" => "2x1TBSATA2",
                        "Storage" => 2048,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€110.99"
                    ],
                    [
                        "Model" => "Dell R210Intel Xeon X3440",
                        "RAM" => "16GBDDR3",
                        "RamValue" => "16GB",
                        "HDD" => "2x2TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€83.99"
                    ],
                    [
                        "Model" => "HP DL120G7Intel G850",
                        "RAM" => "4GBDDR3",
                        "RamValue" => "4GB",
                        "HDD" => "4x1TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€80.99"
                    ],
                    [
                        "Model" => "Dell R210-IIIntel G530",
                        "RAM" => "4GBDDR3",
                        "RamValue" => "4GB",
                        "HDD" => "2x500GBSATA2",
                        "Storage" => 1000,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€40.99"
                    ],
                    [
                        "Model" => "Dell R210Intel Xeon X3440",
                        "RAM" => "16GBDDR3",
                        "RamValue" => "16GB",
                        "HDD" => "2x2TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€63.99"
                    ],
                    [
                        "Model" => "Dell R210-IIIntel Xeon E3-1220",
                        "RAM" => "16GBDDR3",
                        "RamValue" => "16GB",
                        "HDD" => "2x1TBSATA2",
                        "Storage" => 2048,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€90.99"
                    ],
                    [
                        "Model" => "HP DL120G7Intel G850",
                        "RAM" => "4GBDDR3",
                        "RamValue" => "4GB",
                        "HDD" => "4x1TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€60.99"
                    ],
                    [
                        "Model" => "Dell R210Intel Xeon X3440",
                        "RAM" => "16GBDDR3",
                        "RamValue" => "16GB",
                        "HDD" => "2x2TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€197.99"
                    ],
                    [
                        "Model" => "Dell R210-IIIntel G530",
                        "RAM" => "4GBDDR3",
                        "RamValue" => "4GB",
                        "HDD" => "2x500GBSATA2",
                        "Storage" => 1000,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€174.99"
                    ],
                    [
                        "Model" => "Dell R210-IIIntel Xeon E3-1220",
                        "RAM" => "16GBDDR3",
                        "RamValue" => "16GB",
                        "HDD" => "2x1TBSATA2",
                        "Storage" => 2048,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€224.99"
                    ],
                    [
                        "Model" => "HP DL120G7Intel G850",
                        "RAM" => "4GBDDR3",
                        "RamValue" => "4GB",
                        "HDD" => "4x1TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€194.99"
                    ],
                    [
                        "Model" => "Dell R720XD2x Intel Xeon E5-2620",
                        "RAM" => "8GBDDR3",
                        "RamValue" => "8GB",
                        "HDD" => "4x1TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€1907.99"
                    ],
                    [
                        "Model" => "Dell R720XD2x Intel Xeon E5-2650",
                        "RAM" => "8GBDDR3",
                        "RamValue" => "8GB",
                        "HDD" => "4x1TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€1973.99"
                    ],
                    [
                        "Model" => "HP DL380pG82x Intel Xeon E5-2620",
                        "RAM" => "8GBDDR3",
                        "RamValue" => "8GB",
                        "HDD" => "4x1TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€1907.99"
                    ],
                    [
                        "Model" => "HP DL380pG82x Intel Xeon E5-2650",
                        "RAM" => "8GBDDR3",
                        "RamValue" => "8GB",
                        "HDD" => "4x1TBSATA2",
                        "Storage" => 4096,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€1967.99"
                    ],
                    [
                        "Model" => "HP DL120G7Intel G850",
                        "RAM" => "4GBDDR3",
                        "RamValue" => "4GB",
                        "HDD" => "4x500GBSATA2",
                        "Storage" => 2000,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€1775.99"
                    ],
                    [
                        "Model" => "HP DL120G7Intel Xeon E3-1230",
                        "RAM" => "8GBDDR3",
                        "RamValue" => "8GB",
                        "HDD" => "4x500GBSATA2",
                        "Storage" => 2000,
                        "HardDiskType" => "SATA",
                        "Location" => "AmsterdamAMS-01",
                        "Price" => "€1807.99"
                    ]
                ]
        ];
    }

    public static function failurePayloadWithWrongRam(): array
    {
        return [
            'requestPayload' => [
                "storage" => "0 to 2048",
                "ram" => ["165GB"],
                "hardDiskType" => "SATA",
                "location" => "AmsterdamAMS-01"
            ],
            'expectedOutPut' => ["The option \"ram\" with value array is invalid."]
        ];
    }

    public static function failurePayloadWithWrongHardDiskType(): array
    {
        return [
            'requestPayload' => [
                "storage" => "0 to 2048",
                "ram" => ["165GB"],
                "hardDiskType" => "SATATest",
                "location" => "AmsterdamAMS-01"
            ],
            'expectedOutPut' => ["The option \"hardDiskType\" with value \"SATATest\" is invalid."]
        ];
    }
}
