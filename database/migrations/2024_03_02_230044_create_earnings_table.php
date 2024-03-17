<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('description');
            $table->date('payment_date')->default(date('d/m/Y'));
            $table->decimal('value', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};
