<?php

namespace Bricks\Factories;

use Bricks\Objects\Insight;
use Bricks\Objects\Set;
use Bricks\Objects\Shop;
use Bricks\Response\Response;

/** @todo inject a service that count resources */
class ResponseFactory
{
    public function getStatistics()
    {
        return $this->getEmptyResponse()
            ->withKeyValue('sets', count(file('app/data/bricks.objects.set')))
            ->withKeyValue('insights', count(file('app/data/bricks.objects.insight')))
            ->withKeyValue('shops', count(file('app/data/bricks.objects.shop')))
            ->withLink('self', '/stats/');
    }

    private function getEmptyResponse()
    {
        return Response::createEmpty()
            ->withLink('homepage', '/homepage/');
    }

    public function getInsight(Insight $insight)
    {
        return $this->getEmptyResponse()
            ->withKeyValue('shop', $insight->getShop())
            ->withKeyValue('set', $insight->getSet())
            ->withKeyValue('value', $insight->getValue())
            ->withKeyValue('update', $insight->getUpdate())
            ->withLink('self', '/shop/' . $insight->getShop());
    }

    public function getHomepage()
    {
        return $this->getEmptyResponse()
            ->withLink('sets', '/sets/')
            ->withLink('shops', '/shops/')
            ->withLink('insights', '/insights/')
            ->withLink('stats', '/stats/');
    }

    public function getSet(Set $item)
    {
        return $this->getEmptyResponse()
            ->withKeyValue('code', $item->getCode())
            ->withKeyValue('update', $item->getUpdate())
            ->withLink('self', '/set/' . $item->getCode())
            ->withLink('collection', '/sets/');
    }

    public function getCollection($link, $collection, $links)
    {
        return $this->getEmptyResponse()
            ->withKeyValue('collection', $collection)
            ->withKeyValue('links', $links)
            ->withLink('collection', $link);
    }

    public function getShop(Shop $item)
    {
        return $this->getEmptyResponse()
            ->withKeyValue('name', $item->getName())
            ->withKeyValue('slug', $item->getSlug())
            ->withKeyValue('address', $item->getAddress())
            ->withKeyValue('update', $item->getUpdate())
            ->withLink('self', '/shop/' . $item->getSlug());
    }
}
