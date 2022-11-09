<?php

namespace App\Services\Concerns;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

trait BuildBaseRequest
{
    public function buildRequestWithApiKey(): PendingRequest
    {
        return $this->withBaseUrl()->timeout(
            seconds: 15
        )->withHeaders(
            headers: ["apiKey" => $this->apiKey],
        );
    }

    public function withBaseUrl(): PendingRequest
    {
        return Http::baseUrl(
            url: $this->baseUrl,
        );
    }
}
