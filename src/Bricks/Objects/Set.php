<?php

namespace Bricks\Objects;

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
        ];
    }


    public function getCode()
    {
        return $this->code;
    }
    
    public function getUpdate()
    {
        return $this->update;
    }
}
