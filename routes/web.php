<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PlatformController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthPagesController;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\ComponentProofController;
use App\Http\Controllers\Learner\CourseController as LearnerCourseController;
use App\Http\Controllers\Learner\LessonController as LearnerLessonController;
use App\Http\Controllers\Learner\LibraryController as LearnerLibraryController;
use App\Http\Controllers\Learner\PathController as LearnerPathController;
use App\Http\Controllers\Learner\QuizController as LearnerQuizController;
use App\Http\Controllers\LearnerController;
use App\Http\Controllers\Public\CourseController as PublicCourseController;
use App\Http\Controllers\Public\HomeController as PublicHomeController;
use App\Http\Controllers\Public\PageController as PublicPageController;
use App\Http\Controllers\Public\PlatformController as PublicPlatformController;
use App\Http\Controllers\Public\ResourceController as PublicResourceController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Workspace\CategoryController as WorkspaceCategoryController;
use App\Http\Controllers\Workspace\CertificateController as WorkspaceCertificateController;
use App\Http\Controllers\Workspace\CourseController as WorkspaceCourseController;
use App\Http\Controllers\Workspace\CurriculumController as WorkspaceCurriculumController;
use App\Http\Controllers\Workspace\InstructorController as WorkspaceInstructorController;
use App\Http\Controllers\Workspace\LiveSessionController as WorkspaceLiveSessionController;
use App\Http\Controllers\Workspace\QuizController as WorkspaceQuizController;
use App\Http\Controllers\Workspace\ResourceController as WorkspaceResourceController;
use App\Http\Controllers\Workspace\RosterController as WorkspaceRosterController;
use App\Http\Controllers\WorkspaceController;
use App\UserRole;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| FarmSchool — UI shell routes
|--------------------------------------------------------------------------
| Public (F-Public), Super Admin (F3), Platform Workspace (F4) and Learner (F5)
| are real pages. Nav items still need a matching named route so a rail link
| can never 404.
*/

Route::get('/', [PublicHomeController::class, 'index'])->name('home');
Route::get('/about', [PublicPageController::class, 'about'])->name('about');
Route::get('/contact', [PublicPageController::class, 'contact'])->name('contact');
Route::get('/courses', [PublicCourseController::class, 'index'])->name('catalog.courses');
Route::get('/courses/{course}', [PublicCourseController::class, 'show'])->name('catalog.courses.show');
Route::get('/platforms', [PublicPlatformController::class, 'index'])->name('catalog.platforms');
Route::get('/platforms/{platform}', [PublicPlatformController::class, 'show'])->name('catalog.platforms.show');
Route::get('/resources', [PublicResourceController::class, 'index'])->name('catalog.resources');
Route::get('/resources/{resource}', [PublicResourceController::class, 'show'])->name('catalog.resources.show')
    ->where('resource', '[a-z0-9]+(?:-[a-z0-9]+)*');
Route::get('/resources/{resource}/file', [PublicResourceController::class, 'file'])->name('catalog.resources.file')
    ->where('resource', '[a-z0-9]+(?:-[a-z0-9]+)*');
Route::get('/certificates', [CertificateVerificationController::class, 'index'])->name('certificates.lookup');
Route::get('/certificates/{code}', [CertificateVerificationController::class, 'show'])->name('certificates.verify');
Route::get('/verify/{code}', function (string $code) {
    return redirect()->route('certificates.verify', ['code' => $code]);
});

// ---- Authentication --------------------------------------------------------
Route::middleware('guest')->controller(AuthPagesController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'attemptLogin')->name('login.attempt');

    Route::get('/register', 'register')->name('register');
    Route::post('/register', 'storeRegistration')->name('register.store');

    Route::get('/forgot-password', 'forgotPassword')->name('password.request');
    Route::post('/forgot-password', 'sendResetLink')->middleware('throttle:6,1')->name('password.email');

    Route::get('/reset-password/{token}', 'resetPassword')->name('password.reset');
    Route::post('/reset-password', 'updatePassword')->name('password.update');
});

Route::middleware('auth')->controller(AuthPagesController::class)->group(function () {
    Route::get('/verify-email', 'verifyNotice')->name('verification.notice');
    Route::post('/verify-email', 'resendVerification')->middleware('throttle:6,1')->name('verification.send');
    Route::get('/verify-email/confirmed', 'verified')->name('verification.verified');
    Route::get('/email/verify/{id}/{hash}', 'verify')->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/sign-out', 'logout')->name('sign-out');
});

