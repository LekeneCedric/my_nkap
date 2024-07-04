<?php

use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
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
        Schema::create('log_messages', function (Blueprint $table) {
            $table->id();
            $table->string('message', 250);
            $table->string('description');
            $table->enum('level', LogLevelEnum::values());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_messages');
    }
};
