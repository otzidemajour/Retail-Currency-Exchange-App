<?php

namespace App\Services\ExchangeRatesIO;

use App\Services\Concerns\BuildBaseRequest;
use App\Services\Concerns\CanSendGetRequest;
use App\Services\ExchangeRatesIO\Resources\ConvertResource;
use App\Services\ExchangeRatesIO\Resources\CurrencyResource;

class ExchangeRatesIOService
{
    use BuildBaseRequest;
    use CanSendGetRequest;

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $apiKey,
    )
    {}

    public function currencies(): CurrencyResource
    {
        return new CurrencyResource(
            service: $this
        );
    }

    public function exchange(): ConvertResource
    {
        return new ConvertResource(
            service: $this
        );
    }

}
