<?php

namespace Bricks\Factories;

use PHPUnit_Framework_TestCase;

final class ResponseFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testBaseResponseJustContainsHomePageUrl()
    {
        $factory = new ResponseFactory();

        $response = \Bricks\Response\Response::createEmpty()
            ->withLink('homepage', '/homepage/');

        $this->assertEquals(
            $response,
            $factory->getBaseResponse()
        );
    }
}
