<?php

namespace App\Http\Middleware;

use App\Traits\MigrationTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class AutoMigrate
{
    use MigrationTrait;

    // Session key — cleared whenever a deploy resets session storage
    private const SESSION_KEY = 'schema_verified';

    public function handle(Request $request, Closure $next)
    {
        // Only run on local/staging — production deployments must run migrations explicitly
        if (app()->environment('production')) {
            return $next($request);
        }

        // Only check once per session to avoid overhead on every request
        if (!session(self::SESSION_KEY)) {
            $this->ensureSchemaUpToDate();
            session([self::SESSION_KEY => true]);
        }

        return $next($request);
    }

    private function ensureSchemaUpToDate(): void
    {
        try {
            // 1. Run any pending migration files (new tables, new column migrations, etc.)
            $pending = $this->hasPendingMigrations();
            if ($pending) {
                Artisan::call('migrate', ['--force' => true]);
            }

            // 2. Check for any missing columns that exist in DB schema definitions
            //    but are absent from the actual tables (handles altered migration files).
            $this->addMissingColumns();

        } catch (\Throwable $e) {
            // Never crash the admin panel due to a migration error
            \Illuminate\Support\Facades\Log::error('AutoMigrate error: ' . $e->getMessage());
        }
    }

    private function hasPendingMigrations(): bool
    {
        try {
            $migrationFiles = $this->getMigrationFiles();
            $ranMigrations = DB::table('migrations')->pluck('migration')->toArray();

            foreach ($migrationFiles as $file) {
                $name = pathinfo($file, PATHINFO_FILENAME);
                if (!in_array($name, $ranMigrations)) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            return true; // If we can't check, try to migrate
        }

        return false;
    }

    private function getMigrationFiles(): array
    {
        $path = database_path('migrations');
        if (!is_dir($path)) {
            return [];
        }
        return glob($path . '/*.php') ?: [];
    }

    /**
     * Compare expected columns (hardcoded per table) against actual DB columns.
     * Add any columns that are missing without affecting existing data.
     */
    private function addMissingColumns(): void
    {
        $this->repairKnownSchema();
    }
}
