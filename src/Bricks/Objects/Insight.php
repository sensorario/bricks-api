<?php

namespace Bricks\Objects;

use Sensorario\ValueObject\ValueObject;
use JsonSerializable;

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

    /** @codeCoverageIgnore */
    public function getTimestamp()
    {
        return $this->get('update')->getTimestamp();
    }

    /** @codeCoverageIgnore */
    public function getCollectionUri()
    {
        return '/insights/';
    }

    /** @codeCoverageIgnore */
    public function getselfuri()
    {
        return '/insight/' . $this->getTimestamp();
    }
}
