<?php

namespace Bricks\Repositories;

use Bricks\Files;

final class BricksRepository
{
    public function findAllBricks()
    {
        return file(Files::RESOURCE_INSIGHT);
    }
}
