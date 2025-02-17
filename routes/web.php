<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// admin routes
Route::group(['prefix' => 'admin'], function () {

    Route::group(['middleware' => 'admin.guest'], function () {

        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin/login');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin/authenticate');
    });

    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin/dashboard');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin/logout');

        // category routes
        Route::get('/categories/index', [CategoryController::class, 'index'])->name('categories/index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories/create');
        Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories/store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories/edit');
        Route::put('/categories/{category}/update', [CategoryController::class, 'update'])->name('categories/update');
        Route::delete('/categories/{category}/delete', [CategoryController::class, 'destroy'])->name('categories/delete');

        //category image route
        Route::post('/upload-temp-image', [TempImagesController::class, 'create'])->name('temp-images/create');

        Route::post('/getSlug', function (Request $request) {
            $slug = '';
            if (!empty($request->title)) {
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug
            ]);
        })->name('getSlug');


        // sub category routes
        Route::get('/sub-categories/index', [SubCategoryController::class, 'index'])->name('sub-categories/index');
        Route::get('/sub-categories/create', [SubCategoryController::class, 'create'])->name('sub-categories/create');
        Route::post('/sub-categories/store', [SubCategoryController::class, 'store'])->name('sub-categories/store');
        Route::get('/sub-categories/{subCategory}/edit', [SubCategoryController::class, 'edit'])->name('sub-categories/edit');
        Route::put('/sub-categories/{subCategory}/update', [SubCategoryController::class, 'update'])->name('sub-categories/update');
        Route::get('sub-categories/{subCategory}/delete', [SubCategoryController::class, 'destroy'])->name('sub-categories/delete');

        // routes for brands
        // Route::get('/sub-categories/index', [SubCategoryController::class, 'index'])->name('sub-categories/index');
        Route::get('/brands/create', [BrandController::class, 'create'])->name('brands/create');
        // Route::post('/sub-categories/store', [SubCategoryController::class, 'store'])->name('sub-categories/store');
        // Route::get('/sub-categories/{subCategory}/edit', [SubCategoryController::class, 'edit'])->name('sub-categories/edit');
        // Route::put('/sub-categories/{subCategory}/update', [SubCategoryController::class, 'update'])->name('sub-categories/update');
        // Route::get('sub-categories/{subCategory}/delete', [SubCategoryController::class, 'destroy'])->name('sub-categories/delete');


    });
});

require __DIR__ . '/website.php';
