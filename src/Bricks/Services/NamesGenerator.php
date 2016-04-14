<?php

namespace Bricks\Services;

use Bricks\Objects\Interfaces\ObjectInterface;

class NamesGenerator
{
    private $fileName;

    public function generateName(ObjectInterface $object)
    {
        $fileName = __DIR__ . '/../../../app/data/' . strtolower(
            str_replace('\\', '.', get_class($object))
        );

        return $fileName;
    }
}
