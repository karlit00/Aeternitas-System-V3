<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        // Step 1: Remove auto-increment from id
        DB::statement('ALTER TABLE positions MODIFY id BIGINT NOT NULL');

        // Step 2: Drop primary key
        DB::statement('ALTER TABLE positions DROP PRIMARY KEY');

        // Step 3: Add uuid column
        Schema::table('positions', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Step 4: Populate uuid column for all existing rows
        $positions = DB::table('positions')->get();
        foreach ($positions as $position) {
            DB::table('positions')
                ->where('id', $position->id)
                ->update(['uuid' => (string) Str::uuid()]);
        }

        // Step 5: Make uuid the new primary key
        DB::statement('ALTER TABLE positions ADD PRIMARY KEY (uuid)');

        // Step 6: Drop old id column
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        // Step 7: Rename uuid to id
        Schema::table('positions', function (Blueprint $table) {
            $table->renameColumn('uuid', 'id');
        });
    }

    public function down()
    {
        // This migration is intentionally irreversible.
        // The original integer IDs were replaced by UUIDs and then dropped,
        // so there is no safe way to restore the previous state.
        throw new \RuntimeException('Migration 2026_02_25_000003_convert_positions_id_to_uuid cannot be rolled back safely.');
    }
};
