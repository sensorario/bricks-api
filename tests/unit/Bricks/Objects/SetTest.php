<?php

namespace Bricks\Objects;

use PHPUnit_Framework_TestCase;

final class SetTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->randomCode = rand(11111, 99999);
        $this->set = Set::box([
            'code' => $this->randomCode,
            'update' => new \DateTime('now'),
        ]);
    }

    public function testHasCode()
    {
        $this->assertEquals(
            $this->randomCode,
            $this->set->get('code')
        );
    }

    public function testJsonSerialization()
    {
        $this->assertTrue(
            gettype($this->set->jsonSerialize()) == 'array'
        );
    }
}
