<?php

use App\Models\AcademyCourse;
use App\Models\User;
use Illuminate\Support\Facades\File;

test('guests are redirected to the login page when visiting the academy', function () {
    $response = $this->get(route('academy.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the academy catalog', function () {
    $user = User::factory()->create([
        'name' => 'Volledige Naam',
        'first_name' => 'Robert',
    ]);
    $course = AcademyCourse::factory()->create([
        'path' => 'academy-courses/adaptability-foundations',
        'title' => [
            'nl' => 'Adaptability Fundamentals',
            'en' => 'Adaptability Fundamentals',
            'de' => 'Adaptability Fundamentals',
            'fr' => 'Adaptability Fundamentals',
        ],
        'learning_goals' => [
            'nl' => ['Herken je adaptieve patronen'],
            'en' => ['Recognize your adaptive patterns'],
            'de' => ['Erkenne deine adaptiven Muster'],
            'fr' => ['Reconnaissez vos schemas adaptatifs'],
        ],
        'contents' => [
            'nl' => ['Deze inhoud mag niet zichtbaar zijn'],
            'en' => ['This content should not be visible'],
            'de' => ['Dieser Inhalt sollte nicht sichtbar sein'],
            'fr' => ['Ce contenu ne doit pas etre visible'],
        ],
    ]);
    File::ensureDirectoryExists(dirname($course->contentPath()));
    File::put($course->contentPath(), '<html><body>Adaptability Fundamentals</body></html>');

    $response = $this->actingAs($user)->get(route('academy.index'));

    $response->assertOk()
        ->assertSee(route('dashboard', absolute: false), false)
        ->assertSee(__('hermes.dashboard.title'))
        ->assertSee(__('hermes.nav.questionnaires'))
        ->assertSee(__('hermes.nav.profile'))
        ->assertSee(__('hermes.dashboard.logout'))
        ->assertDontSee('<a class="user-menu__item" href="'.route('blog.index').'"', false)
        ->assertSee('<form method="POST" action="'.route('logout').'" class="user-menu__form">', false)
        ->assertDontSee('<button type="submit" class="pill pill--neutral">'.__('hermes.dashboard.logout').'</button>', false)
        ->assertSee('user-panel', false)
        ->assertSee('user-section-heading', false)
        ->assertSee('user-action-row', false)
        ->assertSee('.academy-card__details', false)
        ->assertSee('.academy-card__details[open] ~ .academy-card__details-body', false)
        ->assertSee('grid-template-columns: max-content max-content minmax(0, 1fr)', false)
        ->assertSee('grid-row: 1', false)
        ->assertSee('user-meta-grid', false)
        ->assertSee('user-meta-item', false)
        ->assertSee('academy-content', false)
        ->assertSee(__('hermes.academy.eyebrow'))
        ->assertSee(__('hermes.academy.sidebar_title'))
        ->assertSee(__('hermes.academy.sidebar_text'))
        ->assertSee($course->titleForLocale())
        ->assertSee('user-surface-card', false)
        ->assertSee(__('hermes.academy.more_info'))
        ->assertSee(__('hermes.academy.duration'))
        ->assertSee(__('hermes.academy.format'))
        ->assertSee(__('hermes.academy.web_export_format'))
        ->assertSee(__('hermes.academy.learning_goals'))
        ->assertSee('Herken je adaptieve patronen')
        ->assertDontSee(__('hermes.academy.contents'))
        ->assertDontSee('Deze inhoud mag niet zichtbaar zijn')
        ->assertDontSee(__('hermes.academy.audience'))
        ->assertDontSee(__('hermes.academy.goal'))
        ->assertSee(__('hermes.academy.open_course'))
        ->assertDontSee(__('hermes.academy.heading'))
        ->assertDontSee('Robert')
        ->assertDontSee($user->email)
        ->assertSee('#academy-course-'.$course->slug, false)
        ->assertSee(route('academy.courses.launch', $course->slug, absolute: false), false);
});

test('academy opens the export submap for the active locale', function () {
    $user = User::factory()->create([
        'locale' => 'en',
        'role' => User::ROLE_USER,
    ]);
    $course = AcademyCourse::factory()->create([
        'path' => 'academy-courses/multilingual-course',
        'localized_paths' => [
            'en' => 'EN',
            'de' => 'DE',
        ],
        'title' => [
            'nl' => 'Meertalige cursus',
            'en' => 'Multilingual course',
            'de' => 'Mehrsprachiger Kurs',
        ],
    ]);

    $englishIndex = $course->contentPath(locale: 'en');

    File::ensureDirectoryExists(dirname($englishIndex));
    File::put($englishIndex, '<html><body>English course</body></html>');

    $this->actingAs($user)
        ->get(route('academy.index'))
        ->assertOk()
        ->assertSee('Multilingual course')
        ->assertSee(route('academy.courses.launch', $course->slug, absolute: false), false);

    $response = $this->actingAs($user)
        ->get(route('academy-courses.show', [
            'academyCoursePath' => $course->contentRouteSegment(),
            'asset' => 'EN/index.html',
        ]))
        ->assertOk();

    expect($response->baseResponse->getFile()->getPathname())->toBe($englishIndex);
});

test('dashboard links authenticated users to the academy catalog', function () {
    $user = User::factory()->create();
    AcademyCourse::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertSee(route('academy.index', absolute: false), false)
        ->assertSee(__('hermes.dashboard.academy_title'));
});

test('academy shows a clear empty state with a dashboard action when there are no courses', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('academy.index'))
        ->assertOk()
        ->assertSee(__('hermes.academy.empty_title'))
        ->assertSee(__('hermes.academy.empty_text'))
        ->assertSee(__('hermes.academy.empty_action'))
        ->assertSee(route('dashboard', absolute: false), false)
        ->assertSee('user-guidance-card', false);
});

test('guests are redirected to the login page when opening academy course content', function () {
    $course = AcademyCourse::factory()->create();
    File::ensureDirectoryExists(dirname($course->contentPath()));
    File::put($course->contentPath(), '<html><body>Course</body></html>');

    $this->get(route('academy-courses.show', ['academyCoursePath' => $course->contentRouteSegment()]))
        ->assertRedirect(route('login'));
});
