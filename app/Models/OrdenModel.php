<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenModel extends Model
{
    use HasFactory;

    const PENDIENTE = "Pendiente";
    const CANCELADO = "Cancelado";
    const EN_PROCESO = "En Proceso";
    const EN_ENVIO = "En Envio";
    const ENTREGADO = "Entregado";

    protected $table = 'orden';

    protected $fillable = [
        'total_price',
        'total_disccount',
        'payment_link_id',
        'status'
    ];
}
