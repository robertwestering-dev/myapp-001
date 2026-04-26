<?php

use App\Http\Controllers\AcademyController;
use App\Http\Controllers\Admin\AcademyCourseController;
use App\Http\Controllers\Admin\AdminPortalController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\MediaAssetController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\OrganizationQuestionnaireController;
use App\Http\Controllers\Admin\QuestionnaireCategoryController;
use App\Http\Controllers\Admin\QuestionnaireController;
use App\Http\Controllers\Admin\QuestionnaireQuestionController;
use App\Http\Controllers\Admin\QuestionnaireResponseReportController;
use App\Http\Controllers\Admin\StrategyPageController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\Admin\TwoFactorNoticeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogSitemapController;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForumReplyController;
use App\Http\Controllers\ForumThreadController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProUpgradeController;
use App\Http\Controllers\QuestionnaireLibraryController;
use App\Http\Controllers\QuestionnaireResponseController;
use App\Http\Middleware\EnsureTwoFactorEnabled;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsGlobalAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $user = $request->user();

    if ($user !== null && ! $request->boolean('contact')) {
        return redirect()->route($user->canAccessAdminPortal() ? 'admin.portal' : 'dashboard');
    }

    return view('home');
})->name('home');

Route::post('/contact', [ContactRequestController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::post('/locale', LocaleController::class)->middleware('throttle:30,1')->name('locale.update');
Route::get('/sitemap.xml', BlogSitemapController::class)->name('sitemap');
Route::view('/inspiratiebronnen', 'inspiration-sources')->name('inspiration-sources.show');
Route::view('/over-ons', 'about')->name('about.show');
Route::view('/prijzen', 'pricing')->name('pricing.show');
Route::middleware('auth')->group(function (): void {
    Route::view('/pro-upgrade', 'pro-upgrade')->name('pro-upgrade.show');
    Route::post('/pro-upgrade', ProUpgradeController::class)->name('pro-upgrade.store');
});
Route::view('/privacy', 'privacy')->name('privacy.show');
Route::view('/voor-organisaties', 'organizations')->name('organizations.landing');
Route::view('/contact', 'contact')->name('contact.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{blogPost}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/academy', [AcademyController::class, 'index'])->name('academy.index');
    Route::get('/forum', [ForumThreadController::class, 'index'])->name('forum.index');
    Route::get('/forum/{forumThread}', [ForumThreadController::class, 'show'])->name('forum.show');
    Route::post('/forum', [ForumThreadController::class, 'store'])->middleware('throttle:10,1')->name('forum.store');
    Route::post('/forum/{forumThread}/replies', [ForumReplyController::class, 'store'])->middleware('throttle:20,1')->name('forum-replies.store');
    Route::put('/forum/{forumThread}/replies/{forumReply}', [ForumReplyController::class, 'update'])->name('forum-replies.update');
    Route::delete('/forum/{forumThread}/replies/{forumReply}', [ForumReplyController::class, 'destroy'])->name('forum-replies.destroy');
    Route::get('/vragenlijsten', QuestionnaireLibraryController::class)->name('questionnaires.index');
});

Route::middleware(['auth', 'verified'])
    ->prefix('questionnaires')
    ->name('questionnaire-responses.')
    ->group(function (): void {
        Route::get('/resume/{token}', [QuestionnaireResponseController::class, 'resume'])->name('resume');
        Route::get('/results/{response}', [QuestionnaireResponseController::class, 'results'])->name('results');
        Route::get('/{organizationQuestionnaire}', [QuestionnaireResponseController::class, 'show'])->name('show');
        Route::post('/{organizationQuestionnaire}', [QuestionnaireResponseController::class, 'store'])->name('store');
    });