// ---- Super Admin (Phase F3) ------------------------------------------------
Route::middleware(['auth', 'verified', 'role:'.UserRole::SuperAdmin->value])->group(function () {
    Route::get('/admin', [SuperAdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/admin/platforms', [PlatformController::class, 'index'])->name('admin.platforms');
    Route::get('/admin/platforms/create', [PlatformController::class, 'create'])->name('admin.platforms.create');
    Route::post('/admin/platforms', [PlatformController::class, 'store'])->name('admin.platforms.store');
    Route::get('/admin/platforms/{platform}/edit', [PlatformController::class, 'edit'])->name('admin.platforms.edit');
    Route::post('/admin/platforms/{platform}', [PlatformController::class, 'update'])->name('admin.platforms.update');
    Route::post('/admin/platforms/{platform}/toggle', [PlatformController::class, 'toggle'])->name('admin.platforms.toggle');
    Route::delete('/admin/platforms/{platform}', [PlatformController::class, 'destroy'])->name('admin.platforms.destroy');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/accounts/{user}', [UserController::class, 'showAccount'])->name('admin.accounts.show');
    Route::get('/admin/accounts/{user}/edit', [UserController::class, 'editAccount'])->name('admin.accounts.edit');
    Route::post('/admin/accounts/{user}', [UserController::class, 'updateAccount'])->name('admin.accounts.update');
    Route::delete('/admin/accounts/{user}', [UserController::class, 'destroyAccount'])->name('admin.accounts.destroy');
    Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show')->whereNumber('user');
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit')->whereNumber('user');
    Route::post('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update')->whereNumber('user');
    Route::post('/admin/users/{user}/assign', [UserController::class, 'assign'])->name('admin.users.assign')->whereNumber('user');

    Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.roles');
    Route::get('/admin/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/admin/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::get('/admin/roles/{role}', [RoleController::class, 'show'])->name('admin.roles.show');
    Route::get('/admin/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
    Route::post('/admin/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('/admin/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');

    Route::get('/admin/permissions', [PermissionController::class, 'index'])->name('admin.permissions');
    Route::get('/admin/content', [ContentController::class, 'index'])->name('admin.content');
    Route::get('/admin/activity', [ActivityLogController::class, 'index'])->name('admin.activity');
    Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings');
    Route::post('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
});

// ---- Platform Workspace (Phase F4) -----------------------------------------
Route::middleware(['auth', 'verified', 'role:'.UserRole::SuperAdmin->value.','.UserRole::PlatformStaff->value, 'platform.access'])
    ->where(['platform' => '[a-z0-9]+(?:-[a-z0-9]+)*'])
    ->group(function () {
        Route::get('/workspace/{platform}', [WorkspaceController::class, 'dashboard'])->name('workspace.dashboard');

        Route::get('/workspace/{platform}/courses', [WorkspaceCourseController::class, 'index'])->name('workspace.courses');
        Route::get('/workspace/{platform}/courses/create', [WorkspaceCourseController::class, 'create'])->name('workspace.courses.create');
        Route::post('/workspace/{platform}/courses', [WorkspaceCourseController::class, 'store'])->name('workspace.courses.store');
        Route::get('/workspace/{platform}/courses/{course}', [WorkspaceCourseController::class, 'show'])->name('workspace.courses.show');
        Route::get('/workspace/{platform}/courses/{course}/edit', [WorkspaceCourseController::class, 'edit'])->name('workspace.courses.edit');
        Route::post('/workspace/{platform}/courses/{course}', [WorkspaceCourseController::class, 'update'])->name('workspace.courses.update');
        Route::post('/workspace/{platform}/courses/{course}/transition', [WorkspaceCourseController::class, 'transition'])->name('workspace.courses.transition');

        Route::get('/workspace/{platform}/categories', [WorkspaceCategoryController::class, 'index'])->name('workspace.categories');
        Route::post('/workspace/{platform}/categories', [WorkspaceCategoryController::class, 'store'])->name('workspace.categories.store');
        Route::delete('/workspace/{platform}/categories/{category}', [WorkspaceCategoryController::class, 'destroy'])->name('workspace.categories.destroy')
            ->where('category', '[a-z0-9]+(?:-[a-z0-9]+)*');

        Route::get('/workspace/{platform}/modules', [WorkspaceCurriculumController::class, 'modules'])->name('workspace.modules');
        Route::post('/workspace/{platform}/modules', [WorkspaceCurriculumController::class, 'storeModule'])->name('workspace.modules.store');
        Route::post('/workspace/{platform}/modules/{module}', [WorkspaceCurriculumController::class, 'updateModule'])->name('workspace.modules.update')
            ->where('module', '[a-z0-9]+(?:-[a-z0-9]+)*');
        Route::delete('/workspace/{platform}/modules/{module}', [WorkspaceCurriculumController::class, 'destroyModule'])->name('workspace.modules.destroy')
            ->where('module', '[a-z0-9]+(?:-[a-z0-9]+)*');
        Route::get('/workspace/{platform}/lessons', [WorkspaceCurriculumController::class, 'lessons'])->name('workspace.lessons');
        Route::post('/workspace/{platform}/lessons', [WorkspaceCurriculumController::class, 'storeLesson'])->name('workspace.lessons.store');
        Route::post('/workspace/{platform}/lessons/{lesson}', [WorkspaceCurriculumController::class, 'updateLesson'])->name('workspace.lessons.update')
            ->where('lesson', '[a-z0-9]+(?:-[a-z0-9]+)*');
        Route::delete('/workspace/{platform}/lessons/{lesson}', [WorkspaceCurriculumController::class, 'destroyLesson'])->name('workspace.lessons.destroy')
            ->where('lesson', '[a-z0-9]+(?:-[a-z0-9]+)*');

        Route::get('/workspace/{platform}/quizzes', [WorkspaceQuizController::class, 'index'])->name('workspace.quizzes');
        Route::post('/workspace/{platform}/quizzes', [WorkspaceQuizController::class, 'store'])->name('workspace.quizzes.store');
        Route::get('/workspace/{platform}/quizzes/{quiz}', [WorkspaceQuizController::class, 'show'])->name('workspace.quizzes.show');
        Route::post('/workspace/{platform}/quizzes/{quiz}', [WorkspaceQuizController::class, 'update'])->name('workspace.quizzes.update');

        Route::get('/workspace/{platform}/resources', [WorkspaceResourceController::class, 'index'])->name('workspace.resources');
        Route::get('/workspace/{platform}/resources/create', [WorkspaceResourceController::class, 'create'])->name('workspace.resources.create');
        Route::post('/workspace/{platform}/resources', [WorkspaceResourceController::class, 'store'])->name('workspace.resources.store');
        Route::get('/workspace/{platform}/resources/{resource}', [WorkspaceResourceController::class, 'show'])->name('workspace.resources.show')
            ->where('resource', '[a-z0-9]+(?:-[a-z0-9]+)*');
        Route::get('/workspace/{platform}/resources/{resource}/edit', [WorkspaceResourceController::class, 'edit'])->name('workspace.resources.edit')
            ->where('resource', '[a-z0-9]+(?:-[a-z0-9]+)*');
        Route::post('/workspace/{platform}/resources/{resource}', [WorkspaceResourceController::class, 'update'])->name('workspace.resources.update')
            ->where('resource', '[a-z0-9]+(?:-[a-z0-9]+)*');
        Route::delete('/workspace/{platform}/resources/{resource}', [WorkspaceResourceController::class, 'destroy'])->name('workspace.resources.destroy')
            ->where('resource', '[a-z0-9]+(?:-[a-z0-9]+)*');
        Route::get('/workspace/{platform}/resources/{resource}/file', [WorkspaceResourceController::class, 'file'])->name('workspace.resources.file')
            ->where('resource', '[a-z0-9]+(?:-[a-z0-9]+)*');

        Route::get('/workspace/{platform}/instructors', [WorkspaceInstructorController::class, 'index'])->name('workspace.instructors');
        Route::get('/workspace/{platform}/instructors/{instructor}', [WorkspaceInstructorController::class, 'show'])->name('workspace.instructors.show')->whereNumber('instructor');

        Route::get('/workspace/{platform}/learners', [WorkspaceRosterController::class, 'index'])->name('workspace.learners');
        Route::get('/workspace/{platform}/learners/{learner}', [WorkspaceRosterController::class, 'show'])->name('workspace.learners.show')->whereNumber('learner');

        Route::get('/workspace/{platform}/certificates', [WorkspaceCertificateController::class, 'index'])->name('workspace.certificates');
        Route::post('/workspace/{platform}/certificates/{certificate}/revoke', [WorkspaceCertificateController::class, 'revoke'])->name('workspace.certificates.revoke');

        Route::get('/workspace/{platform}/live-sessions', [WorkspaceLiveSessionController::class, 'index'])->name('workspace.sessions');
        Route::get('/workspace/{platform}/live-sessions/create', [WorkspaceLiveSessionController::class, 'create'])->name('workspace.sessions.create');
        Route::post('/workspace/{platform}/live-sessions', [WorkspaceLiveSessionController::class, 'store'])->name('workspace.sessions.store');
        Route::get('/workspace/{platform}/live-sessions/{session}', [WorkspaceLiveSessionController::class, 'edit'])->name('workspace.sessions.edit')->whereNumber('session');
        Route::post('/workspace/{platform}/live-sessions/{session}', [WorkspaceLiveSessionController::class, 'update'])->name('workspace.sessions.update')->whereNumber('session');
    });

// ---- Learner (Phase F5) ----------------------------------------------------
Route::middleware(['auth', 'verified', 'role:'.UserRole::Learner->value])->group(function () {
    Route::get('/learn', [LearnerController::class, 'dashboard'])->name('learner.dashboard');

    Route::get('/learn/courses', [LearnerCourseController::class, 'index'])->name('learner.courses');
    Route::get('/learn/courses/{course}', [LearnerCourseController::class, 'show'])->name('learner.courses.show');
    Route::post('/learn/courses/{course}/enroll', [LearnerCourseController::class, 'enroll'])->name('learner.courses.enroll');
    Route::get('/learn/courses/{course}/lessons/{lesson}', [LearnerLessonController::class, 'show'])->name('learner.courses.lessons.show');
    Route::get('/learn/courses/{course}/lessons/{lesson}/file', [LearnerLessonController::class, 'file'])->name('learner.courses.lessons.file')
        ->where('lesson', '[a-z0-9]+(?:-[a-z0-9]+)*');
    Route::post('/learn/courses/{course}/lessons/{lesson}/complete', [LearnerLessonController::class, 'complete'])->name('learner.courses.lessons.complete');

    Route::get('/learn/quizzes/{quiz}', [LearnerQuizController::class, 'show'])->name('learner.quizzes.show');
    Route::post('/learn/quizzes/{quiz}', [LearnerQuizController::class, 'submit'])->name('learner.quizzes.submit');

    Route::get('/learn/paths', [LearnerPathController::class, 'index'])->name('learner.paths');
    Route::get('/learn/paths/{path}', [LearnerPathController::class, 'show'])->name('learner.paths.show');

    Route::get('/learn/certificates', [LearnerLibraryController::class, 'certificates'])->name('learner.certificates');
    Route::get('/learn/live-sessions', [LearnerLibraryController::class, 'sessions'])->name('learner.sessions');
    Route::post('/learn/live-sessions/{session}/join', [LearnerLibraryController::class, 'join'])->name('learner.sessions.join')->whereNumber('session');
    Route::get('/learn/resources', [LearnerLibraryController::class, 'resources'])->name('learner.resources');
    Route::get('/learn/resources/{resource}', [LearnerLibraryController::class, 'showResource'])->name('learner.resources.show')
        ->where('resource', '[a-z0-9]+(?:-[a-z0-9]+)*');
    Route::get('/learn/resources/{resource}/file', [LearnerLibraryController::class, 'fileResource'])->name('learner.resources.file')
        ->where('resource', '[a-z0-9]+(?:-[a-z0-9]+)*');
    Route::get('/learn/profile', [LearnerLibraryController::class, 'profile'])->name('learner.profile');
    Route::post('/learn/profile', [LearnerLibraryController::class, 'updateProfile'])->name('learner.profile.update');
});

// ---- Component proof, rendered inside any of the three layouts -------------
Route::get('/design/components/{experience?}', [ComponentProofController::class, 'index'])
    ->whereIn('experience', ['super-admin', 'platform-workspace', 'learner'])
    ->name('design.components');
