<?php

namespace App\Traits;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

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
                foreach ($this->repairKnownSchema() as $repairLine) {
                    $lines[] = htmlspecialchars($repairLine);
                }
            } else {
                // Run pending migrations
                Artisan::call('migrate', ['--force' => true]);
                $output = trim(Artisan::output());
                $lines[] = '<b>migrate</b>: ' . ($output ? nl2br(htmlspecialchars($output)) : 'Nothing to migrate.');

                foreach ($this->repairKnownSchema() as $repairLine) {
                    $lines[] = htmlspecialchars($repairLine);
                }

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

    /**
     * Keep altered migration definitions and important seed/reference rows safe
     * even on databases where the original migration was already marked as run.
     */
    protected function repairKnownSchema(): array
    {
        $lines = [];

        $schema = [
            'users' => [
                'session_id' => ['type' => 'string', 'nullable' => true, 'default' => null],
                'role'       => ['type' => 'string', 'nullable' => false, 'default' => 'Sub Admin'],
                'is_deleted' => ['type' => 'integer', 'nullable' => false, 'default' => 0],
            ],
            'warranty_records' => [
                'is_deleted'  => ['type' => 'integer', 'nullable' => false, 'default' => 0],
                'created_by'  => ['type' => 'integer', 'nullable' => true, 'default' => null],
                'modified_by' => ['type' => 'integer', 'nullable' => true, 'default' => null],
            ],
            'products' => [
                'status'     => ['type' => 'integer', 'nullable' => false, 'default' => 1],
                'is_deleted' => ['type' => 'integer', 'nullable' => false, 'default' => 0],
                'is_variant' => ['type' => 'integer', 'nullable' => false, 'default' => 0],
                'index'      => ['type' => 'integer', 'nullable' => false, 'default' => 1000],
            ],
            'product_names' => [
                'is_deleted'  => ['type' => 'integer', 'nullable' => false, 'default' => 0],
                'created_by'  => ['type' => 'integer', 'nullable' => true, 'default' => null],
                'modified_by' => ['type' => 'integer', 'nullable' => true, 'default' => null],
            ],
            'general_settings' => [
                'modified_by'                => ['type' => 'integer', 'nullable' => true, 'default' => null],
                'replacement_policy_content' => ['type' => 'longText', 'nullable' => true, 'default' => null],
            ],
            'fba_autos' => [
                'shipment_time' => ['type' => 'time', 'nullable' => true, 'default' => null, 'after' => 'shipment_date'],
            ],
        ];

        foreach ($schema as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column => $definition) {
                if (Schema::hasColumn($table, $column)) {
                    continue;
                }

                Schema::table($table, function (Blueprint $table) use ($column, $definition) {
                    $col = match ($definition['type']) {
                        'integer' => $table->integer($column),
                        'string'  => $table->string($column),
                        'text'    => $table->text($column),
                        'longText'=> $table->longText($column),
                        'boolean' => $table->boolean($column),
                        'time'    => $table->time($column),
                        default   => $table->string($column),
                    };

                    if (! empty($definition['after']) && method_exists($col, 'after')) {
                        $col->after($definition['after']);
                    }

                    if ($definition['nullable']) {
                        $col->nullable();
                    }

                    if ($definition['default'] !== null) {
                        $col->default($definition['default']);
                    } elseif ($definition['nullable']) {
                        $col->nullable()->default(null);
                    }
                });

                $lines[] = "Added missing column {$table}.{$column}.";
            }
        }

        $lines = array_merge($lines, $this->repairFbaReferenceData());

        return $lines;
    }

    private function repairFbaReferenceData(): array
    {
        $lines = [];

        if (Schema::hasTable('fba_states')) {
            $states = [
                ['name' => 'Gujarat', 'code' => 'GJ', 'sort_order' => 1],
                ['name' => 'Maharashtra', 'code' => 'MH', 'sort_order' => 2],
                ['name' => 'Karnataka', 'code' => 'KA', 'sort_order' => 3],
                ['name' => 'Delhi', 'code' => 'DL', 'sort_order' => 4],
                ['name' => 'Telangana', 'code' => 'TS', 'sort_order' => 5],
            ];

            foreach ($states as $state) {
                $exists = DB::table('fba_states')->where('name', $state['name'])->exists();

                if (! $exists) {
                    DB::table('fba_states')->insert([
                        'name' => $state['name'],
                        'code' => $state['code'],
                        'sort_order' => $state['sort_order'],
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $lines[] = "Seeded FBA state {$state['name']}.";
                }
            }
        }

        if (Schema::hasTable('fba_warehouses')) {
            $warehouses = [
                ['name' => 'Mumbai Warehouse', 'sort_order' => 1],
                ['name' => 'Delhi Warehouse', 'sort_order' => 2],
                ['name' => 'Chennai Hub', 'sort_order' => 3],
            ];

            foreach ($warehouses as $warehouse) {
                $exists = DB::table('fba_warehouses')->where('name', $warehouse['name'])->exists();

                if (! $exists) {
                    DB::table('fba_warehouses')->insert([
                        'name' => $warehouse['name'],
                        'sort_order' => $warehouse['sort_order'],
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $lines[] = "Seeded FBA warehouse {$warehouse['name']}.";
                }
            }
        }

        return $lines;
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
