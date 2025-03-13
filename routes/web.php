<?php

use App\Http\Controllers\MercadoPagoController;
use App\Models\CreditCard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');


Route::get('/credit-card', function () {
    $creditCard = CreditCard::get();
    return view('credit_card', compact('creditCard'));
})->name('credit-card');


Route::post('/credit-card', [MercadoPagoController::class, 'createCreditCardPayment']);