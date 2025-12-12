<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recomendacion extends Model
{
    use HasFactory;

    protected $table = 'recomendaciones';

    protected $fillable = [
        'user_id',
        'titulo',
        'descripcion',
        'categoria_habito',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
