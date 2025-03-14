<?php

namespace App\Http\Controllers;

use App\Services\PixService;
use Illuminate\Http\Request;

class PixController extends Controller
{
    protected $pixService;

    public function __construct(PixService $pixService)
    {
        $this->pixService = $pixService;
    }

    public function createPixPayment(Request $request)
    {
        $resp = $this->pixService->createPixPayment($request);
        return response(view('pix_pay', compact('resp')), 201);
    }
}
