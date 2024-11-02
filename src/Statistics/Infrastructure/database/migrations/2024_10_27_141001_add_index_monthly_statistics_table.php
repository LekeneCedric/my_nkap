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
        Schema::table('monthly_statistics', function (Blueprint $table) {
            $table->index('year');
            $table->index('month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_statistics', function (Blueprint $table) {
            $table->dropIndex('year');
            $table->dropIndex('month');
        });
    }
};
