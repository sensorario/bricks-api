<?php

use Silex\WebTestCase;

class InsightTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../app/app.php';
    }

    public function setUp()
    {
        parent::setUp();
    }

    public function testInsightEndpointReturnRightResponse()
    {
        $this->markTestIncomplete(
            'Complete this test ASAP'
        );
    }
}
