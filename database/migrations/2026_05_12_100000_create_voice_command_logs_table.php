<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voice_command_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agency_id')->constrained()->cascadeOnDelete();

            $table->text('transcription');
            $table->string('intent', 60)->nullable()->index();
            $table->json('extracted_data')->nullable();
            $table->float('confidence')->nullable();

            // Processing metadata
            $table->string('status', 20)->default('pending'); // pending | processed | applied | failed
            $table->text('error_message')->nullable();
            $table->text('raw_response')->nullable();
            $table->string('model_used', 100)->nullable();
            $table->unsignedInteger('tokens_used')->nullable();
            $table->unsignedInteger('response_time_ms')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voice_command_logs');
    }
};
