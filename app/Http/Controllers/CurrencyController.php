<?php

namespace App\Http\Controllers;

use App\Models\Currency;

class CurrencyController extends Controller
{
    public function all()
    {
        return Currency::all(['code', 'name']);
    }
}
