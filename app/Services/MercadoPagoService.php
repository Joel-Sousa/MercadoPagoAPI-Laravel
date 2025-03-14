<?php

namespace App\Services;

use DateTime;
use DateTimeZone;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoService
{
    public function statusPayment($request)
    {
        MercadoPagoConfig::setAccessToken(env("MERCADO_PAGO_ACCESS_TOKEN"));

        try {
            $payment_id = $request['idPayment'];

            $client = new PaymentClient();
            $paymentStatus = $client->get($payment_id);

            $resp = [
                'id' => $paymentStatus->id,
                'status' => $paymentStatus->status,
                'detail' => $paymentStatus->status_detail,
                'data_approved' => self::dataFormat($paymentStatus->date_approved),
                'transaction_amount' => $paymentStatus->transaction_amount,
                'method_payment' => $paymentStatus->payment_method_id,
                'qr_code' => $paymentStatus->point_of_interaction->transaction_data->qr_code_base64 ?? '',
                'pix_copy_paste' => $paymentStatus->point_of_interaction->transaction_data->qr_code ?? '',
                'ticket_url' => $paymentStatus->point_of_interaction->transaction_data->ticket_url ?? '',
                'external_resource_url' => $paymentStatus->transaction_details->external_resource_url ?? ''
            ];

            return response(view('welcome', compact('resp')), 200);
        } catch (\Exception $e) {
            $response_fields = array('error_message' => $e->getMessage());

            return response(view('welcome', compact('response_fields')), 400);
        }
    }

    private static function dataFormat($date)
    {
        $data = new DateTime($date, new DateTimeZone('America/Sao_Paulo'));
        $data->setTimezone(new DateTimeZone('Etc/GMT+3'));
        return $data->format('d/m/Y H:i:s');
    }
}