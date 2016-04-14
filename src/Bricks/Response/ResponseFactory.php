<?php

namespace Bricks\Response;

use Bricks\Objects\Interfaces\ObjectInterface;
use Bricks\Response\Response;

class ResponseFactory
{
    public function getBaseResponse()
    {
        return Response::createEmpty()
            ->withLink('homepage', '/homepage/');
    }

    public function getHomepage()
    {
        return $this->getBaseResponse()
            ->withLink('sets', '/sets/')
            ->withLink('shops', '/shops/')
            ->withLink('insights', '/insights/')
            ->withLink('stats', '/stats/');
    }

    public function getStatistics()
    {
        return $this->getBaseResponse()
            ->withKeyValue('sets', count(file('app/data/bricks.objects.set')))
            ->withKeyValue('insights', count(file('app/data/bricks.objects.insight')))
            ->withKeyValue('shops', count(file('app/data/bricks.objects.shop')))
            ->withLink('self', '/stats/');
    }

    public function getCollectionUri(ObjectInterface $object)
    {
        $namespace = get_class($object);
        $className = explode('\\', strtolower($namespace));
        return '/' . end($className);
    }

    public function getObject(ObjectInterface $item)
    {
        $response = $this->getBaseResponse()
            ->withLink('self', $item->getSelfUri())
            ->withLink('collection', $this->getCollectionUri($item));

        foreach ($item->jsonSerialize() as $propertyKey => $value) {
            $response = $response->withKeyValue($propertyKey, $value);
        }

        return $response;
    }

    public function getCollection($link, $collection, $links)
    {
        return $this->getBaseResponse()
            ->withKeyValue('collection', $collection)
            ->withKeyValue('links', $links)
            ->withLink('collection', $link);
    }
}
