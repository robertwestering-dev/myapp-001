<?php

namespace App\Providers;

use App\Listeners\LogFailedLogin;
use App\Models\AcademyCourse;
use App\Models\BlogPost;
use App\Models\Questionnaire;
use App\Models\User;
use App\Policies\AcademyCoursePolicy;
use App\Policies\BlogPostPolicy;
use App\Policies\QuestionnairePolicy;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerPolicies();
        $this->registerListeners();
    }

    protected function registerPolicies(): void
    {
        Gate::policy(AcademyCourse::class, AcademyCoursePolicy::class);
        Gate::policy(Questionnaire::class, QuestionnairePolicy::class);
        Gate::policy(BlogPost::class, BlogPostPolicy::class);
    }

    protected function registerListeners(): void
    {
        Event::listen(Failed::class, LogFailedLogin::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        VerifyEmail::createUrlUsing(function (User $user): string {
            return URL::temporarySignedRoute(
                'verification.public',
                now()->addMinutes(60),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ],
            );
        });

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
