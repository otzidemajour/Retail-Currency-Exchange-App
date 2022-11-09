<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TransactionTest extends TestCase
{

    public function test_user_cannot_request_store_endpoint_with_empty_body()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/transaction/store');


        $response->assertStatus(400);
    }

    public function test_user_can_save_transaction()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user);

        $fromCurrency = Currency::factory()->create([
            'code' => 'USD',
            'name' => 'United States Dollar',
        ]);
        $toCurrency = Currency::factory()->create([
            'code' => 'EUR',
            'name' => 'Euro',
        ]);
        $mockAmount = 5;
        $mockRate = 1.5;
        $mockResult = $mockAmount * $mockRate;

        $cacheKey =  sprintf('%s:%s', $fromCurrency->code, $toCurrency->code);
        //put mock cache to redis
        Cache::put($cacheKey,$mockRate,60);

        $response = $this->post('/transaction/store',[
            'base_currency' => $fromCurrency->code,
            'target_currency' => $toCurrency->code,
            'amount' => $mockAmount,
            'type' => (rand(0,1)) ? Transaction::TYPE_DEPOSIT : Transaction::TYPE_WITHDRAW,
            'method' => 'bbva',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas(Transaction::class,['base_amount' => $mockAmount, 'target_amount' => $mockResult]);
    }
}
