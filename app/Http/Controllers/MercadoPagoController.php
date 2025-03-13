<?php

namespace App\Http\Controllers;

use App\Services\MercadoPagoService;
use Illuminate\Http\Request;

class MercadoPagoController extends Controller
{
    protected $mpService;

    public function __construct(MercadoPagoService $mpService)
    {
        $this->mpService = $mpService;
    }

    public function createCreditCardPayment(Request $request)
    {
        $this->mpService->createCreditCardPayment($request);
    }

    public function createPixPayment(Request $request)
    {
        $this->mpService->createPixPayment($request);
    }
    
}
