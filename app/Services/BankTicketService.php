<?php

namespace App\Services;

use App\Repositories\BankTicketRepository;
use App\Traits\UtilTrait;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class BankTicketService
{
    use UtilTrait;

    protected $bankTicketRepository;
    
    public function __construct(BankTicketRepository $bankTicketRepository)
    {
        $this->bankTicketRepository = $bankTicketRepository;
    }

    public function createBankTicketPayment($request)
    {
        MercadoPagoConfig::setAccessToken(env("MERCADO_PAGO_ACCESS_TOKEN"));

        try {

            $uniq = uniqid("", true);

            $client = new PaymentClient();
            $request_options = new RequestOptions();
            $request_options->setCustomHeaders(["X-Idempotency-Key: " . $uniq]);

            $payment = $client->create([
                "transaction_amount" => (float) $request['transactionAmount'],
                "payment_method_id" => 'bolbradesco',
                "payer" => [
                    "email" => $request['email'],
                    "first_name" => $request['payerFirstName'],
                    "last_name" => $request['payerLastName'],
                    "identification" => [
                        "type" =>  $request['identificationType'],
                        "number" => $request['identificationNumber']
                    ],
                    "address" => [
                        "zip_code" => $request['zipCode'],
                        "city" => $request['city'],
                        "street_name" => $request['streetName'],
                        "street_number" => $request['streetNumber'],
                        "neighborhood" => $request['neighborhood'],
                        "federal_unit" => $request['federalUnit']
                    ]
                ]
            ], $request_options);

            $this->validatePaymentResult($payment);

            $this->bankTicketRepository->createBankTicketPayment(
                $request->merge([
                    'id_payment' => $payment->id,
                    'name' => $request['payerFirstName'] . $request['payerLastName'],
                    'identification_type' => $request['identificationType'],
                    'identification_number' => $request['identificationNumber'],
                    'transaction_amount' => $request['transactionAmount'],
                    'status' => $payment->status,
                    'status_detail' => $payment->status_detail,
                    'zip_code' => $request['zipCode'],
                    'street' => $request['streetName'],
                    'street_number' => $request['streetNumber'],
                    'neighborhood' => $request['neighborhood'],
                    'city' => $request['city'],
                    'state' => $request['federalUnit']
                ]));

            $resp = array(
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'external_resource_url' => $payment->transaction_details->external_resource_url
            );

            return $resp;
            
        } catch (\Exception $e) {
            $resp = array('error_message' => $e->getMessage());
            return response(view('bank_ticket', compact('resp')), 401);
        }
    }
}