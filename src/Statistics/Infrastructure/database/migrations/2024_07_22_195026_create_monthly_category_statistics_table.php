<?php

use App\Shared\Domain\Enums\MonthEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monthly_category_statistics', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('composed_id')->index();
            $table->string('user_id')->index();
            $table->unsignedSmallInteger('year')->index();
            $table->enum('month', MonthEnum::values())->index();
            $table->string('category_id')->index();
            $table->string('category_icon');
            $table->string('category_label');
            $table->string('category_color');
            $table->float('total_income')->default(0);
            $table->float('total_expense')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_category_statistics');
    }
};
