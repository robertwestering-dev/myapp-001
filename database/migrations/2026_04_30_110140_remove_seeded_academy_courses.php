<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('academy_courses')
            ->whereIn('slug', ['adaptability-foundations', 'digital-resilience-basics'])
            ->delete();
    }

    public function down(): void
    {
        // The removed seeded Academy courses should not be recreated automatically.
    }
};
