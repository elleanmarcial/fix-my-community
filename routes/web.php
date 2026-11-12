<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SuperAdminSessionController;
use App\Http\Controllers\SuperAdminUserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'super_admin') {
    return redirect()->route('super-admin.applications.index');
}

if ($user->role === 'admin') {
    return redirect()->route('admin.dashboard');
}

    $reports = \App\Models\Report::where('user_id', $user->id)->latest()->get();

    return view('dashboard', compact('reports'));
})->middleware(['auth', 'verified', 'account.active'])->name('dashboard');

Route::middleware(['auth', 'account.active'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::resource('reports', ReportController::class);

    Route::post('/profile/admin-application', [ProfileController::class, 'submitAdminApplication'])
        ->name('profile.admin-application.store');

});

Route::middleware(['auth', 'account.active', 'admin'])->group(function () {

    Route::get('/admin', [AdminController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('/admin/reports', [AdminReportController::class, 'index'])
        ->name('admin.reports.index');

    Route::get('/admin/reports/{report}', [AdminReportController::class, 'show'])
        ->name('admin.reports.show');

    Route::patch('/admin/reports/{report}', [AdminReportController::class, 'update'])
        ->name('admin.reports.update');

});

Route::middleware(['auth', 'account.active', 'super_admin'])->group(function () {

    Route::get('/super-admin/applications', [SuperAdminController::class, 'index'])
        ->name('super-admin.applications.index');

    Route::get('/super-admin/applications/{id}', [SuperAdminController::class, 'show'])
        ->name('super-admin.applications.show');

    Route::post('/super-admin/applications/{id}/approve', [SuperAdminController::class, 'approve'])
        ->name('super-admin.applications.approve');

    Route::post('/super-admin/applications/{id}/reject', [SuperAdminController::class, 'reject'])
        ->name('super-admin.applications.reject');
    
    Route::get('/super-admin/sessions/{role?}', [SuperAdminSessionController::class, 'index'])
    ->name('super-admin.sessions.index');

Route::delete('/super-admin/sessions/{sessionId}', [SuperAdminSessionController::class, 'destroy'])
    ->name('super-admin.sessions.destroy');

Route::get('/super-admin/users', [SuperAdminUserController::class, 'index'])
    ->name('super-admin.users.index');

Route::get('/super-admin/users/{id}', [SuperAdminUserController::class, 'show'])
    ->name('super-admin.users.show');

Route::patch('/super-admin/users/{id}/toggle-status', [SuperAdminUserController::class, 'toggleStatus'])
    ->name('super-admin.users.toggle-status');
});

require __DIR__.'/auth.php';