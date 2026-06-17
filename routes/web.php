<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\AdmissionLeadController;
use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\ChatRoomController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CoachingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ExamResultController;
use App\Http\Controllers\Admin\HomeworkController;
use App\Http\Controllers\Admin\LookupController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OnlineClassController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudyMaterialController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.attempt');

    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:5,1')->name('password.email');
    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

Route::post('logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('dashboard', fn () => redirect()->route('admin.dashboard'))
    ->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin Panel
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'tenant'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile & notifications (any authenticated admin user)
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Cascading dropdown lookups
    Route::prefix('lookups')->name('lookups.')->group(function () {
        Route::get('branches', [LookupController::class, 'branches'])->name('branches');
        Route::get('classes', [LookupController::class, 'classes'])->name('classes');
        Route::get('batches', [LookupController::class, 'batches'])->name('batches');
        Route::get('students', [LookupController::class, 'students'])->name('students');
    });

    // Banners
    Route::resource('banners', BannerController::class)->except(['create', 'show']);

    // Cities
    Route::get('cities/export', [CityController::class, 'export'])->name('cities.export');
    Route::resource('cities', CityController::class)->except(['create', 'show']);

    // Coachings
    Route::get('coachings/export', [CoachingController::class, 'export'])->name('coachings.export');
    Route::resource('coachings', CoachingController::class);

    // Branches
    Route::get('branches/export', [BranchController::class, 'export'])->name('branches.export');
    Route::resource('branches', BranchController::class)->except(['create', 'show']);

    // Users
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('users/{user}/permissions', [UserController::class, 'permissions'])->name('users.permissions');
    Route::put('users/{user}/permissions', [UserController::class, 'syncPermissions'])->name('users.permissions.sync');
    Route::resource('users', UserController::class)->except(['show']);

    // Roles & permissions
    Route::resource('roles', RoleController::class)->except(['show']);

    // Teachers
    Route::get('teachers/export', [TeacherController::class, 'export'])->name('teachers.export');
    Route::resource('teachers', TeacherController::class);

    // Students
    Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
    Route::get('students/id-cards/bulk', [StudentController::class, 'bulkIdCards'])->name('students.id-cards.bulk');
    Route::get('students/id-cards/batch/{batch}', [StudentController::class, 'batchIdCards'])->name('students.id-cards.batch');
    Route::get('students/{student}/id-card', [StudentController::class, 'idCard'])->name('students.id-card');
    Route::delete('students/{student}/documents/{document}', [StudentController::class, 'destroyDocument'])->name('students.documents.destroy');
    Route::resource('students', StudentController::class);

    // Classes
    Route::get('classes/export', [SchoolClassController::class, 'export'])->name('classes.export');
    Route::resource('classes', SchoolClassController::class)
        ->parameters(['classes' => 'class'])
        ->except(['create', 'show']);

    // Subjects
    Route::get('subjects/export', [SubjectController::class, 'export'])->name('subjects.export');
    Route::resource('subjects', SubjectController::class)->except(['create', 'show']);

    // Batches
    Route::get('batches/export', [BatchController::class, 'export'])->name('batches.export');
    Route::resource('batches', BatchController::class);

    // Homework
    Route::get('homework/export', [HomeworkController::class, 'export'])->name('homework.export');
    Route::put('homework/{homework}/submissions/{submission}', [HomeworkController::class, 'reviewSubmission'])->name('homework.submissions.review');
    Route::resource('homework', HomeworkController::class);

    // Notices
    Route::get('notices/export', [NoticeController::class, 'export'])->name('notices.export');
    Route::resource('notices', NoticeController::class);

    // Study materials
    Route::get('study-materials/export', [StudyMaterialController::class, 'export'])->name('study-materials.export');
    Route::get('study-materials/{study_material}/download', [StudyMaterialController::class, 'download'])->name('study-materials.download');
    Route::resource('study-materials', StudyMaterialController::class)->except(['show']);

    // Online classes
    Route::get('online-classes/export', [OnlineClassController::class, 'export'])->name('online-classes.export');
    Route::resource('online-classes', OnlineClassController::class)->except(['show']);

    // Exams & results
    Route::get('exams/export', [ExamController::class, 'export'])->name('exams.export');
    Route::get('exams/{exam}/results', [ExamResultController::class, 'entry'])->name('exams.results.entry');
    Route::post('exams/{exam}/results', [ExamResultController::class, 'store'])->name('exams.results.store');
    Route::post('exams/{exam}/results/publish', [ExamResultController::class, 'publish'])->name('exams.results.publish');
    Route::post('exams/{exam}/results/unpublish', [ExamResultController::class, 'unpublish'])->name('exams.results.unpublish');
    Route::get('exams/{exam}/results/export', [ExamResultController::class, 'export'])->name('exams.results.export');
    Route::get('exams/{exam}/results/{result}/marksheet', [ExamResultController::class, 'marksheet'])->name('exams.results.marksheet');
    Route::resource('exams', ExamController::class);

    // Chat monitoring
    Route::post('chat-rooms/{chat_room}/toggle-status', [ChatRoomController::class, 'toggleStatus'])->name('chat-rooms.toggle-status');
    Route::post('chat-rooms/{chat_room}/messages/{message}/flag', [ChatRoomController::class, 'flagMessage'])->name('chat-rooms.messages.flag');
    Route::delete('chat-rooms/{chat_room}/messages/{message}', [ChatRoomController::class, 'removeMessage'])->name('chat-rooms.messages.remove');
    Route::resource('chat-rooms', ChatRoomController::class)->only(['index', 'show']);

    // CRM admission leads
    Route::get('leads/export', [AdmissionLeadController::class, 'export'])->name('leads.export');
    Route::post('leads/{lead}/follow-up', [AdmissionLeadController::class, 'followUp'])->name('leads.follow-up');
    Route::resource('leads', AdmissionLeadController::class)->parameters(['leads' => 'lead']);

    // Subscriptions
    Route::get('subscriptions/export', [SubscriptionController::class, 'export'])->name('subscriptions.export');
    Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::resource('subscriptions', SubscriptionController::class)->except(['create', 'show']);
    Route::resource('subscription-plans', SubscriptionPlanController::class)
        ->parameters(['subscription-plans' => 'plan'])
        ->except(['create', 'show']);

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{type}/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('reports/{type}', [ReportController::class, 'show'])->name('reports.show');

    // Settings
    Route::put('settings/{group}', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('settings/{group?}', [SettingsController::class, 'index'])->name('settings.index');
});
