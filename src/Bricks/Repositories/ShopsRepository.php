<?php

namespace Bricks\Repositories;

use Bricks\Files;

final class ShopsRepository
{
    public function findAll()
    {
        return file(Files::RESOURCE_SHOP);
    }
}
