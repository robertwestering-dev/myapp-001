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
        $defaultOrganizationId = DB::table('organizations')
            ->where('naam', 'Hermes Results')
            ->value('org_id');

        if ($defaultOrganizationId === null) {
            $defaultOrganizationId = DB::table('organizations')->insertGetId([
                'naam' => 'Hermes Results',
                'adres' => '',
                'postcode' => '',
                'plaats' => '',
                'land' => '',
                'telefoon' => '',
                'contact_id' => DB::table('users')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ], 'org_id');
        }

        Schema::table('users', function (Blueprint $table) use ($defaultOrganizationId) {
            $table->foreignId('org_id')
                ->after('role')
                ->default($defaultOrganizationId)
                ->constrained('organizations', 'org_id');
        });

        DB::table('users')
            ->whereNull('org_id')
            ->update(['org_id' => $defaultOrganizationId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('org_id');
        });
    }
};
