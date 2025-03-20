<?php
namespace App\Exceptions;

use Exception;

class CreditCardException extends Exception
{
    public function render()
    {
        return response()->json(['error' => $this->getMessage()], 400);
    }
}
