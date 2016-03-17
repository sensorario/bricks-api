<?php

namespace Bricks;

use DateTime;
use DateTimeZone;
use JsonSerializable;
use Symfony\Component\HttpFoundation\Request;

final class Shop implements JsonSerializable
{
    private $name;

    private $address;

    private $slug;

    private $update;

    private function __construct(array $properties)
    {
        $this->name = $properties['name'];
        $this->address = $properties['address'];
        $this->update = new DateTime('now');
        $this->slug = strtolower(
            str_replace(' ', '-', $this->name)
        );
    }

    public static function fromRequest(Request $request)
    {
        return new self([
            'name' => $request->request->get('name'),
            'address' => $request->request->get('address'),
        ]);
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'update' => $this->update->setTimeZone(new DateTimeZone('UTC')),
            'slug' => $this->slug,
            'links' => [[
                'rel' => 'self',
                'href' => 'http://localhost:8080/api/v1/shop/' . $this->slug,
            ],[
                'rel' => 'collection',
                'href' => 'http://localhost:8080/api/v1/shops/',
            ],],
        ];
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getSlug()
    {
        return $this->slug;
    }
}
