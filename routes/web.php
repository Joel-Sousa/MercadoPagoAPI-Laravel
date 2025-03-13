<?php

use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\PixController;
use App\Models\CreditCard;
use App\Models\Pix;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');


Route::get('/credit-card', function () {
    $creditCard = CreditCard::get();
    return view('credit_card', compact('creditCard'));
})->name('credit-card');

Route::get('/pix', function () {
    $pix = Pix::get();
    return view('pix', compact('pix'));
})->name('pix');

Route::post('/credit-card', [CreditCardController::class, 'createCreditCardPayment']);
Route::post('/pix', [PixController::class, 'createPixPayment']);
