<?php

use App\Models\PaymentMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('name');
            $table->timestamps();
        });

        $baseMethods = [
            [
                'slug' => 'msi',
                'name' => 'MSI-PAY',
            ],
            [
                'slug' => 'bbva',
                'name' => 'BBVA',
            ],
            [
                'slug' => 'visa',
                'name' => 'Visa',
            ]
        ];

        foreach ($baseMethods as $baseMethod) {
            $paymentMethod = new PaymentMethod();
            $paymentMethod->slug = $baseMethod['slug'];
            $paymentMethod->name = $baseMethod['name'];
            $paymentMethod->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
};
