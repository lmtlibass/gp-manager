<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('departure_date');
            $table->string('origin');
            $table->string('destination');
            $table->string('status')->default('planned');
            $table->string('driver_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['agency_id', 'status']);
            $table->index('departure_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
