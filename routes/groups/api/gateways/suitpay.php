<?php
use App\Http\Controllers\HoksController;
use Illuminate\Support\Facades\Route;

Route::prefix('suitpay')
    ->group(function ()
    {
        Route::post('qrcode-pix', [HoksController::class, 'getQRCodePix']);
        Route::post('consult-status-transaction', [HoksController::class, 'consultStatusTransactionPix']);
    });


