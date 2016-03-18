<?php

namespace Bricks\Objects;

use Sensorario\ValueObject\ValueObject;
use JsonSerializable;

final class Set
    extends ValueObject
    implements JsonSerializable
{
    public static function mandatory()
    {
        return [
            'code',
            'update'
        ];
    }

    public function jsonSerialize()
    {
        return [
            'code' => $this->get('code'),
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
}
