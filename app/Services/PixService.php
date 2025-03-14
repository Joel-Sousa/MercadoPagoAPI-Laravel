<?php

namespace App\Services;

use App\Jobs\SendMailJob;
use App\Repositories\PixRepository;
use App\Traits\UtilTrait;
use Exception;
use Illuminate\Support\Facades\Mail;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class PixService
{
    use UtilTrait;
    
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

            $this->validatePaymentResult($payment);

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
                    'data_created' => $this->dateFormat($payment->date_created),
                    'data_expiration' => $this->dateFormat($payment->date_of_expiration),
                    'qr_code' => $payment->point_of_interaction->transaction_data->qr_code_base64,
                    'pix_copy_paste' => $payment->point_of_interaction->transaction_data->qr_code,
                    'ticket_url' => $payment->point_of_interaction->transaction_data->ticket_url,
                );

                $data = [
                    'view' => 'mail.pix_payment',
                    'name' => $request["payerFirstName"],
                    'description' => $request['description'],
                    'email' => $request['email'],
                    'subject' => 'Pagamento Pix',
                    'message' => 'Obrigado pela compra',
                    'type_document' => $request["identificationType"],
                    'number_document' => $request["identificationNumber"],
                    'status' => $payment->status,
                    'status_detail' => $payment->status_detail,
                    'value' => $request["transactionAmount"],
                    'data_created' => $this->dateFormat($payment->date_created),
                    'data_expiration' => $this->dateFormat($payment->date_of_expiration),
                    'qr_code' => $payment->point_of_interaction->transaction_data->qr_code_base64,
                    'pix_copy_paste' => $payment->point_of_interaction->transaction_data->qr_code,
                    'ticket_url' => $payment->point_of_interaction->transaction_data->ticket_url,
                ];;
    
                SendMailJob::dispatch($data);

                return $response_fields;

        } catch (Exception $e) {
            $response_fields = array('error_message' => $e->getMessage());

            $response_body = json_encode($response_fields);

            return response($response_body, 400);
        }
    }
    
}