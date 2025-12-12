<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroAlimento extends Model
{
    use HasFactory;

    protected $table = 'registros_alimento';

    protected $fillable = [
        'user_id',
        'tipo_comida',
        'detalles',
        'fecha_registro',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
