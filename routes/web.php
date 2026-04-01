<?php

use App\Http\Controllers\AcademyController;
use App\Http\Controllers\Admin\AcademyCourseController;
use App\Http\Controllers\Admin\AdminPortalController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\OrganizationQuestionnaireController;
use App\Http\Controllers\Admin\QuestionnaireCategoryController;
use App\Http\Controllers\Admin\QuestionnaireController;
use App\Http\Controllers\Admin\QuestionnaireQuestionController;
use App\Http\Controllers\Admin\QuestionnaireResponseReportController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\QuestionnaireResponseController;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsGlobalAdmin;
use App\Models\AcademyCourse;
use App\Models\OrganizationQuestionnaire;
use App\Models\QuestionnaireResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $user = $request->user();

    if ($user !== null && ! $request->boolean('contact')) {
        return redirect()->route($user->canAccessAdminPortal() ? 'admin.portal' : 'dashboard');
    }

    return view('home');
})->name('home');

Route::post('/contact', [ContactRequestController::class, 'store'])->name('contact.store');
Route::post('/locale', LocaleController::class)->name('locale.update');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{blogPost}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/dashboard', function (Request $request) {
    $user = $request->user();

    if ($user !== null && $user->canAccessAdminPortal()) {
        return redirect()->route('admin.portal');
    }

    $availableQuestionnaires = OrganizationQuestionnaire::query()
        ->with('questionnaire')
        ->where('org_id', $user?->org_id)
        ->where('is_active', true)
        ->get()
        ->filter(fn (OrganizationQuestionnaire $organizationQuestionnaire): bool => $organizationQuestionnaire->isAvailable())
        ->map(function (OrganizationQuestionnaire $organizationQuestionnaire) use ($user): OrganizationQuestionnaire {
            $organizationQuestionnaire->setRelation(
                'currentResponse',
                QuestionnaireResponse::query()
                    ->where('organization_questionnaire_id', $organizationQuestionnaire->id)
                    ->where('user_id', $user?->id)
                    ->first(),
            );

            return $organizationQuestionnaire;
        });

    return view('dashboard', [
        'availableQuestionnaires' => $availableQuestionnaires,
        'academyCourseCount' => AcademyCourse::query()->active()->count(),
    ]);
})
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function (): void {
    Route::get('/academy', [AcademyController::class, 'index'])->name('academy.index');
});

Route::middleware(['auth'])
    ->prefix('questionnaires')
    ->name('questionnaire-responses.')
    ->group(function (): void {
        Route::get('/{organizationQuestionnaire}', [QuestionnaireResponseController::class, 'show'])->name('show');
        Route::post('/{organizationQuestionnaire}', [QuestionnaireResponseController::class, 'store'])->name('store');
    });

