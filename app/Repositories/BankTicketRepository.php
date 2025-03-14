<?php

namespace App\Repositories;

use App\Models\BankTicket;

class BankTicketRepository
{
    public function createBankTicketPayment($request)
    {
        return BankTicket::create($request->all());
    }
}