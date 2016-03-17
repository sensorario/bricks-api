<?php

namespace Bricks;

use DateTime;
use DateTimeZone;
use JsonSerializable;

final class Set implements JsonSerializable
{
    private $code;

    private $update;

    private function __construct(array $properties)
    {
        $this->code = $properties['code'];
        $this->update = new DateTime('now');
    }

    public static function fromCode($code)
    {
        return new self([
            'code' => $code,
        ]);
    }

    public function jsonSerialize()
    {
        return [
            'code' => $this->code,
            'update' => $this->update->setTimeZone(new DateTimeZone('UTC')),
            'links' => [[
                'rel' => 'self',
                'href' => 'http://localhost:8080/api/v1/set/' . $this->code,
            ],[
                'rel' => 'collection',
                'href' => 'http://localhost:8080/api/v1/sets/',
            ],],
        ];
    }

    public function getCode()
    {
        return $this->code;
    }
}
