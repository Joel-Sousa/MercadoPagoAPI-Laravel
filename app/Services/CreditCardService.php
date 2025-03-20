<?php

namespace App\Services;

use App\Jobs\SendMailJob;
use App\Repositories\CreditCardRepository;
use App\Traits\UtilTrait;
use App\Validations\CreditCardValidation;
use Exception;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class CreditCardService
{
    use UtilTrait;

    protected $creditCardRepository;

    public function __construct(CreditCardRepository $creditCardRepository)
    {
        $this->creditCardRepository = $creditCardRepository;
    }

    public function createCreditCardPayment($request)
    {
        MercadoPagoConfig::setAccessToken(env("MERCADO_PAGO_ACCESS_TOKEN"));

        try {

            $error = CreditCardValidation::validate($request);

            if($error->erro) return response(json_encode(array('error_message' => $error)));
            // throw_if($error->erro, new \App\Exceptions\CreditCardException('Campos necessarios devem ser preenchidos'), 406);

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

            $this->validatePaymentResult($payment);

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

            $data = [
                'view' => 'mail.credit_card_payment',
                'name' => 'Nome completo',
                'description' => $request['description'],
                'email' => $request['payer']['email'],
                'subject' => 'Pagamento cartao de credito',
                'message' => 'Obrigado pela compra',
                'value' => $request['transaction_amount'],
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,

            ];

            SendMailJob::dispatch($data);

            $response_fields = array(
                'status' => $payment->status,
                'status_detail' => $payment->status_detail
            );

            return $response_fields;
        } catch (Exception $e) {
            $response_fields = array('error_message' => $e->getMessage());

            $response_body = json_encode($response_fields);

            return response($response_body, 400);
        }
    }
}