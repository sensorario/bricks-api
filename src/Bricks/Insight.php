<?php

namespace Bricks;

use DateTime;
use DateTimeZone;
use JsonSerializable;
use Symfony\Component\HttpFoundation\Request;

final class Insight implements JsonSerializable
{
    private $shop;

    private $set;

    private $value;

    private $update;

    private function __construct(array $properties)
    {
        $this->shop = $properties['shop'];
        $this->set = $properties['set'];
        $this->value = $properties['value'];
        $this->update = new DateTime('now');
    }

    public static function fromRequest(Request $request)
    {
        return new self([
            'shop' => $request->request->get('shop'),
            'set' => $request->request->get('set'),
            'value' => $request->request->get('value'),
        ]);
    }

    public function jsonSerialize()
    {
        return [
            'shop' => $this->shop,
            'set' => $this->set,
            'value' => $this->value,
            'update' => $this->update->setTimeZone(new DateTimeZone('UTC')),
        ];
    }

    public function getTimestamp()
    {
        return $this->update->getTimestamp();
    }

    public function getSet()
    {
        return $this->set;
    }

    public function getShop()
    {
        return $this->shop;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getUpdate()
    {
        return $this->update;
    }
}
