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
        Schema::create('financial_goals', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->integer('account_id');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('details');
            $table->decimal('current_amount', 15, 2);
            $table->decimal('desired_amount', 15, 2);
            $table->boolean('is_complete')->default(false);
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
        Schema::dropIfExists('financial_goals');
    }
};
