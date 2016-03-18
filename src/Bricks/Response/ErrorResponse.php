<?php

namespace Bricks\Response;

use JsonSerializable;

final class ErrorResponse implements JsonSerializable
{
    private function __construct(array $parameters)
    {
        $this->status = $parameters['status'];
        $this->code = $parameters['code'];
        $this->message = $parameters['message'];
    }

    public static function withDefaultMessage()
    {
        return new self([
            'status' => 'error',
            'code' => '404',
            'message' => 'Invalid request',
        ]);
    }

    public function jsonSerialize()
    {
        return [
            'status' => $this->status,
            'code' => $this->code,
            'message' => $this->message,
        ];
    }
}
