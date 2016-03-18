<?php

namespace Bricks\Objects;

use Sensorario\ValueObject\ValueObject;
use JsonSerializable;

final class Set
    extends ValueObject
    implements ObjectInterface
{
    public static function mandatory()
    {
        return [
            'code',
            'name',
            'pieces',
            'update'
        ];
    }

    public function jsonSerialize()
    {
        return [
            'code' => $this->get('code'),
            'name' => $this->get('name'),
            'pieces' => $this->get('pieces'),
            'update' => $this->get('update')
                ->setTimeZone(new \DateTimeZone('UTC')),
        ];
    }

    public static function rules()
    {
        return [
            'update' => [
                'object' => 'DateTime'
            ]
        ];
    }

    public function getCollectionUri()
    {
        return '/sets/';
    }

    public function getSelfuri()
    {
        return '/set/' . $this->get('code');
    }
}
