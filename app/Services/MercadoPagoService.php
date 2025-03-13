<?php

namespace App\Services;

use App\Repositories\MercadoPagoRepository;
use Exception;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoService
{
    protected $mpRepository;

    public function __construct(MercadoPagoRepository $mpRepository)
    {
        $this->mpRepository = $mpRepository;
    }

    public function createCreditCardPayment($request)
    {
        MercadoPagoConfig::setAccessToken(env("MERCADO_PAGO_ACCESS_TOKEN"));
        try {

            $uniq = uniqid("", true);

            $client = new PaymentClient();
            $request_options = new RequestOptions();
            $request_options->setCustomHeaders(["X-Idempotency-Key: " . $uniq]);

            $data = $request->all();

            $payment = $client->create(
                [
                    "transaction_amount" => $data['transaction_amount'],
                    "token" => $data['token'],
                    "description" => $data['description'],
                    "installments" => $data['installments'],
                    "payment_method_id" => $data['payment_method_id'],
                    "issuer_id" => $data['issuer_id'],
                    "payer" => [
                        "email" => $data['payer']['email'],
                        "identification" => [
                            "type" => $data['payer']['identification']['type'],
                            "number" => $data['payer']['identification']['number']
                        ]
                    ]
                ],
                $request_options
            );

            self::validatePaymentResult($payment);

            $this->mpRepository->createCreditCardPayment(
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
