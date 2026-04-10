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
        Schema::table('organization_questionnaires', function (Blueprint $table) {
            if ($this->foreignKeyExists('organization_questionnaires', 'organization_questionnaires_org_id_foreign')) {
                $table->dropForeign('organization_questionnaires_org_id_foreign');
            }
        });

        Schema::table('organization_questionnaires', function (Blueprint $table) {
            $table->unsignedBigInteger('org_id')->nullable()->change();
        });

        Schema::table('organization_questionnaires', function (Blueprint $table) {
            $table->foreign('org_id')
                ->references('org_id')
                ->on('organizations')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_questionnaires', function (Blueprint $table) {
            if ($this->foreignKeyExists('organization_questionnaires', 'organization_questionnaires_org_id_foreign')) {
                $table->dropForeign('organization_questionnaires_org_id_foreign');
            }
        });

        DB::table('organization_questionnaires')
            ->whereNull('org_id')
            ->delete();

        Schema::table('organization_questionnaires', function (Blueprint $table) {
            $table->unsignedBigInteger('org_id')->nullable(false)->change();
        });

        Schema::table('organization_questionnaires', function (Blueprint $table) {
            $table->foreign('org_id')
                ->references('org_id')
                ->on('organizations')
                ->cascadeOnDelete();
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
