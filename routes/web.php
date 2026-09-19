<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AnalyticsController;
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
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Workspace\AnalyticsController as WorkspaceAnalyticsController;
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
use App\Support\DemoData\Platforms;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Orora School — UI shell routes
|--------------------------------------------------------------------------
| Super Admin (F3), Platform Workspace (F4) and Learner (F5) are real pages.
| Nav items still need a matching named route so a rail link can never 404.
*/

Route::redirect('/', '/login');

// ---- Unauthenticated pages (Phase F2) --------------------------------------
// Conventional Laravel route names, so real auth can take these over without
// touching a single link in the views.
Route::controller(AuthPagesController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'attemptLogin')->name('login.attempt');

    Route::get('/register', 'register')->name('register');
    Route::post('/register', 'storeRegistration')->name('register.store');
    Route::post('/register/link', 'linkPlatform')->name('register.link');

    Route::get('/forgot-password', 'forgotPassword')->name('password.request');
    Route::post('/forgot-password', 'sendResetLink')->name('password.email');

    Route::get('/reset-password/{token}', 'resetPassword')->name('password.reset');
    Route::post('/reset-password', 'updatePassword')->name('password.update');

    Route::get('/verify-email', 'verifyNotice')->name('verification.notice');
    Route::post('/verify-email', 'resendVerification')->name('verification.send');
    Route::get('/verify-email/confirmed', 'verified')->name('verification.verified');
});

// ---- Super Admin (Phase F3) ------------------------------------------------
Route::get('/admin', [SuperAdminController::class, 'dashboard'])->name('admin.dashboard');

