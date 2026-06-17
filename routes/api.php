<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NearbyController;
use App\Models\Banner;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\Homework;
use App\Models\Notice;
use App\Models\OnlineClass;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudyMaterial;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1 — token auth via Sanctum, access gated by Spatie permissions.
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::get('public/nearby-coachings', NearbyController::class)->middleware('throttle:60,1');

    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware(['auth:sanctum', 'active'])->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::get('branches', function (Request $request) {
            return Branch::visibleTo($request->user())->active()->orderBy('name')->paginate(50);
        })->middleware('permission:branches.view');

        Route::get('classes', function (Request $request) {
            return SchoolClass::visibleTo($request->user())->active()->orderBy('name')->paginate(50);
        })->middleware('permission:classes.view');

        Route::get('subjects', function (Request $request) {
            return Subject::visibleTo($request->user())->active()->orderBy('name')->paginate(50);
        })->middleware('permission:subjects.view');

        Route::get('batches', function (Request $request) {
            return Batch::visibleTo($request->user())->active()->with(['schoolClass:id,name'])->paginate(50);
        })->middleware('permission:batches.view');

        Route::get('teachers', function (Request $request) {
            return Teacher::visibleTo($request->user())->active()->with('user:id,name,email,mobile')->paginate(50);
        })->middleware('permission:teachers.view');

        Route::get('students', function (Request $request) {
            return Student::visibleTo($request->user())->active()->with('user:id,name,email,mobile')->paginate(50);
        })->middleware('permission:students.view');

        Route::get('homework', function (Request $request) {
            return Homework::visibleTo($request->user())->with(['subject:id,name', 'targets'])->latest()->paginate(50);
        })->middleware('permission:homework.view');

        Route::get('notices', function (Request $request) {
            return Notice::visibleTo($request->user())->live()->latest('publish_at')->paginate(50);
        })->middleware('permission:notices.view');

        Route::get('study-materials', function (Request $request) {
            return StudyMaterial::visibleTo($request->user())->active()->with('subject:id,name')->latest()->paginate(50);
        })->middleware('permission:study-materials.view');

        Route::get('online-classes', function (Request $request) {
            return OnlineClass::visibleTo($request->user())->upcoming()->with('teacher.user:id,name')->paginate(50);
        })->middleware('permission:online-classes.view');

        Route::get('banners', function (Request $request) {
            return Banner::visibleTo($request->user())
                ->active()
                ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
                ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
                ->orderBy('sort_order')
                ->get();
        });

        Route::prefix('notifications')->group(function () {
            Route::get('/', function (Request $request) {
                return $request->user()->notifications()->paginate(20)->through(fn ($n) => [
                    'id'        => $n->id,
                    'title'     => $n->data['title'] ?? 'Notification',
                    'body'      => $n->data['body'] ?? '',
                    'url'       => $n->data['url'] ?? null,
                    'is_read'   => ! is_null($n->read_at),
                    'time'      => $n->created_at->diffForHumans(),
                    'created_at' => $n->created_at,
                ]);
            });

            Route::get('unread-count', function (Request $request) {
                return response()->json(['count' => $request->user()->unreadNotifications()->count()]);
            });

            Route::post('{id}/read', function (Request $request, string $id) {
                $request->user()->notifications()->findOrFail($id)->markAsRead();
                return response()->json(['message' => 'Marked as read.']);
            });

            Route::post('read-all', function (Request $request) {
                $request->user()->unreadNotifications->markAsRead();
                return response()->json(['message' => 'All marked as read.']);
            });
        });
    });
});
