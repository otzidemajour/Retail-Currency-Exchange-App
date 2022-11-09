<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Services\ExchangeRatesIO\DataObjects\Exchange;
use App\Services\ExchangeRatesIO\DataObjects\Symbols;
use App\Services\ExchangeRatesIO\ExchangeRatesIOService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestController extends Controller
{
    public function __construct(
        private readonly ExchangeRatesIOService $api
    )
    {}

    public function index(Request $request)
    {
        return Currency::all(['code','name']);
        $validator = Validator::make($request->all(),[
            'base' => 'required',
            'target' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        }
        dd("asd");
        dd((new Exchange($this->api->exchange()->get()->body()))->result());
        dd("new");

        $data = (new Symbols($this->api->currencies()->all()->body()));
        dd($data->status(), $data->toArray());
        dd((new Symbols($this->api->currencies()->all()->body()))->status());
    }
}
