<?php

namespace Bricks\Objects;

use PHPUnit_Framework_TestCase;

final class SetTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->randomCode = rand(11111, 99999);
        $this->set = Set::fromCode($this->randomCode);
    }

    public function testHasCode()
    {
        $this->assertEquals(
            $this->randomCode,
            $this->set->getCode()
        );
    }

    public function testJsonSerialization()
    {
        $this->assertTrue(
            gettype($this->set->jsonSerialize()) == 'array'
        );
    }
}
