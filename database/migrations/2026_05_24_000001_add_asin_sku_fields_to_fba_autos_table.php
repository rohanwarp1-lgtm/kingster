<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fba_autos', function (Blueprint $table) {
            $table->string('asin', 50)->nullable()->after('product_name');
            $table->string('sku', 100)->nullable()->after('asin');
            $table->string('sku_label', 100)->nullable()->after('sku');
        });
    }

    public function down(): void
    {
        Schema::table('fba_autos', function (Blueprint $table) {
            $table->dropColumn(['asin', 'sku', 'sku_label']);
        });
    }
};
