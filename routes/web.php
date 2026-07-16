<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ContactSubmissionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\TemplateController as AdminTemplateController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Front\TagController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['es', 'en'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('locale.switch');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/tag/{slug}', [TagController::class, 'show'])->name('tag.show');

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('posts', AdminPostController::class)->except(['show']);
        Route::resource('pages', AdminPageController::class)->except(['show']);
        Route::resource('categories', AdminCategoryController::class)->except(['show']);
        Route::resource('tags', AdminTagController::class)->except(['show']);
        Route::resource('media', AdminMediaController::class)->except(['show', 'update', 'edit']);
        Route::get('media/json', [AdminMediaController::class, 'jsonList'])->name('media.json');
        Route::post('media/upload-ajax', [AdminMediaController::class, 'uploadAjax'])->name('media.upload-ajax');
        Route::resource('templates', AdminTemplateController::class)->except(['show']);
        Route::resource('users', AdminUserController::class)->except(['show']);
        Route::resource('menus', AdminMenuController::class)->except(['show']);

        Route::post('menus/{menu}/items', [AdminMenuController::class, 'addItem'])->name('menus.items.add');
        Route::put('menus/{menu}/items/{item}', [AdminMenuController::class, 'updateItem'])->name('menus.items.update');
        Route::delete('menus/{menu}/items/{item}', [AdminMenuController::class, 'removeItem'])->name('menus.items.destroy');

        Route::get('contact-submissions', [ContactSubmissionController::class, 'index'])->name('contact-submissions.index');
        Route::get('contact-submissions/{contact_submission}', [ContactSubmissionController::class, 'show'])->name('contact-submissions.show');
        Route::delete('contact-submissions/{contact_submission}', [ContactSubmissionController::class, 'destroy'])->name('contact-submissions.destroy');

        Route::get('link-picker/data', [AdminPostController::class, 'linkPickerData'])->name('link-picker.data');

        Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
        Route::get('backups/export', [BackupController::class, 'export'])->name('backups.export');
        Route::post('backups/import', [BackupController::class, 'import'])->name('backups.import');

        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });

Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/contacto', [ContactController::class, 'send'])->name('contact.send');

require __DIR__ . '/auth.php';

Route::get('/{slug}', [HomeController::class, 'show'])->name('post.show');
