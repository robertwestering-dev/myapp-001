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
        $organizations = DB::table('organizations')
            ->orderBy('org_id')
            ->pluck('org_id');

        $globalAvailabilities = DB::table('organization_questionnaires')
            ->whereNull('org_id')
            ->get();

        foreach ($globalAvailabilities as $availability) {
            foreach ($organizations as $organizationId) {
                DB::table('organization_questionnaires')->updateOrInsert(
                    [
                        'questionnaire_id' => $availability->questionnaire_id,
                        'org_id' => $organizationId,
                    ],
                    [
                        'available_from' => $availability->available_from,
                        'available_until' => $availability->available_until,
                        'is_active' => $availability->is_active,
                        'updated_at' => $availability->updated_at,
                        'created_at' => $availability->created_at,
                    ],
                );
            }
        }

        DB::table('organization_questionnaires')
            ->whereNull('org_id')
            ->delete();

        Schema::table('organization_questionnaires', function (Blueprint $table) {
            $table->dropForeign(['org_id']);
        });

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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_questionnaires', function (Blueprint $table) {
            $table->dropForeign(['org_id']);
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
};
