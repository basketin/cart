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
        Schema::create('storephp_carts', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique()->index();
            $table->nullablemorphs('customer');
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['open', 'checkout', 'abandoned', 're-open'])->default('open');
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
        Schema::dropIfExists('storephp_carts');
    }
};
