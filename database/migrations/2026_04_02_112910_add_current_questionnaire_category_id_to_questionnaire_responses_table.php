<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('questionnaire_responses', 'current_questionnaire_category_id')) {
            Schema::table('questionnaire_responses', function (Blueprint $table) {
                $table->unsignedBigInteger('current_questionnaire_category_id')
                    ->nullable()
                    ->after('resume_token');
            });
        }

        if (! $this->foreignKeyExists('questionnaire_responses', 'q_resp_current_cat_fk')) {
            Schema::table('questionnaire_responses', function (Blueprint $table) {
                $table->foreign('current_questionnaire_category_id', 'q_resp_current_cat_fk')
                    ->references('id')
                    ->on('questionnaire_categories')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questionnaire_responses', function (Blueprint $table) {
            if ($this->foreignKeyExists('questionnaire_responses', 'q_resp_current_cat_fk')) {
                $table->dropForeign('q_resp_current_cat_fk');
            }

            if (Schema::hasColumn('questionnaire_responses', 'current_questionnaire_category_id')) {
                $table->dropColumn('current_questionnaire_category_id');
            }
        });
    }

    protected function foreignKeyExists(string $table, string $constraint): bool
    {
        if (DB::getDriverName() !== 'mysql') {
            return false;
        }

        return DB::table('information_schema.table_constraints')
            ->where('constraint_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('constraint_name', $constraint)
            ->where('constraint_type', 'FOREIGN KEY')
            ->exists();
    }
};
