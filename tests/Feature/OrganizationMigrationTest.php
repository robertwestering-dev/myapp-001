<?php

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

test('organizations table contains the expected columns', function () {
    expect(Schema::hasTable('organizations'))->toBeTrue();
    expect(Schema::hasColumns('organizations', [
        'org_id',
        'naam',
        'adres',
        'postcode',
        'plaats',
        'land',
        'telefoon',
        'contact_id',
        'created_at',
        'updated_at',
    ]))->toBeTrue();
});

test('organizations contact_id must reference an existing user', function () {
    $contact = User::factory()->create();

    $this->assertDatabaseHas('organizations', [
        'naam' => 'Hermes Results',
    ]);

    $this->assertDatabaseMissing('organizations', [
        'naam' => 'Acme BV',
    ]);

    DB::table('organizations')->insert([
        'naam' => 'Acme BV',
        'adres' => 'Hoofdstraat 1',
        'postcode' => '1234 AB',
        'plaats' => 'Amsterdam',
        'land' => 'Nederland',
        'telefoon' => '+31 20 123 4567',
        'contact_id' => $contact->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->assertDatabaseHas('organizations', [
        'naam' => 'Acme BV',
        'contact_id' => $contact->id,
    ]);

    expect(function (): void {
        DB::table('organizations')->insert([
            'naam' => 'Broken BV',
            'adres' => 'Teststraat 99',
            'postcode' => '9999 ZZ',
            'plaats' => 'Utrecht',
            'land' => 'Nederland',
            'telefoon' => '+31 30 999 9999',
            'contact_id' => 999999,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    })->toThrow(QueryException::class);
});

test('new users are linked to Hermes Results by default', function () {
    $organization = DB::table('organizations')
        ->where('naam', 'Hermes Results')
        ->first();

    expect($organization)->not->toBeNull();

    $user = User::factory()->create()->fresh();

    expect($user?->org_id)->toBe($organization->org_id);
});
