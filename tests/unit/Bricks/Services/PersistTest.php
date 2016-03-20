<?php

namespace Bricks\Services;

use PHPUnit_Framework_TestCase;

final class PersistTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->loggerInterface = $this
            ->getMock('Psr\Log\LoggerInterface');

        $this->objectInterface = $this
            ->getMock('Bricks\\Objects\\ObjectInterface');

        $this->namesGenerator = $this
            ->getMockBuilder('Bricks\\Services\\NamesGenerator')
            ->setMethods(['generateName'])
            ->getMock();

        $this->namesGenerator->expects($this->once())
            ->method('generateName')
            ->with($this->objectInterface)
            ->will($this->returnValue(__DIR__ . '/../../../../bricks.objects.objectinterface'));

        $this->persist = new Persist(
            $this->objectInterface,
            $this->namesGenerator,
            $this->loggerInterface
        );
    }

    public function testFileNameIsBasedOnObjectClassName()
    {
        $this->assertEquals(
            __DIR__ . '/../../../../bricks.objects.objectinterface',
            $this->persist->getFileName()
        );
    }

    public function testDeleteFile()
    {
        $this->markTestIncomplete();
    }

    public function testCreateFileIfNotExists()
    {
        $this->markTestIncomplete();
    }

    public function testSaveObjectSerializationInsideTheFile()
    {
        $this->markTestIncomplete();
    }

    public function testTotalRecordCountZeroWhenFileDoesNotExists()
    {
        $this->markTestIncomplete();
    }
} 
