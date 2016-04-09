<?php

namespace Bricks\Objects;

use Sensorario\ValueObject\ValueObject;
use JsonSerializable;

final class Shop
    extends ValueObject
    implements ObjectInterface
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
            'update' => $this->get('update'),
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

    public function getCollectionUri()
    {
        return '/shops/';
    }

    public function getSelfUri()
    {
        return '/shop/' . $this->getSlug();
    }
}