Route::get('/verify-email/{id}/{hash}', EmailVerificationController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.public');

Route::middleware(['auth', EnsureUserIsAdmin::class])
    ->prefix('admin-portal')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', [AdminPortalController::class, 'index'])->name('portal');

        Route::middleware([EnsureUserIsGlobalAdmin::class])
            ->prefix('academy-courses')
            ->name('academy-courses.')
            ->group(function (): void {
                Route::get('/', [AcademyCourseController::class, 'index'])->name('index');
                Route::get('/create', [AcademyCourseController::class, 'create'])->name('create');
                Route::post('/', [AcademyCourseController::class, 'store'])->name('store');
                Route::get('/{academyCourse}/edit', [AcademyCourseController::class, 'edit'])->name('edit');
                Route::get('/{academyCourse}/confirm-delete', [AcademyCourseController::class, 'confirmDestroy'])->name('confirm-delete');
                Route::put('/{academyCourse}', [AcademyCourseController::class, 'update'])->name('update');
                Route::delete('/{academyCourse}', [AcademyCourseController::class, 'destroy'])->name('destroy');
            });

        Route::middleware([EnsureUserIsGlobalAdmin::class])
            ->prefix('translations')
            ->name('translations.')
            ->group(function (): void {
                Route::get('/', [TranslationController::class, 'index'])->name('index');
                Route::get('/edit', [TranslationController::class, 'edit'])->name('edit');
                Route::put('/', [TranslationController::class, 'update'])->name('update');
            });

        Route::middleware([EnsureUserIsGlobalAdmin::class])
            ->prefix('blog-posts')
            ->name('blog-posts.')
            ->group(function (): void {
                Route::get('/', [BlogPostController::class, 'index'])->name('index');
                Route::get('/create', [BlogPostController::class, 'create'])->name('create');
                Route::post('/', [BlogPostController::class, 'store'])->name('store');
                Route::get('/{blogPost}/edit', [BlogPostController::class, 'edit'])->name('edit');
                Route::get('/{blogPost}/confirm-delete', [BlogPostController::class, 'confirmDestroy'])->name('confirm-delete');
                Route::put('/{blogPost}', [BlogPostController::class, 'update'])->name('update');
                Route::delete('/{blogPost}', [BlogPostController::class, 'destroy'])->name('destroy');
            });

        Route::prefix('users')
            ->name('users.')
            ->group(function (): void {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::get('/create', [UserController::class, 'create'])->name('create');
                Route::post('/', [UserController::class, 'store'])->name('store');
                Route::get('/export', [UserController::class, 'export'])->name('export');
                Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
                Route::get('/{user}/confirm-delete', [UserController::class, 'confirmDestroy'])->name('confirm-delete');
                Route::put('/{user}', [UserController::class, 'update'])->name('update');
                Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            });

        Route::prefix('organizations')
            ->name('organizations.')
            ->group(function (): void {
                Route::get('/', [OrganizationController::class, 'index'])->name('index');
                Route::get('/create', [OrganizationController::class, 'create'])->name('create');
                Route::post('/', [OrganizationController::class, 'store'])->name('store');
                Route::get('/{organization}/edit', [OrganizationController::class, 'edit'])->name('edit');
                Route::get('/{organization}/confirm-delete', [OrganizationController::class, 'confirmDestroy'])->name('confirm-delete');
                Route::put('/{organization}', [OrganizationController::class, 'update'])->name('update');
                Route::delete('/{organization}', [OrganizationController::class, 'destroy'])->name('destroy');
            });

        Route::prefix('questionnaires')
            ->name('questionnaires.')
            ->group(function (): void {
                Route::get('/', [QuestionnaireController::class, 'index'])->name('index');
                Route::get('/create', [QuestionnaireController::class, 'create'])->name('create');
                Route::post('/', [QuestionnaireController::class, 'store'])->name('store');
                Route::get('/{questionnaire}/edit', [QuestionnaireController::class, 'edit'])->name('edit');
                Route::put('/{questionnaire}', [QuestionnaireController::class, 'update'])->name('update');
                Route::delete('/{questionnaire}', [QuestionnaireController::class, 'destroy'])->name('destroy');

                Route::get('/{questionnaire}/categories/create', [QuestionnaireCategoryController::class, 'create'])->name('categories.create');
                Route::post('/{questionnaire}/categories', [QuestionnaireCategoryController::class, 'store'])->name('categories.store');
                Route::get('/{questionnaire}/categories/{category}/edit', [QuestionnaireCategoryController::class, 'edit'])->name('categories.edit');
                Route::put('/{questionnaire}/categories/{category}', [QuestionnaireCategoryController::class, 'update'])->name('categories.update');
                Route::delete('/{questionnaire}/categories/{category}', [QuestionnaireCategoryController::class, 'destroy'])->name('categories.destroy');

                Route::get('/{questionnaire}/questions/create', [QuestionnaireQuestionController::class, 'create'])->name('questions.create');
                Route::post('/{questionnaire}/questions', [QuestionnaireQuestionController::class, 'store'])->name('questions.store');
                Route::get('/{questionnaire}/questions/{question}/edit', [QuestionnaireQuestionController::class, 'edit'])->name('questions.edit');
                Route::put('/{questionnaire}/questions/{question}', [QuestionnaireQuestionController::class, 'update'])->name('questions.update');
                Route::delete('/{questionnaire}/questions/{question}', [QuestionnaireQuestionController::class, 'destroy'])->name('questions.destroy');

                Route::get('/{questionnaire}/availability/create', [OrganizationQuestionnaireController::class, 'create'])->name('availability.create');
                Route::post('/{questionnaire}/availability', [OrganizationQuestionnaireController::class, 'store'])->name('availability.store');
                Route::get('/{questionnaire}/availability/{organizationQuestionnaire}/edit', [OrganizationQuestionnaireController::class, 'edit'])->name('availability.edit');
                Route::put('/{questionnaire}/availability/{organizationQuestionnaire}', [OrganizationQuestionnaireController::class, 'update'])->name('availability.update');
                Route::delete('/{questionnaire}/availability/{organizationQuestionnaire}', [OrganizationQuestionnaireController::class, 'destroy'])->name('availability.destroy');
            });

        Route::prefix('questionnaire-responses')
            ->name('questionnaire-responses.')
            ->group(function (): void {
                Route::get('/', [QuestionnaireResponseReportController::class, 'index'])->name('index');
                Route::get('/stats', [QuestionnaireResponseReportController::class, 'stats'])->name('stats');
                Route::get('/export', [QuestionnaireResponseReportController::class, 'export'])->name('export');
                Route::get('/export-summary', [QuestionnaireResponseReportController::class, 'exportSummary'])->name('export-summary');
                Route::get('/export-stats', [QuestionnaireResponseReportController::class, 'exportStats'])->name('export-stats');
                Route::get('/{response}', [QuestionnaireResponseReportController::class, 'show'])->name('show');
            });
    });

require __DIR__.'/settings.php';
