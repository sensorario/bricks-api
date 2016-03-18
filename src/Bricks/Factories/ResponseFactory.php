<?php

namespace Bricks\Factories;

use Bricks\Objects\Insight;
use Bricks\Objects\Set;
use Bricks\Objects\Shop;
use Bricks\Response\Response;

/** @todo inject a service that count resources */
class ResponseFactory
{
    public function getHomepage()
    {
        return $this->getEmptyResponse()
            ->withLink('sets', '/sets/')
            ->withLink('shops', '/shops/')
            ->withLink('insights', '/insights/')
            ->withLink('stats', '/stats/');
    }

    private function getEmptyResponse()
    {
        return Response::createEmpty()
            ->withLink('homepage', '/homepage/');
    }

    public function getStatistics()
    {
        return $this->getEmptyResponse()
            ->withKeyValue('sets', count(file('app/data/bricks.objects.set')))
            ->withKeyValue('insights', count(file('app/data/bricks.objects.insight')))
            ->withKeyValue('shops', count(file('app/data/bricks.objects.shop')))
            ->withLink('self', '/stats/');
    }

    public function getInsight(Insight $item)
    {
        return $this->getEmptyResponse()
            ->withKeyValue('shop', $item->get('shop'))
            ->withKeyValue('set', $item->get('set'))
            ->withKeyValue('value', $item->get('value'))
            ->withKeyValue('update', $item->get('update'))

            ->withLink('self', '/insight/' . $item->getTimestamp())
            ->withLink('collection', '/insights/')
        ;
    }

    public function getSet(Set $item)
    {
        return $this->getEmptyResponse()
            ->withKeyValue('code', $item->get('code'))
            ->withKeyValue('update', $item->get('update'))
            ->withKeyValue('pieces', $item->get('pieces'))
            ->withKeyValue('name', $item->get('name'))

            ->withLink('self', '/set/' . $item->get('code'))
            ->withLink('collection', '/sets/')
        ;
    }

    public function getShop(Shop $item)
    {
        return $this->getEmptyResponse()
            ->withKeyValue('name', $item->get('name'))
            ->withKeyValue('slug', $item->getSlug())
            ->withKeyValue('address', $item->get('address'))
            ->withKeyValue('update', $item->get('update'))

            ->withLink('self', '/insight/' . $item->get('update')->getTimestamp())
            ->withLink('collection', '/insights/')
        ;
    }

    public function getCollection($link, $collection, $links)
    {
        return $this->getEmptyResponse()
            ->withKeyValue('collection', $collection)
            ->withKeyValue('links', $links)
            ->withLink('collection', $link);
    }
}
