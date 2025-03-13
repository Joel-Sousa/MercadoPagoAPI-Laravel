<?php

namespace App\Repositories;

use App\Models\CreditCard;

class CreditCardRepository
{
    public function createCreditCardPayment($request)
    {
        return CreditCard::create($request->all());
    }

}