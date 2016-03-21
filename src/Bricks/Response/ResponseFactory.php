<?php

namespace Bricks\Response;

use Bricks\Objects\ObjectInterface;
use Bricks\Response\Response;

class ResponseFactory
{
    /** @codeCoverageIgnore */
    public function getHomepage()
    {
        return $this->getBaseResponse()
            ->withLink('sets', '/sets/')
            ->withLink('shops', '/shops/')
            ->withLink('insights', '/insights/')
            ->withLink('stats', '/stats/');
    }

    /** @codeCoverageIgnore */
    public function getBaseResponse()
    {
        return Response::createEmpty()
            ->withLink('homepage', '/homepage/');
    }

    /** @codeCoverageIgnore */
    public function getStatistics()
    {
        return $this->getBaseResponse()
            ->withKeyValue('sets', count(file('app/data/bricks.objects.set')))
            ->withKeyValue('insights', count(file('app/data/bricks.objects.insight')))
            ->withKeyValue('shops', count(file('app/data/bricks.objects.shop')))
            ->withLink('self', '/stats/');
    }

    public function getObject(ObjectInterface $item)
    {
        $response = $this->getBaseResponse()
            ->withLink('self', $item->getSelfUri())
            ->withLink('collection', $item->getCollectionUri());

        foreach ($item->jsonSerialize() as $propertyKey => $value) {
            $response = $response->withKeyValue($propertyKey, $value);
        }

        return $response;
    }

    /** @codeCoverageIgnore */
    public function getCollection($link, $collection, $links)
    {
        return $this->getBaseResponse()
            ->withKeyValue('collection', $collection)
            ->withKeyValue('links', $links)
            ->withLink('collection', $link);
    }
}
