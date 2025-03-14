<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_payment',
        '_token',
        'description',
        'name',
        'email',
        'identification_type',
        'identification_number',
        'zip_code',
        'street',
        'street_number',
        'neighborhood',
        'city',
        'state',
        'status',
        'status_detail',
        'transaction_amount',
    ];
}
