<?php

namespace Bricks\Services;

use Bricks\Response\ErrorResponse;
use Bricks\Services\Persist;
use Silex\WebTestCase;

class SetCreationTest extends WebTestCase
{
    /** @todo move this in a BrickWebTestCase */
    public function createApplication()
    {
        return require __DIR__ . '/../../app/app.php';
    }

    public function setUp()
    {
        parent::setUp();

        $this->set = [
            'code' => 42,
            'name' => 'foo',
            'pieces' => '7',
        ];

        /** @todo move this in a BrickWebTestCase */
        $this->client = $this->createClient();
    }

    public function testAfterPostFileMustExists()
    {
        $generator = new NamesGenerator();
        $fileName = $generator->generateName(
            \Bricks\Objects\Set::box(array_merge(
                $this->set,
                ['update' => new \DateTime()]
            ))
        );

        $this->client->request(
            'POST',
            '/set/', 
            ['json' => $this->set]
        );

        $this->assertTrue(
            file_exists($fileName)
        );
    }
}
