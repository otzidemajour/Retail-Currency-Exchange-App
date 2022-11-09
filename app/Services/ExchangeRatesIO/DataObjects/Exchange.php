<?php

namespace App\Services\ExchangeRatesIO\DataObjects;

class Exchange
{
    private object $responseAsObject;

    public function __construct(
        private readonly string $responseBody
    )
    {
        $this->responseAsObject = json_decode($this->responseBody);
    }

    public function status()
    {
        return $this->responseAsObject->success;
    }

    public function rate(): ?float
    {
        if ($this->status()) {
            return (float)$this->responseAsObject->info->rate;
        }
        return null;
    }

    public function result(): ?float
    {
        if ($this->status()) {
            return $this->responseAsObject->result;
        }
        return null;
    }

    public function toArray(): array
    {
        if ($this->status()) {
            return (array) $this->responseAsObject;
        }
        return [];
    }
}
