<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('customers', 'agency_id')) {
            return;
        }

        DB::table('packages')
            ->select('sender_id as customer_id', 'agency_id')
            ->whereNotNull('sender_id')
            ->orderBy('id')
            ->chunk(500, function ($packages): void {
                foreach ($packages as $package) {
                    DB::table('customers')
                        ->where('id', $package->customer_id)
                        ->whereNull('agency_id')
                        ->update(['agency_id' => $package->agency_id]);
                }
            });

        DB::table('packages')
            ->select('receiver_id as customer_id', 'agency_id')
            ->whereNotNull('receiver_id')
            ->orderBy('id')
            ->chunk(500, function ($packages): void {
                foreach ($packages as $package) {
                    DB::table('customers')
                        ->where('id', $package->customer_id)
                        ->whereNull('agency_id')
                        ->update(['agency_id' => $package->agency_id]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
