<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('basketin_cart_fields');
    }

    public function down()
    {
        Schema::create('basketin_cart_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('basketin_carts')->cascadeOnDelete();
            $table->string('field_key')->index();
            $table->json('field_value');
            $table->timestamps();
        });
    }
};
