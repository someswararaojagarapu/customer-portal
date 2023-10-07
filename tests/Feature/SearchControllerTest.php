<?php

namespace App\CustomerPortal\Tests\Feature;

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
        $this->assertIsArray(json_decode($response->getContent(), true));
    }
}
