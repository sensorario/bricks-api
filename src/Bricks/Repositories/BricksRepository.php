<?php

namespace Bricks\Repositories;

use Bricks\Files;

final class BricksRepository
{
    public function findAll()
    {
        return file(Files::RESOURCE_SET);
    }

    public function deleteAll()
    {
        unlink(Files::RESOURCE_SET);
    }
}
