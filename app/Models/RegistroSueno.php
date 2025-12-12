<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroSueno extends Model
{
    use HasFactory;

    protected $table = 'registros_sueno';

    protected $fillable = [
        'user_id',
        'hora_inicio',
        'hora_fin',
        'duracion_minutos',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
