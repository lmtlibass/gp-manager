<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('agency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('validated_by')->nullable()->constrained('users')->cascadeOnDelete();
            
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('withdrawal_code');
            $table->string('identity_document')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamp('withdrawn_at');
            $table->timestamps();

            $table->index('agency_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
