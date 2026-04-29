<?php

use App\Models\AcademyCourse;
use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

// --- Questionnaire library ---

test('user role sees pro-only questionnaire but card is locked (greyed out, no start button)', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create(['org_id' => $organization->org_id, 'role' => User::ROLE_USER]);

    $questionnaire = Questionnaire::factory()->create(['pro_only' => true]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('questionnaires.index'))
        ->assertOk()
        ->assertSee($questionnaire->title)
        ->assertSee('questionnaire-card--pro-only', false)
        ->assertSee('PRO')
        ->assertDontSee(route('questionnaire-responses.show', $availability, absolute: false), false);
});

test('user_pro role can start a pro-only questionnaire', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create(['org_id' => $organization->org_id, 'role' => User::ROLE_USER_PRO]);

    $questionnaire = Questionnaire::factory()->create(['pro_only' => true]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('questionnaires.index'))
        ->assertOk()
        ->assertSee($questionnaire->title)
        ->assertSee(route('questionnaire-responses.show', $availability, absolute: false), false);
});

test('user role can start a non-pro questionnaire normally', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create(['org_id' => $organization->org_id, 'role' => User::ROLE_USER]);

    $questionnaire = Questionnaire::factory()->create(['pro_only' => false]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('questionnaires.index'))
        ->assertOk()
        ->assertSee($questionnaire->title)
        ->assertSee(route('questionnaire-responses.show', $availability, absolute: false), false);
});

// --- Academy ---

test('user role sees pro-only academy course but card is locked (greyed out, no launch button)', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER]);
    $course = AcademyCourse::factory()->create([
        'pro_only' => true,
        'path' => 'academy-courses/adaptability-foundations',
        'title' => ['nl' => 'PRO Cursus', 'en' => 'PRO Course', 'de' => 'PRO Kurs', 'fr' => 'PRO Cours'],
    ]);
    File::ensureDirectoryExists(dirname($course->contentPath()));
    File::put($course->contentPath(), '<html><body>Pro course</body></html>');

    $this->actingAs($user)
        ->get(route('academy.index'))
        ->assertOk()
        ->assertSee('PRO Cursus')
        ->assertSee('academy-card--pro-only', false)
        ->assertSee('PRO')
        ->assertDontSee($course->launchUrl(), false);
});

test('user_pro role sees pro-only academy course without lock', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER_PRO]);
    $course = AcademyCourse::factory()->create([
        'pro_only' => true,
        'path' => 'academy-courses/adaptability-foundations',
        'title' => ['nl' => 'PRO Cursus', 'en' => 'PRO Course', 'de' => 'PRO Kurs', 'fr' => 'PRO Cours'],
    ]);
    File::ensureDirectoryExists(dirname($course->contentPath()));
    File::put($course->contentPath(), '<html><body>Pro course</body></html>');

    $this->actingAs($user)
        ->get(route('academy.index'))
        ->assertOk()
        ->assertSee('PRO Cursus')
        ->assertSee($course->launchUrl(), false);
});

test('admin cannot open academy course content', function () {
    $admin = User::factory()->admin()->create();
    $course = AcademyCourse::factory()->create([
        'path' => 'academy-courses/admin-blocked-course',
    ]);
    File::ensureDirectoryExists(dirname($course->contentPath()));
    File::put($course->contentPath(), '<html><body>Blocked</body></html>');

    $this->actingAs($admin)
        ->get(route('academy-courses.show', ['academyCoursePath' => $course->contentRouteSegment()]))
        ->assertForbidden();
});

test('user role cannot open pro-only academy course content', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER]);
    $course = AcademyCourse::factory()->create([
        'pro_only' => true,
        'path' => 'academy-courses/pro-only-course',
    ]);
    File::ensureDirectoryExists(dirname($course->contentPath()));
    File::put($course->contentPath(), '<html><body>Blocked pro</body></html>');

    $this->actingAs($user)
        ->get(route('academy-courses.show', ['academyCoursePath' => $course->contentRouteSegment()]))
        ->assertForbidden();
});

test('user role can open non-pro academy course content', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER]);
    $course = AcademyCourse::factory()->create([
        'pro_only' => false,
        'path' => 'academy-courses/free-course',
    ]);
    File::ensureDirectoryExists(dirname($course->contentPath()));
    File::put($course->contentPath(), '<html><body>Open course</body></html>');

    $response = $this->actingAs($user)
        ->get(route('academy-courses.show', ['academyCoursePath' => $course->contentRouteSegment()]))
        ->assertOk();

    expect($response->baseResponse->getFile()->getPathname())->toBe($course->contentPath());
});

test('user_pro role can open pro-only academy course content', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER_PRO]);
    $course = AcademyCourse::factory()->create([
        'pro_only' => true,
        'path' => 'academy-courses/pro-course',
    ]);
    File::ensureDirectoryExists(dirname($course->contentPath()));
    File::put($course->contentPath(), '<html><body>Allowed pro</body></html>');

    $response = $this->actingAs($user)
        ->get(route('academy-courses.show', ['academyCoursePath' => $course->contentRouteSegment()]))
        ->assertOk();

    expect($response->baseResponse->getFile()->getPathname())->toBe($course->contentPath());
});

test('academy course content route serves nested asset files from the course export', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER]);
    $course = AcademyCourse::factory()->create([
        'path' => 'academy-courses/positief-fundament',
    ]);
    $assetPath = $course->contentPath('story_content/data.js');

    File::ensureDirectoryExists(dirname($assetPath));
    File::put($assetPath, 'window.storyData = true;');

    $response = $this->actingAs($user)
        ->get(route('academy-courses.show', [
            'academyCoursePath' => $course->contentRouteSegment(),
            'asset' => 'story_content/data.js',
        ]))
        ->assertOk();

    expect($response->baseResponse->getFile()->getPathname())->toBe($assetPath);
});

// --- Admin forms ---

test('admin can set pro_only when creating a questionnaire', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.questionnaires.store'), [
            'title' => 'PRO Scan',
            'description' => '',
            'locale' => 'nl',
            'is_active' => '1',
            'pro_only' => '1',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('questionnaires', ['title' => 'PRO Scan', 'pro_only' => true]);
});

test('admin can set pro_only when creating an academy course', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.academy-courses.store'), [
            'slug' => 'pro-cursus',
            'theme' => AcademyCourse::THEME_ADAPTABILITY,
            'path' => 'academy-courses/pro-cursus',
            'estimated_minutes' => 30,
            'sort_order' => 0,
            'is_active' => '1',
            'pro_only' => '1',
            'title' => ['nl' => 'PRO', 'en' => 'PRO', 'de' => 'PRO', 'fr' => 'PRO'],
            'audience' => ['nl' => 'Allen', 'en' => 'All', 'de' => 'Alle', 'fr' => 'Tous'],
            'goal' => ['nl' => 'Doel', 'en' => 'Goal', 'de' => 'Ziel', 'fr' => 'But'],
            'summary' => ['nl' => 'Samenvatting', 'en' => 'Summary', 'de' => 'Zusammenfassung', 'fr' => 'Résumé'],
            'learning_goals' => ['nl' => 'Leerdoel 1', 'en' => 'Goal 1', 'de' => 'Ziel 1', 'fr' => 'Objectif 1'],
            'contents' => ['nl' => 'Inhoud 1', 'en' => 'Content 1', 'de' => 'Inhalt 1', 'fr' => 'Contenu 1'],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('academy_courses', ['slug' => 'pro-cursus', 'pro_only' => true]);
});
