<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('customers')->cascadeOnDelete();
            
            $table->string('tracking_number')->unique();
            $table->string('withdrawal_code')->unique();
            $table->string('package_name');
            $table->text('description')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('declared_value', 12, 2)->nullable();
            $table->string('origin_country');
            $table->string('destination_country');
            $table->string('status')->default('received');
            
            $table->timestamp('received_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('tracking_number');
            $table->index('withdrawal_code');
            $table->index('status');
            $table->index('agency_id');
            $table->index('sender_id');
            $table->index('receiver_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
