<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('three_good_things_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('three_good_things_entries', 'entry_type')) {
                $table->string('entry_type', 50)->default('three_good_things')->after('entry_date');
            }

            if (! Schema::hasColumn('three_good_things_entries', 'content')) {
                $table->json('content')->nullable()->after('entry_type');
            }
        });

        DB::table('three_good_things_entries')
            ->whereNull('content')
            ->orderBy('id')
            ->chunkById(200, function ($entries): void {
                foreach ($entries as $entry) {
                    DB::table('three_good_things_entries')
                        ->where('id', $entry->id)
                        ->update([
                            'entry_type' => 'three_good_things',
                            'content' => json_encode([
                                'what_went_well' => $entry->what_went_well,
                                'my_contribution' => $entry->my_contribution,
                            ], JSON_THROW_ON_ERROR),
                        ]);
                }
            });

        Schema::table('three_good_things_entries', function (Blueprint $table) {
            $table->dropUnique('three_good_things_entries_user_id_entry_date_unique');
            $table->unique(['user_id', 'entry_date', 'entry_type'], 'journal_entries_user_id_entry_date_entry_type_unique');
            $table->index(['user_id', 'entry_date', 'entry_type'], 'journal_entries_user_id_entry_date_entry_type_index');
        });
    }

    public function down(): void
    {
        Schema::table('three_good_things_entries', function (Blueprint $table) {
            $table->dropUnique('journal_entries_user_id_entry_date_entry_type_unique');
            $table->dropIndex('journal_entries_user_id_entry_date_entry_type_index');
            $table->unique(['user_id', 'entry_date']);
            $table->dropColumn(['entry_type', 'content']);
        });
    }
};
