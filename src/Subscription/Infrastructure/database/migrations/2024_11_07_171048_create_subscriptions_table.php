<?php

use App\Subscription\Domain\Enums\SubscriptionPlansEnum;
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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->index();
            $table->enum('name', SubscriptionPlansEnum::values())->index();
            $table->decimal('price');
            $table->unsignedMediumInteger('nb_token_per_day');
            $table->unsignedSmallInteger('nb_operations_per_day');
            $table->tinyInteger('nb_accounts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
