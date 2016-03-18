<?php

namespace Bricks\Factories;

use Bricks\Response\Response;

/** @todo inject a service that count resources */
class ResponseFactory
{
    public function getStatistics()
    {
        return Response::createEmpty()
            ->withKeyValue('sets', count(file('app/data/bricks.set')))
            ->withKeyValue('insights', count(file('app/data/bricks.insight')))
            ->withKeyValue('shops', count(file('app/data/bricks.shop')))
            ->withLink(['rel' => 'homepage', 'href' => '/homepage/'])
            ->withLink(['rel' => 'self', 'href' => '/stats/']);
    }
}
