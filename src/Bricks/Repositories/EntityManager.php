<?php

namespace Bricks\Repositories;

use Bricks\Files;
use RuntimeException;

final class EntityManager
{
    public function getRepository($repositoryName)
    {
        if ($repositoryName === 'Bricks') {
            return new BricksRepository();
        }

        if ($repositoryName === 'Shops') {
            return new ShopsRepository();
        }

        if ($repositoryName === 'Insights') {
            return new InsightsRepository();
        }

        throw new RuntimeException(
            $repositoryName . " is not a valid repository name."
        );
    }
}
