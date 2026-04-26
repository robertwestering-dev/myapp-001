<?php

use App\Models\BlogPost;

test('home page exposes seo metadata and structured data', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('<title>'.__('hermes.home_people.title').'</title>', false)
        ->assertSee('<meta name="description" content="'.__('hermes.home_people.meta_description').'">', false)
        ->assertSee('<link rel="canonical" href="'.route('home').'">', false)
        ->assertSee('<meta property="og:title" content="'.__('hermes.home_people.title').'">', false)
        ->assertSee('<meta name="twitter:description" content="'.__('hermes.home_people.meta_description').'">', false)
        ->assertSee(__('hermes.home_people.hero_title'))
        ->assertSeeInOrder([
            __('hermes.home_people.challenges_eyebrow'),
            __('hermes.about_page.story_section_eyebrow'),
            __('hermes.about_page.story_title'),
            __('hermes.about_page.mission_title'),
            __('hermes.home_people.resilience_model_eyebrow'),
            __('hermes.home_people.resilience_model_title'),
            __('hermes.home_people.tools_title'),
        ])
        ->assertSee('images/6lagen-model.png', false)
        ->assertSee('<section class="home-section about-story-section">', false)
        ->assertSee(route('organizations.landing', absolute: false), false)
        ->assertSee('Organisaties')
        ->assertSee('"@type": "WebSite"', false)
        ->assertSee('"@type": "Organization"', false);
});

test('organization page exposes seo metadata and organization messaging', function () {
    $response = $this->get(route('organizations.landing'));

    $response->assertOk()
        ->assertSee('<title>'.__('hermes.organizations_page.title').'</title>', false)
        ->assertSee('<meta name="description" content="'.__('hermes.organizations_page.meta_description').'">', false)
        ->assertSee('<link rel="canonical" href="'.route('organizations.landing').'">', false)
        ->assertSee(__('hermes.home.hero_title'))
        ->assertSee('Mensen haken af bij de zoveelste nieuwe tool of werkwijze')
        ->assertSee(__('hermes.home.sidebar_title'))
        ->assertSee('Ervaren medewerkers worden te weinig betrokken of raken zelfs uit beeld.')
        ->assertDontSee('Vooral ervaren medewerkers, die jarenlang de tent overeind hielden, raken te vaak uit beeld.')
        ->assertSee('Waarom ik dit doe')
        ->assertDontSee('Wie ik ben — en waarom ik dit doe')
        ->assertSee('De techniek werkt, maar de mensen haken af.')
        ->assertDontSee('De techniek werkt vaak wel, maar de mensen haken af.')
        ->assertSee(__('hermes.home.failure_title'))
        ->assertSee(__('hermes.home.offers_heading'))
        ->assertSee(__('hermes.home.plan_heading'))
        ->assertSee(__('hermes.home.bridge_title'))
        ->assertSee(__('hermes.home.closing_title'))
        ->assertSee('Home')
        ->assertSee('Blog')
        ->assertSee('Over')
        ->assertSee('Inspiratiebronnen')
        ->assertSee('Over ons')
        ->assertSee('Privacy')
        ->assertSee('Prijzen')
        ->assertSee('Organisaties')
        ->assertSee(route('home', absolute: false), false)
        ->assertSee('"@type": "WebPage"', false);
});

test('blog index exposes a non-empty localized heading and seo metadata', function () {
    $response = $this->get(route('blog.index'));

    $response->assertOk()
        ->assertSee(__('hermes.blog.summary_title'))
        ->assertSee('<meta name="description" content="'.__('hermes.blog.meta_description').'">', false)
        ->assertSee('<link rel="canonical" href="'.route('blog.index').'">', false)
        ->assertSee('Home')
        ->assertSee('Blog')
        ->assertSee('Over')
        ->assertSee('Inspiratiebronnen')
        ->assertSee('Over ons')
        ->assertSee('Privacy')
        ->assertSee('Prijzen')
        ->assertSee('Organisaties')
        ->assertSee('"@type": "Blog"', false);
});

test('blog article uses the homepage guest header for visitors', function () {
    $blogPost = BlogPost::factory()->create();

    $response = $this->get(route('blog.show', $blogPost));

    $response->assertOk()
        ->assertSee('Home')
        ->assertSee('Blog')
        ->assertSee('Over')
        ->assertSee('Inspiratiebronnen')
        ->assertSee('Over ons')
        ->assertSee('Privacy')
        ->assertSee('Prijzen')
        ->assertSee('Organisaties');
});
