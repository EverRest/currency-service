<?php

use App\Http\Controllers\BankBranchController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\CurrencyRateController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::name('auth.')->group(function () {
    Route::post('/register', RegisterController::class)->name('register');
    Route::post('/login', LoginController::class)->name('login');
    Route::post('/logout', LogoutController::class)->middleware('auth:sanctum')->name('logout');
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    })->name('user');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'users', 'name' => 'users'], function () {
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::patch('/{user}', [UserController::class, 'toggleCriticalRateChangeAlert'])
            ->name('toggle-critical-rate-change-alert');
    });
    Route::group(['prefix' => 'banks', 'name' => 'banks'], function () {
        Route::get('/', [BankController::class, 'list'])->name('list');
        Route::get('/{bank}', [BankController::class, 'item'])->name('item');
    });
    Route::group(['prefix' => 'currencies', 'name' => 'currencies'], function () {
        Route::get('/', [CurrencyController::class, 'list'])->name('list');
    });
    Route::group(['prefix' => 'bank-branches', 'name' => 'bank-branches'], function () {
        Route::get('/', [BankBranchController::class, 'list'])->name('list');
        Route::get('/closest', [BankBranchController::class, 'getClosestBanks'])->name('closest-bank-branches');
    });
    Route::group(['prefix' => 'currency-rates', 'name' => 'currency-rates'], function () {
        Route::get('/', [CurrencyRateController::class, 'currentRates'])->name('current-currency-rates');
        Route::get('/avg', [CurrencyRateController::class, 'avgRates'])->name('avg-rates');
        Route::get('/statistic', [CurrencyRateController::class, 'statistic'])->name('statistic');
    });
    Route::group(['prefix' => 'subscriptions', 'name' => 'subscriptions'], function () {
        Route::get('/', [SubscriptionController::class, 'list'])->name('list');
        Route::post('/', [SubscriptionController::class, 'store'])->name('store');
        Route::put('/{subscription}', [SubscriptionController::class, 'update'])->name('store');
        Route::delete('/{subscription}', [SubscriptionController::class, 'destroy'])->name('destroy');
    });
});
