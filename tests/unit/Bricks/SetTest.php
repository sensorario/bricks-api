<?php

namespace Bricks;

use PHPUnit_Framework_TestCase;

final class SetTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $this->randomCode = rand(11111, 99999);
        $this->set = Set::fromCode($this->randomCode);
        $this->assertEquals(
            $this->randomCode,
            $this->set->getCode()
        );
    }
}
