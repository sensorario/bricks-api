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
            'links' => [[
                'rel' => 'self',
                'href' => 'http://localhost:8080/api/v1/insight/' . $this->update->getTimestamp(),
            ],[
                'rel' => 'shop',
                'href' => 'http://localhost:8080/api/v1/shop/' . $this->shop,
            ],[
                'rel' => 'set',
                'href' => 'http://localhost:8080/api/v1/set/' . $this->set,
            ],[
                'rel' => 'collection',
                'href' => 'http://localhost:8080/api/v1/insights/',
            ],],
        ];
    }

    public function getTimestamp()
    {
        return $this->update->getTimestamp();
    }
}
