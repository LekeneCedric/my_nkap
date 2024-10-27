<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('status');
            $table->index('email');
            $table->index('is_deleted');
            $table->index('verification_code_exp');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('status');
            $table->dropIndex('email');
            $table->dropIndex('is_deleted');
            $table->dropIndex('verification_code_exp');
        });
    }
};
