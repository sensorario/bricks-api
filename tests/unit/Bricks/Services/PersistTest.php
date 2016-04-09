<?php

namespace Bricks\Services;

use PHPUnit_Framework_TestCase;

final class PersistTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        @unlink($this->persist->getFileName());

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
            ->will($this->returnValue(__DIR__ . '/../../../../app/data/bricks.objects.objectinterface'));

        $this->persist = new Persist(
            $this->objectInterface,
            $this->namesGenerator,
            $this->loggerInterface
        );
    }

    public function testFileNameIsBasedOnObjectClassName()
    {
        $this->assertEquals(
            __DIR__ . '/../../../../app/data/bricks.objects.objectinterface',
            $this->persist->getFileName()
        );
    }

    public function testDeleteFile()
    {
        $this->markTestIncomplete();
    }

    public function testPersistDataInsideAppDataFolder()
    {
        $this->persist->persist();

        $this->assertTrue(
            file_exists(
                $this->persist->getFileName()
            )
        );
    }

    public function testSaveObjectSerializationInsideTheFile()
    {
        $this->markTestIncomplete();
    }

    public function testTotalRecordCountZeroWhenFileDoesNotExists()
    {
        $this->markTestIncomplete();
    }

    public function tearDown()
    {
        @unlink($this->persist->getFileName());
    }
} 
