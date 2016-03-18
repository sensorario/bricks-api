<?php

namespace Bricks\Response;

use PHPUnit_Framework_TestCase;

final class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->emptyResponse = Response::createEmpty();
    }

    public function testDefaultResponseIsAnEmptyArray()
    {
        $this->assertEquals([], $this->emptyResponse->asArray());
    }

    public function testAcceptContentAsKeyValuePairs()
    {
        $newResponse = $this->emptyResponse->withKeyValue('foo', 'bar');
        $this->assertEquals(
            ['foo' => 'bar'],
            $newResponse->asArray()
        );
    }

    public function testNewKeyValuesAreAddedToTheResponse()
    {
        $newResponse = $this->emptyResponse->withKeyValue('foo', 'bar');
        $newResponse = $newResponse->withKeyValue('bar', 'foo');
        
        $expectedArray = [
            'foo' => 'bar',
            'bar' => 'foo',
        ];

        $this->assertEquals(
            $expectedArray,
            $newResponse->asArray()
        );
    }

    public function testLinkCanBeAddedARuntime()
    {
        $newResponse = $this->emptyResponse->withKeyValue('foo', 'bar');
        $newResponse = $newResponse->withKeyValue('bar', 'foo');

        $linkToAdd = [
            'rel' => 'self',
            'href' => 'http://localhost:8080/api/v1/foo/'
        ];

        $expectedArray = [
            'foo' => 'bar',
            'bar' => 'foo',
            'links' => [$linkToAdd],
        ];

        $newResponse = $newResponse->withLink($linkToAdd);

        $this->assertEquals(
            $expectedArray,
            $newResponse->asArray()
        );
    }

    public function testAcceptMoreLinks()
    {
        $newResponse = $this->emptyResponse->withKeyValue('foo', 'bar');
        $newResponse = $newResponse->withKeyValue('bar', 'foo');

        $linkToAdd = [
            'rel' => 'self',
            'href' => 'http://localhost:8080/api/v1/foo/'
        ];

        $expectedArray = [
            'foo' => 'bar',
            'bar' => 'foo',
            'links' => [
                $linkToAdd,
                $linkToAdd,
                $linkToAdd,
            ],
        ];

        $newResponse = $newResponse->withLink($linkToAdd);
        $newResponse = $newResponse->withLink($linkToAdd);
        $newResponse = $newResponse->withLink($linkToAdd);

        $this->assertEquals(
            $expectedArray,
            $newResponse->asArray()
        );
    }
}
