<?php

namespace Bricks\Objects;

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

    public function getName()
    {
        return $this->name;
    }

    public function getUpdate()
    {
        return $this->update;
    }
}
