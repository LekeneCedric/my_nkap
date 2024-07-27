<?php

use App\Shared\Domain\Enums\MonthEnum;
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
        Schema::create('monthly_statistics', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('composed_id')->index();
            $table->string('user_id')->index();
            $table->unsignedSmallInteger('year');
            $table->enum('month', MonthEnum::values());
            $table->decimal('total_expense', 15)->default(0);
            $table->decimal('total_income', 15)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_statistics');
    }
};
