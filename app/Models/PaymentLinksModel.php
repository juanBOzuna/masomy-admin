<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentLinksModel extends Model
{
    const POR_DEFINIR = "Por definir";
    const ABANDONADA = "Abandonada";
    const PAGO_PENDIENTE = "Pago pendiente";
    const RECHAZADA = "Rechazada";
    const NO_PAGADO = "No pagado";
    const CANCELADA = "Cancelada";
    const FALLIDA = "Fallida";
    const PAGADO = "Pagado";
    const ACEPTADA = "Aceptada";
    const APROBADA = "Aprobada";
    const MULTIPLES_INTENTOS = "Multiples intentos";
    const OTRO = "OTRO";

    use HasFactory;
    protected $table = 'payment_links';
    protected $fillable = [
        'reference',
        'link',
        'status',
        'have_order',
        'user_id'
    ];

}
