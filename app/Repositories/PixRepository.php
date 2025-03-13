<?php

namespace App\Repositories;

use App\Models\Pix;

class PixRepository
{
    public function createPixPayment($request)
    {
        return Pix::create($request->all());
    }
}