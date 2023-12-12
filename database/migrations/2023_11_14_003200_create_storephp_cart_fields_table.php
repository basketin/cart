<?php

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
        Schema::create('storephp_cart_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('storephp_carts')->cascadeOnDelete();
            $table->string('field_key')->index();
            $table->json('field_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storephp_cart_fields');
    }
};