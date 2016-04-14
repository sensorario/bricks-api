<?php

namespace Bricks\Objects\Interfaces;

interface ObjectInterface
    extends \JsonSerializable
{
    public function getSelfUri();
}
