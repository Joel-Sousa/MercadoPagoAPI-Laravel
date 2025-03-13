<?php

namespace App\Repositories;

use App\Models\CreditCard;
use App\Models\Pix;

class MercadoPagoRepository
{
    public function createCreditCardPayment($request)
    {
        return CreditCard::create($request->all());
    }

    public function createPixPayment($request)
    {
        return Pix::create($request->all());
    }
}