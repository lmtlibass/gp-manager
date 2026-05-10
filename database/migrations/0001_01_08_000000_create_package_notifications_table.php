<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            
            $table->string('channel');
            $table->string('recipient_phone');
            $table->text('message');
            $table->string('status')->default('pending');
            
            $table->timestamp('sent_at')->nullable();
            $table->text('failed_reason')->nullable();
            
            $table->timestamps();

            $table->index('package_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_notifications');
    }
};
