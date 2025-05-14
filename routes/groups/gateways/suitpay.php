<?php


use App\Http\Controllers\HoksController;
use Illuminate\Support\Facades\Route;


Route::prefix('suitpay')
    ->group(function ()
    {
        Route::post('callback', [HoksController::class, 'callbackMethod']);
        Route::post('payment', [HoksController::class, 'callbackMethodPayment']);
        Route::get('withdrawal/{id}', [HoksController::class, 'withdrawalFromModal'])->name('suitpay.withdrawal');
        Route::get('cancelwithdrawal/{id}', [HoksController::class, 'cancelWithdrawalFromModal'])->name('suitpay.cancelwithdrawal');
    });

