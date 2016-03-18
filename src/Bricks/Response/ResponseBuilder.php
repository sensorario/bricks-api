<?php

namespace Bricks\Response;

final class ResponseBuilder
{
    private $properties;

    private function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    public static function createEmpty()
    {
        return new self([]);
    }

    public function withKeyValue($key, $value)
    {
        return new self(array_merge(
            $this->properties,
            [$key => $value]
        ));
    }

    public function withLink($link)
    {
        $newProperties = $this->properties;
        if (isset($newProperties['links'])) {
            $newProperties['links'][] = $link;
        } else {
            $newProperties['links'] = [$link];
        }

        return new self(
            $newProperties
        );
    }

    public function asArray()
    {
        return $this->properties;
    }
}
