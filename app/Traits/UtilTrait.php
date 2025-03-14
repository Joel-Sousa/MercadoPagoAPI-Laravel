<?php

namespace App\Traits;

use DateTime;
use DateTimeZone;
use Exception;

trait UtilTrait
{
    public function validatePaymentResult($payment)
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

    public function dataFormat($date)
    {
        $data = new DateTime($date, new DateTimeZone('America/Sao_Paulo'));
        $data->setTimezone(new DateTimeZone('Etc/GMT+3'));
        return $data->format('d/m/Y H:i:s');
    }
}
