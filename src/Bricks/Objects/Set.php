<?php

namespace Bricks\Objects;

use Bricks\Objects\Interfaces\ObjectInterface;
use JsonSerializable;
use Sensorario\ValueObject\ValueObject;

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

    public function getSelfUri()
    {
        return '/set/' . $this->get('code');
    }
}
