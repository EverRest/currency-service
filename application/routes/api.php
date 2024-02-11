<?php

use App\Http\Controllers\Api\BankBranchController;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\ExchangeRateController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\UserController;
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
    Route::middleware('auth:sanctum')->post('/logout', LogoutController::class)->name('logout');
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
        Route::get('/', [ExchangeRateController::class, 'currentRates'])->name('current-currency-rates');
        Route::get('/avg', [ExchangeRateController::class, 'avgRates'])->name('avg-rates');
        Route::get('/statistic', [ExchangeRateController::class, 'statistic'])->name('statistic');
    });
    Route::group(['prefix' => 'subscriptions', 'name' => 'subscriptions'], function () {
        Route::get('/', [SubscriptionController::class, 'list'])->name('list');
        Route::post('/', [SubscriptionController::class, 'store'])->name('store');
        Route::put('/{subscription}', [SubscriptionController::class, 'update'])->name('store');
        Route::delete('/{subscription}', [SubscriptionController::class, 'destroy'])->name('destroy');
    });
});
