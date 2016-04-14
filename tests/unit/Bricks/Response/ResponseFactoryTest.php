<?php

namespace Bricks\Response;

use PHPUnit_Framework_TestCase;

final class ResponseFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->factory = new ResponseFactory();

        $this->objectInterface = $this
            ->getMockBuilder('Bricks\\Objects\\Interfaces\\ObjectInterface')
            ->setMethods([
                'jsonSerialize',
                'getSelfUri'
            ])->getMock();

        $this->objectInterface->expects($this->once())
            ->method('jsonSerialize')
            ->will($this->returnValue([
                'foo' => 'bar',
            ]));

        $this->objectInterface->expects($this->once())
            ->method('getSelfUri')
            ->will($this->returnValue('/bar/'));
    }

    public function testRetrieveInformationFromAnObjectInterfaceChild()
    {
        $response = $this->factory->getObject($this->objectInterface);

        $resourceName = $this->factory->getCollectionUri(
            $this->objectInterface
        );

        $this->assertEquals(
            [
                'foo' => 'bar',
                'links' => [
                    [
                        'href' => 'http://localhost:8080/api/v1/homepage/',
                        'rel' => 'homepage',
                    ],[
                        'href' => 'http://localhost:8080/api/v1/bar/',
                        'rel' => 'self',
                    ],[
                        'href' => 'http://localhost:8080/api/v1' . $resourceName,
                        'rel' => 'collection',
                    ],
                ],
            ],
            $response->asArray()
        );
    }
}
