<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('description')->nullable();
            $table->string('category', 50);
            $table->enum('payment_method', ['C', 'D', 'P']);
            $table->date('payment_date')->default(date('d/m/Y'));
            $table->boolean('paid');
            $table->decimal('value', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
