<?php

namespace YourApp\Tests;

use Silex\WebTestCase;
use Bricks\Response\ErrorResponse;

class InvalidRequestTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../app/app.php';
    }

    public function setUp()
    {
        parent::setUp();

        $this->client = $this->createClient();
        $this->crawler = $this->client->request('GET', '/invalid-request');
    }

    public function testInvalidRequestReturn404ResponsePage()
    {
        $this->assertEquals(
            404,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testErrorPageMessageContent()
    {
        $this->assertEquals(
            json_encode(ErrorResponse::withMessage('Invalid request')
                ->jsonSerialize()),
            $this->client->getResponse()->getContent()
        );
    }
}
