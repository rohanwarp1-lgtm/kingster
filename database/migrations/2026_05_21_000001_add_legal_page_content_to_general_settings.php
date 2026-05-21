<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('general_settings', 'privacy_policy_content')) {
                $table->longText('privacy_policy_content')->nullable();
            }

            if (! Schema::hasColumn('general_settings', 'terms_condition_content')) {
                $table->longText('terms_condition_content')->nullable();
            }

            if (! Schema::hasColumn('general_settings', 'shipping_returns_content')) {
                $table->longText('shipping_returns_content')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'shipping_returns_content')) {
                $table->dropColumn('shipping_returns_content');
            }

            if (Schema::hasColumn('general_settings', 'terms_condition_content')) {
                $table->dropColumn('terms_condition_content');
            }

            if (Schema::hasColumn('general_settings', 'privacy_policy_content')) {
                $table->dropColumn('privacy_policy_content');
            }
        });
    }
};
