<?php

namespace App\Services;

use App\Repositories\PixRepository;
use DateTime;
use DateTimeZone;
use Exception;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class PixService
{
    protected $pixRepository;

    public function __construct(PixRepository $pixRepository)
    {
        $this->pixRepository = $pixRepository;
    }

    public function createPixPayment($request)
    {
        MercadoPagoConfig::setAccessToken(env("MERCADO_PAGO_ACCESS_TOKEN"));

        try {
            $uniq = uniqid("", true);

            $client = new PaymentClient();
            $request_options = new RequestOptions();
            $request_options->setCustomHeaders(["X-Idempotency-Key: " . $uniq]);

            $payment = $client->create([
                "transaction_amount" => (float) $request['transactionAmount'],
                "payment_method_id" => 'pix',
                "payer" => [
                    "email" => $request['email']
                ]
            ], $request_options);

            self::validatePaymentResult($payment);

            $this->pixRepository->createPixPayment(
                $request->merge([
                    'id_payment' => $payment->id,
                    'status' => $payment->status,
                    'status_detail' => $payment->status_detail,
                    'payer_name' => $request['payerFirstName'],
                    'identification_type' => $request['identificationType'],
                    'identification_number' => $request['identificationNumber'],
                    'transaction_amount' => $request['transactionAmount'],
                ]));

                $response_fields = array(
                    'status' => $payment->status,
                    'detail' => $payment->status_detail,
                    'value' => $request['transactionAmount'],
                    'data_created' => self::dataFormat($payment->date_created),
                    'data_expiration' => self::dataFormat($payment->date_of_expiration),
                    'qr_code' => $payment->point_of_interaction->transaction_data->qr_code_base64,
                    'pix_copy_paste' => $payment->point_of_interaction->transaction_data->qr_code,
                    'ticket_url' => $payment->point_of_interaction->transaction_data->ticket_url,
                );

                $resp = $response_fields;

                return response(view('pix_pay', compact('resp')), 201);

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
    
    private function dataFormat($date)
    {
        $data = new DateTime($date, new DateTimeZone('America/Sao_Paulo'));
        $data->setTimezone(new DateTimeZone('Etc/GMT+3'));
        return $data->format('d/m/Y H:i:s');
    }
}