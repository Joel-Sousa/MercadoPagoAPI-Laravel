<?php

namespace App\Repositories;

use App\Models\CreditCard;

class MercadoPagoRepository
{
    public function createCreditCardPayment($request)
    {
        return CreditCard::create($request->all());
    }
}