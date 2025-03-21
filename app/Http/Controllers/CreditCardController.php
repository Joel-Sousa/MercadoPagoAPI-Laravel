<?php

namespace App\Http\Controllers;

use App\Services\CreditCardService;
use Illuminate\Http\Request;

class CreditCardController extends Controller
{
    protected $creditCardService;

    public function __construct(CreditCardService $creditCardService)
    {
        $this->creditCardService = $creditCardService;
    }

    public function createCreditCardPayment(Request $request)
    {
        $resp = $this->creditCardService->createCreditCardPayment($request);
        return response(json_encode($resp), 201);
    }
}
