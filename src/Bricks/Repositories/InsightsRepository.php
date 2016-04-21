<?php

namespace Bricks\Repositories;

use Bricks\Files;

final class InsightsRepository
{
    public function findAll()
    {
        return file(Files::RESOURCE_INSIGHT);
    }
}
