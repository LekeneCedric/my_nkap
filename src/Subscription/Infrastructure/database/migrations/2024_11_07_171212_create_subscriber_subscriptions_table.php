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
        Schema::create('subscriber_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->index();
            $table->integer('user_id');
            $table->integer('subscription_id');
            $table->integer('start_date');
            $table->integer('end_date');
            $table->unsignedMediumInteger('nb_token');
            $table->unsignedSmallInteger('nb_operations');
            $table->tinyInteger('nb_accounts');
            $table->integer('nb_token_updated_at');
            $table->integer('nb_operations_updated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriber_subscriptions');
    }
};
