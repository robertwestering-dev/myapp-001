<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->boolean('pro_only')->default(false)->after('is_active');
        });

        Schema::table('academy_courses', function (Blueprint $table) {
            $table->boolean('pro_only')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->dropColumn('pro_only');
        });

        Schema::table('academy_courses', function (Blueprint $table) {
            $table->dropColumn('pro_only');
        });
    }
};
