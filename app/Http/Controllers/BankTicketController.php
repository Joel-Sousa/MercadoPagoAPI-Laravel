<?php

namespace App\Http\Controllers;

use App\Models\BankTicket;
use App\Services\BankTicketService;
use Illuminate\Http\Request;

class BankTicketController extends Controller
{

    protected $bankTicketService;
    
    public function __construct(BankTicketService $bankTicketService)
    {
        $this->bankTicketService = $bankTicketService;
    }

    public function createBankTicketPayment(Request $request)
    {
         $this->bankTicketService->createBankTicketPayment($request);
    }
    
}
