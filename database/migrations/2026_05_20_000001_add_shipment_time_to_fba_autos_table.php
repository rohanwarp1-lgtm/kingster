<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('fba_autos', 'shipment_time')) {
            Schema::table('fba_autos', function (Blueprint $table) {
                $table->time('shipment_time')->nullable()->after('shipment_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('fba_autos', 'shipment_time')) {
            Schema::table('fba_autos', function (Blueprint $table) {
                $table->dropColumn('shipment_time');
            });
        }
    }
};
