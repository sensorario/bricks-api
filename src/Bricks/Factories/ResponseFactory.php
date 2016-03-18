<?php

namespace Bricks\Factories;

use Bricks\Objects\ObjectInterface;
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

    public function getObject(ObjectInterface $item)
    {
        $response = $this->getEmptyResponse()
            ->withLink('self', $item->getSelfUri())
            ->withLink('collection', $item->getCollectionUri());

        foreach ($item->jsonSerialize() as $propertyKey => $value) {
            $response = $response->withKeyValue($propertyKey, $value);
        }

        return $response;
    }

    public function getCollection($link, $collection, $links)
    {
        return $this->getEmptyResponse()
            ->withKeyValue('collection', $collection)
            ->withKeyValue('links', $links)
            ->withLink('collection', $link);
    }
}
