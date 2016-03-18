<?php

namespace Bricks\Factories;

use Bricks\Insight;
use Bricks\Response\Response;
use Bricks\Set;
use Bricks\Shop;

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

    public function getInsight(Insight $insight)
    {
        return Response::createEmpty()
            ->withKeyValue('shop', $insight->getShop())
            ->withKeyValue('set', $insight->getSet())
            ->withKeyValue('value', $insight->getValue())
            ->withKeyValue('update', $insight->getUpdate())
            ->withLink(['rel' => 'homepage', 'href' => '/homepage/'])
            ->withLink(['rel' => 'self', 'href' => '/shop/' . $insight->getShop()]);
    }

    public function getHomepage()
    {
        return Response::createEmpty()
            ->withLink(['rel' => 'homepage', 'href' => '/homepage/'])
            ->withLink(['rel' => 'sets', 'href' => '/sets/'])
            ->withLink(['rel' => 'shops', 'href' => '/shops/'])
            ->withLink(['rel' => 'insights', 'href' => '/insights/'])
            ->withLink(['rel' => 'stats', 'href' => '/stats/']);
    }

    public function getLegoSets($sets, $links)
    {
        return Response::createEmpty()
            ->withKeyValue('collection', $sets)
            ->withKeyValue('links', $links)
            ->withLink(['rel' => 'homepage', 'href' => '/homepage/'])
            ->withLink(['rel' => 'collection', 'href' => '/sets/']);
    }

    public function getSet(Set $item)
    {
        return Response::createEmpty()
            ->withKeyValue('code', $item->getCode())
            ->withKeyValue('update', $item->getUpdate())
            ->withLink(['rel' => 'self', 'href' => '/set/' . $item->getCode()])
            ->withLink(['rel' => 'homepage', 'href' => '/homepage/'])
            ->withLink(['rel' => 'collection', 'href' => '/sets/']);
    }

    public function getInsightCollection($insights, $links)
    {
        return Response::createEmpty()
            ->withKeyValue('collection', $insights)
            ->withKeyValue('links', $links)
            ->withLink(['rel' => 'homepage', 'href' => '/homepage/'])
            ->withLink(['rel' => 'collection', 'href' => '/insights/']);
    }

    public function getShops($shops, $links)
    {
        return Response::createEmpty()
            ->withKeyValue('collection', $shops)
            ->withKeyValue('links', $links)
            ->withLink(['rel' => 'homepage', 'href' => '/homepage/'])
            ->withLink(['rel' => 'collection', 'href' => '/shops/']);
    }

    public function getShop(Shop $item)
    {
        return Response::createEmpty()
            ->withKeyValue('name', $item->getName())
            ->withKeyValue('slug', $item->getSlug())
            ->withKeyValue('address', $item->getAddress())
            ->withKeyValue('update', $item->getUpdate())
            ->withLink(['rel' => 'homepage', 'href' => '/homepage/'])
            ->withLink(['rel' => 'self', 'href' => '/shop/' . $item->getSlug()]);
    }
}