Route::get('/admin/platforms', [PlatformController::class, 'index'])->name('admin.platforms');
Route::get('/admin/platforms/create', [PlatformController::class, 'create'])->name('admin.platforms.create');
Route::post('/admin/platforms', [PlatformController::class, 'store'])->name('admin.platforms.store');
Route::get('/admin/platforms/{platform}/edit', [PlatformController::class, 'edit'])->name('admin.platforms.edit');
Route::post('/admin/platforms/{platform}', [PlatformController::class, 'update'])->name('admin.platforms.update');
Route::post('/admin/platforms/{platform}/toggle', [PlatformController::class, 'toggle'])->name('admin.platforms.toggle');

Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show')->whereNumber('user');
Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit')->whereNumber('user');
Route::post('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update')->whereNumber('user');
Route::post('/admin/users/{user}/assign', [UserController::class, 'assign'])->name('admin.users.assign')->whereNumber('user');

Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.roles');
Route::get('/admin/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
Route::post('/admin/roles', [RoleController::class, 'store'])->name('admin.roles.store');
Route::get('/admin/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
Route::post('/admin/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');

Route::get('/admin/permissions', [PermissionController::class, 'index'])->name('admin.permissions');
Route::get('/admin/content', [ContentController::class, 'index'])->name('admin.content');
Route::get('/admin/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');
Route::get('/admin/activity', [ActivityLogController::class, 'index'])->name('admin.activity');
Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings');
Route::post('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');

// ---- Platform Workspace (Phase F4) -----------------------------------------
Route::whereIn('platform', Platforms::slugs())->group(function () {
    Route::get('/workspace/{platform}', [WorkspaceController::class, 'dashboard'])->name('workspace.dashboard');

    Route::get('/workspace/{platform}/courses', [WorkspaceCourseController::class, 'index'])->name('workspace.courses');
    Route::get('/workspace/{platform}/courses/create', [WorkspaceCourseController::class, 'create'])->name('workspace.courses.create');
    Route::post('/workspace/{platform}/courses', [WorkspaceCourseController::class, 'store'])->name('workspace.courses.store');
    Route::get('/workspace/{platform}/courses/{course}', [WorkspaceCourseController::class, 'show'])->name('workspace.courses.show');
    Route::get('/workspace/{platform}/courses/{course}/edit', [WorkspaceCourseController::class, 'edit'])->name('workspace.courses.edit');
    Route::post('/workspace/{platform}/courses/{course}', [WorkspaceCourseController::class, 'update'])->name('workspace.courses.update');
    Route::post('/workspace/{platform}/courses/{course}/transition', [WorkspaceCourseController::class, 'transition'])->name('workspace.courses.transition');

    Route::get('/workspace/{platform}/categories', [WorkspaceCategoryController::class, 'index'])->name('workspace.categories');

    Route::get('/workspace/{platform}/modules', [WorkspaceCurriculumController::class, 'modules'])->name('workspace.modules');
    Route::post('/workspace/{platform}/modules', [WorkspaceCurriculumController::class, 'storeModule'])->name('workspace.modules.store');
    Route::get('/workspace/{platform}/lessons', [WorkspaceCurriculumController::class, 'lessons'])->name('workspace.lessons');
    Route::post('/workspace/{platform}/lessons', [WorkspaceCurriculumController::class, 'storeLesson'])->name('workspace.lessons.store');

    Route::get('/workspace/{platform}/quizzes', [WorkspaceQuizController::class, 'index'])->name('workspace.quizzes');
    Route::post('/workspace/{platform}/quizzes', [WorkspaceQuizController::class, 'store'])->name('workspace.quizzes.store');
    Route::get('/workspace/{platform}/quizzes/{quiz}', [WorkspaceQuizController::class, 'show'])->name('workspace.quizzes.show');
    Route::post('/workspace/{platform}/quizzes/{quiz}', [WorkspaceQuizController::class, 'update'])->name('workspace.quizzes.update');

    Route::get('/workspace/{platform}/resources', [WorkspaceResourceController::class, 'index'])->name('workspace.resources');
    Route::get('/workspace/{platform}/resources/create', [WorkspaceResourceController::class, 'create'])->name('workspace.resources.create');
    Route::post('/workspace/{platform}/resources', [WorkspaceResourceController::class, 'store'])->name('workspace.resources.store');

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

    Route::get('/workspace/{platform}/analytics', [WorkspaceAnalyticsController::class, 'index'])->name('workspace.analytics');
});

Route::get('/verify/{code}', [CertificateVerificationController::class, 'show'])->name('certificates.verify');

// ---- Learner (Phase F5) ----------------------------------------------------
Route::get('/learn', [LearnerController::class, 'dashboard'])->name('learner.dashboard');

Route::get('/learn/courses', [LearnerCourseController::class, 'index'])->name('learner.courses');
Route::get('/learn/courses/{course}', [LearnerCourseController::class, 'show'])->name('learner.courses.show');
Route::post('/learn/courses/{course}/enroll', [LearnerCourseController::class, 'enroll'])->name('learner.courses.enroll');
Route::get('/learn/courses/{course}/lessons/{lesson}', [LearnerLessonController::class, 'show'])->name('learner.courses.lessons.show');
Route::post('/learn/courses/{course}/lessons/{lesson}/complete', [LearnerLessonController::class, 'complete'])->name('learner.courses.lessons.complete');

Route::get('/learn/quizzes/{quiz}', [LearnerQuizController::class, 'show'])->name('learner.quizzes.show');
Route::post('/learn/quizzes/{quiz}', [LearnerQuizController::class, 'submit'])->name('learner.quizzes.submit');

Route::get('/learn/paths', [LearnerPathController::class, 'index'])->name('learner.paths');
Route::get('/learn/paths/{path}', [LearnerPathController::class, 'show'])->name('learner.paths.show');

Route::get('/learn/certificates', [LearnerLibraryController::class, 'certificates'])->name('learner.certificates');
Route::get('/learn/live-sessions', [LearnerLibraryController::class, 'sessions'])->name('learner.sessions');
Route::post('/learn/live-sessions/{session}/join', [LearnerLibraryController::class, 'join'])->name('learner.sessions.join')->whereNumber('session');
Route::get('/learn/resources', [LearnerLibraryController::class, 'resources'])->name('learner.resources');
Route::get('/learn/profile', [LearnerLibraryController::class, 'profile'])->name('learner.profile');
Route::post('/learn/profile', [LearnerLibraryController::class, 'updateProfile'])->name('learner.profile.update');

// ---- Component proof, rendered inside any of the three layouts -------------
Route::get('/design/components/{experience?}', [ComponentProofController::class, 'index'])
    ->whereIn('experience', ['super-admin', 'platform-workspace', 'learner'])
    ->name('design.components');

// ---- Sign-out placeholder --------------------------------------------------
// No session to end yet, but now that the guest pages exist it can at least land
// where a real sign-out would.
Route::post('/sign-out', function () {
    return redirect()->route('login')
        ->with('status', 'Signed out. Nothing was ended — this build has no authentication yet.');
})->name('sign-out');
