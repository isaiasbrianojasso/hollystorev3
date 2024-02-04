<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'tipo',
        'api',
        'precio',
        'descripcion',
        'estatus',
        'imagen',

        // Otras columnas permitidas para asignación masiva, si las hay
    ];
}
