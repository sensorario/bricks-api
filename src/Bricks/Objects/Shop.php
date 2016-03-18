<?php

namespace Bricks\Objects;

use Sensorario\ValueObject\ValueObject;
use JsonSerializable;

final class Shop
    extends ValueObject
    implements JsonSerializable
{
    public static function mandatory()
    {
        return [
            'name',
            'address',
            'update',
        ];
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->get('name'),
            'address' => $this->get('address'),
            'slug' => $this->getSlug(),
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

    public function getSlug()
    {
        return strtolower(
            str_replace(' ', '-', $this->get('name'))
        );
    }
}
