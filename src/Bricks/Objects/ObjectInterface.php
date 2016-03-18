<?php

namespace Bricks\Objects;

interface ObjectInterface
    extends \JsonSerializable
{
    public function getCollectionUri();

    public function getSelfUri();
}
