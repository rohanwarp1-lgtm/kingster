<?php

namespace App\Traits;

use Illuminate\Support\Facades\Artisan;

trait MigrationTrait
{
    /**
     * Run all pending migrations.
     *
     * @param  string|null  $path
     * @param  bool  $fresh
     * @return string
     */
    public function migrateAllTables(?string $path = null, bool $fresh = false): string
    {
        $command = $fresh ? 'migrate:fresh' : 'migrate';
        $params = ['--force' => true];

        if ($path) {
            $params['--path'] = $path;
        }

        Artisan::call($command, $params);

        return Artisan::output();
    }
}
