<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AdminController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

 Route::middleware(['auth'])->group(function() {

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/poster/{day}', [DashboardController::class, 'posterMessage'])->name('poster.message');
Route::get('/doctor/create/{day}', [DoctorController::class, 'create'])->name('doctor.create');
Route::post('/doctor/store', [DoctorController::class, 'store'])->name('doctor.store');
Route::get('/doctor-poster', [DoctorController::class, 'index'])->name('doctorposter.index');
// Route::post('/doctor-poster/download', [DoctorController::class, 'download'])->name('doctorposter.download');
// routes/web.php
Route::post('/doctor/banner/download', [DoctorController::class, 'download'])
    ->name('doctor.banner.download');
Route::post('/doctor/banner/preview1', [DoctorController::class, 'preview1'])->name('doctor.banner.preview1');

Route::post('/doctor/banner', [DoctorController::class, 'generate'])->name('doctor.banner.generate');
// Route::post('/doctor/banner/preview', [DoctorController::class, 'preview'])->name('doctor.banner.preview');
Route::post('/doctor/banner/preview', [DoctorController::class, 'preview'])->name('doctor.banner.preview');

Route::get('/get-doctor-name', [DoctorController::class, 'getDoctorName'])->name('doctor.get_name');


 });

Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');


// Admin Dashboard
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

// // Employee CRUD
Route::get('/admin/employees', [AdminController::class, 'listEmployees'])->name('admin.employees');
Route::get('/admin/banner', [AdminController::class, 'listbanner'])->name('admin.banner');
Route::get('/admin/doctors', [AdminController::class, 'listdoctors'])->name('admin.doctors');
Route::get('/admin/all-employees', [AdminController::class, 'getAllEmployees']);
Route::get('/admin/all-doctors', [AdminController::class, 'getAllDoctors']);
Route::get('/admin/all-banners', [AdminController::class, 'getAllBanners']);




Route::get('/admin/employees/create', [AdminController::class, 'createEmployee']);
Route::post('/admin/employees/store', [AdminController::class, 'storeEmployee']);
Route::get('/admin/employees/delete/{id}', [AdminController::class, 'deleteEmployee']);
Route::get('/admin/export-logins', [AdminController::class, 'exportLogins'])->name('admin.export');
