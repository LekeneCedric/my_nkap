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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('name');
            $table->string('type');
            $table->string('icon');
            $table->string('color');
            $table->decimal('balance', 15, 2);
            $table->boolean('is_include_in_total_balance')->default(true);
            $table->boolean('is_deleted')->default(false);
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
