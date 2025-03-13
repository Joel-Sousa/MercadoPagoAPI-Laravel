<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_payment',
        'token',
        'description',
        'type_document',
        'number_document',
        'email',
        'status',
        'status_detail',
        'transaction_amount',
    ];
}