Route::get('/verify-email/{id}/{hash}', EmailVerificationController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.public');

Route::post('/email/verification-notification', EmailVerificationNotificationController::class)
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::middleware(['auth', EnsureUserIsAdmin::class])
    ->prefix('admin-portal')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/two-factor-notice', TwoFactorNoticeController::class)->name('two-factor.notice');

        Route::middleware([EnsureTwoFactorEnabled::class])
            ->group(function (): void {
                Route::get('/', [AdminPortalController::class, 'index'])->name('portal');

                Route::middleware([EnsureUserIsGlobalAdmin::class])
                    ->prefix('strategie')
                    ->name('strategy-pages.')
                    ->group(function (): void {
                        Route::get('/', [StrategyPageController::class, 'index'])->name('index');
                        Route::get('/{page}', [StrategyPageController::class, 'show'])->name('show');
                        Route::get('/{page}/preview', [StrategyPageController::class, 'preview'])->name('preview');
                    });

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
                        Route::get('/{blogPost}/preview', [BlogPostController::class, 'preview'])->name('preview');
                        Route::get('/{blogPost}/edit', [BlogPostController::class, 'edit'])->name('edit');
                        Route::get('/{blogPost}/confirm-delete', [BlogPostController::class, 'confirmDestroy'])->name('confirm-delete');
                        Route::put('/{blogPost}', [BlogPostController::class, 'update'])->name('update');
                        Route::delete('/{blogPost}', [BlogPostController::class, 'destroy'])->name('destroy');
                    });

                Route::middleware([EnsureUserIsGlobalAdmin::class])
                    ->prefix('media-assets')
                    ->name('media-assets.')
                    ->group(function (): void {
                        Route::get('/', [MediaAssetController::class, 'index'])->name('index');
                        Route::post('/', [MediaAssetController::class, 'store'])->name('store');
                    });

                Route::prefix('users')
                    ->name('users.')
                    ->group(function (): void {
                        Route::get('/', [UserController::class, 'index'])->name('index');
                        Route::get('/create', [UserController::class, 'create'])->name('create');
                        Route::post('/', [UserController::class, 'store'])->name('store');
                        Route::get('/export', [UserController::class, 'export'])->middleware('throttle:10,1')->name('export');
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
                        Route::post('/{questionnaire}/toggle', [QuestionnaireController::class, 'toggle'])->name('toggle');

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

                        Route::get('/{questionnaire}/availability', [OrganizationQuestionnaireController::class, 'index'])->name('availability.index');
                        Route::get('/{questionnaire}/availability/create', [OrganizationQuestionnaireController::class, 'create'])->name('availability.create');
                        Route::post('/{questionnaire}/availability', [OrganizationQuestionnaireController::class, 'store'])->name('availability.store');
                        Route::get('/{questionnaire}/availability/{organizationQuestionnaire}/edit', [OrganizationQuestionnaireController::class, 'edit'])->name('availability.edit');
                        Route::post('/{questionnaire}/availability/{organization}/toggle', [OrganizationQuestionnaireController::class, 'toggle'])->name('availability.toggle');
                        Route::put('/{questionnaire}/availability/{organizationQuestionnaire}', [OrganizationQuestionnaireController::class, 'update'])->name('availability.update');
                        Route::delete('/{questionnaire}/availability/{organizationQuestionnaire}', [OrganizationQuestionnaireController::class, 'destroy'])->name('availability.destroy');
                    });

                Route::prefix('questionnaire-responses')
                    ->name('questionnaire-responses.')
                    ->group(function (): void {
                        Route::get('/', [QuestionnaireResponseReportController::class, 'index'])->name('index');
                        Route::get('/stats', [QuestionnaireResponseReportController::class, 'stats'])->name('stats');
                        Route::get('/export', [QuestionnaireResponseReportController::class, 'export'])->middleware('throttle:10,1')->name('export');
                        Route::get('/export-summary', [QuestionnaireResponseReportController::class, 'exportSummary'])->middleware('throttle:10,1')->name('export-summary');
                        Route::get('/export-stats', [QuestionnaireResponseReportController::class, 'exportStats'])->middleware('throttle:10,1')->name('export-stats');
                        Route::get('/{response}', [QuestionnaireResponseReportController::class, 'show'])->name('show');
                    });

                Route::middleware([EnsureUserIsGlobalAdmin::class])
                    ->prefix('audit-logs')
                    ->name('audit-logs.')
                    ->group(function (): void {
                        Route::get('/', [AuditLogController::class, 'index'])->name('index');
                    });
            }); // end EnsureTwoFactorEnabled group
    });

require __DIR__.'/settings.php';
