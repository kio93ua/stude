<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\StudentDashboardController;
use App\Http\Controllers\Dashboard\StudentHomeworkController;
use App\Http\Controllers\Dashboard\StudentMaterialController;
use App\Http\Controllers\Dashboard\StudentTestController;
use App\Http\Controllers\Dashboard\TeacherDashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index'])
        ->middleware('panel.role:admin')
        ->name('admin');
    Route::get('/teacher', [TeacherDashboardController::class, 'index'])
        ->middleware('panel.role:teacher')
        ->name('teacher');
    Route::middleware('panel.role:student')->group(function () {
        Route::get('/student', [StudentDashboardController::class, 'index'])->name('student');
        Route::get('/student/tests', [StudentTestController::class, 'index'])->name('student.tests.index');
        Route::get('/student/tests/{test}', [StudentTestController::class, 'show'])->name('student.tests.show');
        Route::post('/student/tests/{test}', [StudentTestController::class, 'submit'])->name('student.tests.submit');
        Route::get('/student/homework', [StudentHomeworkController::class, 'index'])->name('student.homework.index');
        Route::get('/student/homework/{homework}', [StudentHomeworkController::class, 'show'])->name('student.homework.show');
        Route::post('/student/homework/{homework}', [StudentHomeworkController::class, 'submit'])->name('student.homework.submit');
        Route::get('/student/materials', [StudentMaterialController::class, 'index'])->name('student.materials.index');
        Route::get('/student/materials/{lesson}', [StudentMaterialController::class, 'show'])->name('student.materials.show');
        Route::get('/student/vocabulary', [\App\Http\Controllers\Dashboard\StudentVocabularyController::class, 'index'])->name('student.vocabulary.index');
        Route::post('/student/vocabulary', [\App\Http\Controllers\Dashboard\StudentVocabularyController::class, 'store'])->name('student.vocabulary.store');
        Route::put('/student/vocabulary/{entry}', [\App\Http\Controllers\Dashboard\StudentVocabularyController::class, 'update'])->name('student.vocabulary.update');
        Route::delete('/student/vocabulary/{entry}', [\App\Http\Controllers\Dashboard\StudentVocabularyController::class, 'destroy'])->name('student.vocabulary.destroy');
    });
});
