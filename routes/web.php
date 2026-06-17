<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\OrganizationTypeController;
use App\Http\Controllers\OrganizationUnitController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will be
| assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clinical Pathway Matrix
    Route::middleware(['permission:manage_clinical_pathway'])->group(function () {
        Route::get('/diagnoses', [\App\Http\Controllers\DiagnosisController::class, 'index'])->name('diagnoses.index');
        // Static route must come before wildcard {diagnosis} route
        Route::get('/diagnoses/services/search', [\App\Http\Controllers\DiagnosisPathwayController::class, 'searchServices'])->name('diagnoses.services.search');
        Route::get('/diagnoses/{diagnosis}/pathway', [\App\Http\Controllers\DiagnosisPathwayController::class, 'show'])->name('diagnoses.pathway');
        Route::post('/diagnoses/{diagnosis}/pathway', [\App\Http\Controllers\DiagnosisPathwayController::class, 'update'])->name('diagnoses.pathway.update');
    });

    Route::middleware(['permission:manage_service_categories'])->group(function () {
        Route::resource('service-categories', \App\Http\Controllers\ServiceCategoryController::class)->except(['create', 'show', 'edit']);
    });

    Route::middleware(['permission:manage_services'])->group(function () {
        Route::resource('services', \App\Http\Controllers\MedicalServiceController::class);
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Profile routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/toggle-layout', [App\Http\Controllers\ProfileController::class, 'toggleLayout'])->name('profile.toggle-layout');

    // User Management routes
    Route::middleware('permission:manage_users')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Role Management routes
    Route::middleware('permission:manage_roles')->group(function () {
        Route::resource('roles', RoleController::class);
    });

    // Permission Management routes
    Route::middleware('permission:manage_permissions')->group(function () {
        Route::resource('permissions', PermissionController::class);
    });

    // Organization Type Management routes
    Route::middleware('permission:manage_organization_types')->group(function () {
        Route::resource('organization-types', OrganizationTypeController::class);
    });

    // Organization Unit Management routes
    Route::middleware('permission:manage_organization_units')->group(function () {
        Route::resource('organization-units', OrganizationUnitController::class);
        
        // Member management routes
        Route::post('organization-units/{organization_unit}/members', [OrganizationUnitController::class, 'addMember'])
            ->name('organization-units.add-member');
        Route::delete('organization-units/{organization_unit}/members/{user}', [OrganizationUnitController::class, 'removeMember'])
            ->name('organization-units.remove-member');
        Route::patch('organization-units/{organization_unit}/head', [OrganizationUnitController::class, 'updateHead'])
            ->name('organization-units.update-head');
    });

});
