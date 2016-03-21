<?php

namespace Bricks\Services;

use Bricks\Files;
use Bricks\Response\ErrorResponse;
use Bricks\Services\Persist;
use Silex\WebTestCase;

class SetCreationTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../app/app.php';
    }

    public function setUp()
    {
        parent::setUp();

        $this->set = [
            'code' => 'fake' . rand(11111111111111111111111111, 99999999999999999999999999),
            'name' => 'foo',
            'pieces' => '7',
        ];

        $this->client = $this->createClient();

        $this->generator = new NamesGenerator();
        $this->fileName = $this->generator->generateName(
            \Bricks\Objects\Set::box(array_merge(
                $this->set,
                ['update' => new \DateTime()]
            ))
        );
    }

    public function testAfterPostFileMustExists()
    {
        $this->client->request(
            'POST',
            'http://localhost::8080/api/v1/set/', 
            $this->set
        );

        $this->assertTrue(
            file_exists($this->fileName)
        );
    }

    public function testSavesNewLineEachTimeIsCalled()
    {
        $fileAsArray = file($this->fileName);
        $recordStoredUntilNow = count($fileAsArray);

        $this->client->request(
            'POST',
            'http://localhost::8080/api/v1/set/', 
            $this->set
        );

        $fileAsArray = file($this->fileName);

        $this->assertEquals(
            $recordStoredUntilNow + 1,
            count(file($this->fileName))
        );
    }

    public function testAllSetsAreShownViaGetRequest()
    {
        $this->client->request(
            'GET',
            'http://localhost::8080/api/v1/sets/', 
            $this->set
        );

        print_r(json_encode($this->client->getResponse()->getContent());
    }
}
