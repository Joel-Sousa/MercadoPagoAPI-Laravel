<?php

namespace App\Services;

use App\Repositories\CreditCardRepository;
use Exception;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class CreditCardService
{
    protected $creditCardRepository;

    public function __construct(CreditCardRepository $creditCardRepository)
    {
        $this->creditCardRepository = $creditCardRepository;
    }

    public function createCreditCardPayment($request)
    {
        MercadoPagoConfig::setAccessToken(env("MERCADO_PAGO_ACCESS_TOKEN"));

        try {

            $uniq = uniqid("", true);

            $client = new PaymentClient();
            $request_options = new RequestOptions();
            $request_options->setCustomHeaders(["X-Idempotency-Key: " . $uniq]);

            $payment = $client->create(
                [
                    "transaction_amount" => $request['transaction_amount'],
                    "token" => $request['token'],
                    "description" => $request['description'],
                    "installments" => $request['installments'],
                    "payment_method_id" => $request['payment_method_id'],
                    "issuer_id" => $request['issuer_id'],
                    "payer" => [
                        "email" => $request['payer']['email'],
                        "identification" => [
                            "type" => $request['payer']['identification']['type'],
                            "number" => $request['payer']['identification']['number']
                        ]
                    ]
                ],
                $request_options
            );

            self::validatePaymentResult($payment);

            $this->creditCardRepository->createCreditCardPayment(
                $request->merge([
                    'id_payment' => $payment->id,
                    'status' => $payment->status,
                    'status_detail' => $payment->status_detail,
                    'type_document' => $request['payer']['identification']['type'],
                    'number_document' => $request['payer']['identification']['number'],
                    'email' => $request['payer']['email'],
                ])
            );

            $response_fields = array(
                'status' => $payment->status,
                'status_detail' => $payment->status_detail
            );

            return response(json_encode($response_fields), 201);
        } catch (Exception $e) {
            $response_fields = array('error_message' => $e->getMessage());

            $response_body = json_encode($response_fields);

            return response($response_body, 400);
        }
    }

    private static function validatePaymentResult($payment)
    {
        if ($payment->id === null) {
            $error_message = 'Unknown error cause';

            if ($payment->error !== null) {
                $sdk_error_message = $payment->error->message;
                $error_message = $sdk_error_message !== null ? $sdk_error_message : $error_message;
            }

            throw new Exception($error_message);
        }
    }
}