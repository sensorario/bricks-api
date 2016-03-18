<?php

namespace Bricks\Response;

final class Response
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
        if ($key == 'links') {
            foreach ($value as $link) {
                $newObject = $this->withLink($link);
            }

            return $newObject;
        }

        return new self(array_merge(
            $this->properties,
            [$key => $value]
        ));
    }

    public function withLink($route)
    {
        $newProperties = $this->properties;
        $route['href'] = 'http://localhost:8080/api/v1' . $route['href'];
        if (isset($newProperties['links'])) {
            $newProperties['links'][] = $route;
        } else {
            $newProperties['links'] = [$route];
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
