<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('three_good_things_entries', function (Blueprint $table): void {
            // The unique constraint on (user_id, entry_date, entry_type) already creates an index
            // in MySQL, so this separate non-unique index on the same columns is redundant.
            try {
                $table->dropIndex('journal_entries_user_id_entry_date_entry_type_index');
            } catch (Exception) {
                // Index may not exist if the database was created without the redundant index.
            }
        });
    }

    public function down(): void
    {
        Schema::table('three_good_things_entries', function (Blueprint $table): void {
            $table->index(['user_id', 'entry_date', 'entry_type'], 'journal_entries_user_id_entry_date_entry_type_index');
        });
    }
};
