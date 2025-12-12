<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroAgua extends Model
{
    use HasFactory;

    protected $table = 'registros_agua';

    protected $fillable = [
        'user_id',
        'cantidad',
        'unidad',
        'fecha_registro',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}