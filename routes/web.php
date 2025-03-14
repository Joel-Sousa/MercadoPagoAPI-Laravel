<?php

use App\Http\Controllers\BankTicketController;
use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\PixController;
use App\Models\BankTicket;
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

Route::get('/bank-ticket', function () {
    $bankTicket = BankTicket::get();
    return view('bank_ticket', compact('bankTicket'));
})->name('bank-ticket');

Route::get('/status-payment', [MercadoPagoController::class, 'statusPayment']);

Route::post('/credit-card', [CreditCardController::class, 'createCreditCardPayment']);
Route::post('/pix', [PixController::class, 'createPixPayment']);
Route::post('/bank-ticket', [BankTicketController::class, 'createBankTicketPayment']);

