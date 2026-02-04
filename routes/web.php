<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SneezeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/leaderboard-data', [HomeController::class, 'getLeaderboardData'])->name('leaderboard.data');

// Language switcher
Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'nl'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->middleware('web')->name('language.switch');

// Public leaderboard (no auth required)
Route::get('/leaderboard', [SneezeController::class, 'leaderboard'])->name('leaderboard');

// Daily details page (available to both guests and authenticated users)
Route::get('/daily-details/{date}', [SneezeController::class, 'dailyDetails'])->name('daily.details');

// Monthly details page (available to both guests and authenticated users)
Route::get('/monthly-details/{month}', [SneezeController::class, 'monthlyDetails'])->name('monthly.details');

// Terms and conditions page
Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/dashboard', [SneezeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Quick sneeze entry page (for phone shortcuts)
    Route::get('/quick-sneeze', function () {
        return view('quick-sneeze');
    })->name('quick.sneeze');
    
    // Help page
    Route::get('/help', function () {
        return view('help');
    })->name('help');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Sneeze routes
    Route::post('/sneezes', [SneezeController::class, 'store'])->name('sneezes.store');
    Route::put('/sneezes/{sneeze}', [SneezeController::class, 'update'])->name('sneezes.update');
    Route::delete('/sneezes/{sneeze}', [SneezeController::class, 'destroy'])->name('sneezes.destroy');
    Route::get('/sneezes/export', [SneezeController::class, 'export'])->name('sneezes.export');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/sneezes', [AdminController::class, 'sneezes'])->name('admin.sneezes');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.editUser');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.updateUser');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('admin.toggleAdmin');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
    Route::post('/switch-database', [AdminController::class, 'switchDatabase'])->name('admin.switchDatabase');
});

require __DIR__.'/auth.php';
