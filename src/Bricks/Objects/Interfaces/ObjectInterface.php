<?php

namespace Bricks\Objects\Interfaces;

interface ObjectInterface
    extends \JsonSerializable
{
    public function getCollectionUri();

    public function getSelfUri();
}
