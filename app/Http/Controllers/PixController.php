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
        return $this->pixService->createPixPayment($request);
    }
}
