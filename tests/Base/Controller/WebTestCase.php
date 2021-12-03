<?php

declare(strict_types=1);

namespace tests\Base\Controller;

use Symfony\Component\{
    DomCrawler\Crawler,
    HttpFoundation\Response
};
use Symfony\Bundle\FrameworkBundle\Client;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    protected $apiKey = '';

    public function setUp()
    {
        $this->client = static::createClient();
        $this->apiKey = '';
    }

    protected function assertJsonResponse(Response $response, $statusCode = 200): void
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            json_encode($response->headers)
        );
    }

    protected function jsonRequest(string $verb, string $endpoint, array $data = []): Crawler
    {
        $data = empty($data) ? null : json_encode($data);
        return $this->client->request($verb, $endpoint,
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_APIKEY' => $this->apiKey
            ],
            $data
        );
    }
}
