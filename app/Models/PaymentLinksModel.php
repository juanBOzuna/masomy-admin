<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentLinksModel extends Model
{
    const PENDING_STATUS = 'PENDIENTE';
    const SUCCESS_STATUS = 'APROBADA';
    const FAIL_STATUS = 'RECHAZADA';

    use HasFactory;
    protected $table = 'payment_links';
    protected $fillable = [
        'reference',
        'link',
        'user_id'
    ];

}
