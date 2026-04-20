<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questionnaire_responses', function (Blueprint $table) {
            $table->index(['organization_questionnaire_id', 'user_id'], 'qr_org_questionnaire_user_index');
            $table->dropUnique('qr_org_questionnaire_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questionnaire_responses', function (Blueprint $table) {
            $table->unique(['organization_questionnaire_id', 'user_id'], 'qr_org_questionnaire_user_unique');
            $table->dropIndex('qr_org_questionnaire_user_index');
        });
    }
};
