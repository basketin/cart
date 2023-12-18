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
        // carts table
        Schema::create('basketin_carts', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique()->index();
            $table->nullablemorphs('customer');
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['open', 'checkout', 'abandoned', 're-open'])->default('open');
            $table->timestamps();
        });

        // quotes table
        Schema::create('basketin_cart_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('basketin_carts')->cascadeOnDelete();
            $table->morphs('item');
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });

        // fields table
        Schema::create('basketin_cart_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('basketin_carts')->cascadeOnDelete();
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
        Schema::dropIfExists('basketin_cart_fields');
        Schema::dropIfExists('basketin_cart_quotes');
        Schema::dropIfExists('basketin_carts');
    }
};
