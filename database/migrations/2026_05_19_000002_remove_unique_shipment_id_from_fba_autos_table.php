<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fba_autos', function (Blueprint $table) {
            $table->dropUnique(['shipment_id']);
            $table->index('shipment_id');
        });
    }

    public function down(): void
    {
        Schema::table('fba_autos', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
            $table->unique('shipment_id');
        });
    }
};
