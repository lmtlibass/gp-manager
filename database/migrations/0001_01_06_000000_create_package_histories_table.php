<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('comment')->nullable();
            
            $table->timestamp('created_at')->useCurrent();

            $table->index('package_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_histories');
    }
};
