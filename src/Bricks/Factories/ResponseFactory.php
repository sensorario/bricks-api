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
            ->withLink('homepage', '/homepage/')
            ->withLink('self', '/stats/');
    }

    public function getInsight(Insight $insight)
    {
        return Response::createEmpty()
            ->withKeyValue('shop', $insight->getShop())
            ->withKeyValue('set', $insight->getSet())
            ->withKeyValue('value', $insight->getValue())
            ->withKeyValue('update', $insight->getUpdate())
            ->withLink('homepage', '/homepage/')
            ->withLink('self', '/shop/' . $insight->getShop());
    }

    public function getHomepage()
    {
        return Response::createEmpty()
            ->withLink('homepage', '/homepage/')
            ->withLink('sets', '/sets/')
            ->withLink('shops', '/shops/')
            ->withLink('insights', '/insights/')
            ->withLink('stats', '/stats/');
    }

    public function getLegoSets($sets, $links)
    {
        return Response::createEmpty()
            ->withKeyValue('collection', $sets)
            ->withKeyValue('links', $links)
            ->withLink('homepage', '/homepage/')
            ->withLink('collection', '/sets/');
    }

    public function getSet(Set $item)
    {
        return Response::createEmpty()
            ->withKeyValue('code', $item->getCode())
            ->withKeyValue('update', $item->getUpdate())
            ->withLink('self', '/set/' . $item->getCode())
            ->withLink('homepage', '/homepage/')
            ->withLink('collection', '/sets/');
    }

    public function getInsightCollection($insights, $links)
    {
        return Response::createEmpty()
            ->withKeyValue('collection', $insights)
            ->withKeyValue('links', $links)
            ->withLink('homepage', '/homepage/')
            ->withLink('collection', '/insights/');
    }

    public function getShops($shops, $links)
    {
        return Response::createEmpty()
            ->withKeyValue('collection', $shops)
            ->withKeyValue('links', $links)
            ->withLink('homepage', '/homepage/')
            ->withLink('collection', '/shops/');
    }

    public function getShop(Shop $item)
    {
        return Response::createEmpty()
            ->withKeyValue('name', $item->getName())
            ->withKeyValue('slug', $item->getSlug())
            ->withKeyValue('address', $item->getAddress())
            ->withKeyValue('update', $item->getUpdate())
            ->withLink('homepage', '/homepage/')
            ->withLink('self', '/shop/' . $item->getSlug());
    }
}
