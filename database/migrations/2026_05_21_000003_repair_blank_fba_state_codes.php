<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('fba_states') || ! Schema::hasColumn('fba_states', 'code')) {
            return;
        }

        $states = DB::table('fba_states')
            ->whereNull('code')
            ->orWhere('code', '')
            ->orderBy('id')
            ->get(['id', 'name']);

        foreach ($states as $state) {
            DB::table('fba_states')
                ->where('id', $state->id)
                ->update(['code' => $this->uniqueCode((string) $state->name, (int) $state->id)]);
        }
    }

    public function down(): void
    {
        //
    }

    private function uniqueCode(string $name, int $ignoreId): string
    {
        $base = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $name));
        $base = $base !== '' ? substr($base, 0, 10) : 'STATE';
        $code = $base;
        $suffix = 1;

        while ($this->codeExists($code, $ignoreId)) {
            $suffixText = (string) $suffix++;
            $code = substr($base, 0, max(1, 10 - strlen($suffixText))) . $suffixText;
        }

        return $code;
    }

    private function codeExists(string $code, int $ignoreId): bool
    {
        return DB::table('fba_states')
            ->where('code', $code)
            ->where('id', '!=', $ignoreId)
            ->exists();
    }
};
