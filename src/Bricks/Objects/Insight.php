<?php

namespace Bricks\Objects;

use Bricks\Objects\Interfaces\ObjectInterface;
use JsonSerializable;
use Sensorario\ValueObject\ValueObject;

final class Insight
    extends ValueObject
    implements ObjectInterface
{
    public static function mandatory()
    {
        return [
            'shop',
            'set',
            'value',
            'update',
        ];
    }

    public function jsonSerialize()
    {
        return [
            'shop' => $this->get('shop'),
            'set' => $this->get('set'),
            'value' => $this->get('value'),
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

    public function getTimestamp()
    {
        return $this->get('update')->getTimestamp();
    }

    public function getselfuri()
    {
        return '/insight/' . $this->getTimestamp();
    }
}
