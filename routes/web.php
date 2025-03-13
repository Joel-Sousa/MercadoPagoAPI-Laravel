<?php

use App\Http\Controllers\MercadoPagoController;
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

Route::post('/credit-card', [MercadoPagoController::class, 'createCreditCardPayment']);
Route::post('/pix', [MercadoPagoController::class, 'createPixPayment']);
