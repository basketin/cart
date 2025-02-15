<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('basketin_carts', function (Blueprint $table) {
            $table->string('cart_type')->after('ulid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basketin_carts', callback: function (Blueprint $table) {
            $table->dropColumn('cart_type');
        });
    }
};
