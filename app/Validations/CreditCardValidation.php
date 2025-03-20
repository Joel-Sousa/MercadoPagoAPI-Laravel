<?php

namespace App\Validations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreditCardValidation
{

    public static function validate(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'description' => 'required',
                'transaction_amount' => 'required',
            ],
            [
                'description.required' => 'O campo de descricao e obrigatorio!',
                'transaction_amount.required' => 'O campo de valor e obrigatorio!',
            ]
        );

        if ($validation->fails()) {
            
            $error = self::validateFields($validation->errors()->toArray());

            return (object) ['erro' => true, 'data' => $error];
        } else {
            return (object) ['erro' => false];
        }
    }

    private static function validateFields($data){

        $error = [];

        foreach ($data as $i => $e) {
            $err = (object) [];
            $err->label = $i;
            $err->erro = $e[0];

            array_push($error, $err);
        }

        return $error;
}
}
