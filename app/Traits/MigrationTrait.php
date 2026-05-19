<?php

namespace App\Traits;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait MigrationTrait
{
    /**
     * Run all pending migrations and fix missing columns, then return an HTML report.
     */
    public function migrateAllTables(?string $path = null, bool $fresh = false): string
    {
        $lines = [];

        try {
            if ($fresh) {
                Artisan::call('migrate:fresh', ['--force' => true]);
                $lines[] = '<b>migrate:fresh</b> completed.';
                $lines[] = nl2br(htmlspecialchars(Artisan::output()));
            } else {
                // Run pending migrations
                Artisan::call('migrate', ['--force' => true]);
                $output = trim(Artisan::output());
                $lines[] = '<b>migrate</b>: ' . ($output ? nl2br(htmlspecialchars($output)) : 'Nothing to migrate.');

                // Invalidate the auto-migrate session cache so the middleware re-checks
                session()->forget('schema_verified');

                // Report migration status
                $pending = $this->getPendingMigrationNames();
                if (empty($pending)) {
                    $lines[] = '✅ All migrations are up to date.';
                } else {
                    $lines[] = '⚠️ Still pending: ' . implode(', ', $pending);
                }
            }
        } catch (\Throwable $e) {
            $lines[] = '❌ Migration error: ' . htmlspecialchars($e->getMessage());
        }

        $html  = '<pre style="font-family:monospace;font-size:13px;line-height:1.6;">';
        $html .= implode("\n", $lines);
        $html .= '</pre>';

        return $html;
    }

    private function getPendingMigrationNames(): array
    {
        $migrationPath = database_path('migrations');
        $files = glob($migrationPath . '/*.php') ?: [];
        $ran = DB::table('migrations')->pluck('migration')->toArray();
        $pending = [];

        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            if (!in_array($name, $ran)) {
                $pending[] = $name;
            }
        }

        return $pending;
    }
}
