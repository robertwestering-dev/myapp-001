<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $userId = DB::table('users')->orderBy('id')->value('id');

        if ($userId !== null) {
            DB::table('users')
                ->where('id', $userId)
                ->update(['role' => 'Admin']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $userId = DB::table('users')->orderBy('id')->value('id');

        if ($userId !== null) {
            DB::table('users')
                ->where('id', $userId)
                ->update(['role' => 'User']);
        }
    }
};
