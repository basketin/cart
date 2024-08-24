<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('basketin_cart_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->unique()->constrained('basketin_carts')->cascadeOnDelete();
            $table->string('reference')->nullable()->index();
            $table->nullableMorphs('orderable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basketin_cart_orders');
    }
};
