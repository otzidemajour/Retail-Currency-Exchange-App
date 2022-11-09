<?php

namespace App\Services\ExchangeRatesIO\Resources;

use App\Services\ExchangeRatesIO\ExchangeRatesIOService;
use Illuminate\Http\Client\Response;

class CurrencyResource
{
    public function __construct(
        private readonly ExchangeRatesIOService $service
    )
    {
    }

    public function all(): Response
    {
        return $this->service->get(
            request: $this->service->buildRequestWithApiKey(),
            url: "/symbols",
        );
    }
}
