<?php

namespace App\Services\ExchangeRatesIO\DataObjects;

class Symbols
{
    public function __construct(
        private readonly string $responseBody
    )
    {}

    public function status()
    {
        return json_decode($this->responseBody)->success;
    }

    public function toArray(): array
    {
        if ($this->status()) {
            return json_decode($this->responseBody,true)['symbols'];
        }
        return [];
    }
}
