<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pix extends Model
{
    use HasFactory;

    protected $table = 'pixes';

    protected $fillable = [
        'id_payment',
        '_token',
        'payer_name',
        'email',
        'identification_type',
        'identification_number',
        'status',
        'description',
        'status',
        'status_detail',
        'transaction_amount',
    ];
}
