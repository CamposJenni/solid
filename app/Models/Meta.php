<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    use HasFactory;

    protected $table = 'metas';

    protected $fillable = [
        'user_id',
        'tipo_habito',
        'objetivo',
        'unidad',
        'fecha_inicio',
        'fecha_fin',
        'completada',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
