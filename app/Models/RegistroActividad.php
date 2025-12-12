<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroActividad extends Model
{
    use HasFactory;

    protected $table = 'registros_actividad';

    protected $fillable = [
        'user_id',
        'tipo_actividad',
        'duracion_minutos',
        'intensidad',
        'fecha_registro',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
