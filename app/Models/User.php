<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- Nuevas relaciones ---
    public function registrosAgua()
    {
        return $this->hasMany(RegistroAgua::class);
    }

    public function registrosSueno()
    {
        return $this->hasMany(RegistroSueno::class);
    }

    public function registrosActividad()
    {
        return $this->hasMany(RegistroActividad::class);
    }

    public function registrosAlimento()
    {
        return $this->hasMany(RegistroAlimento::class);
    }

    public function metas()
    {
        return $this->hasMany(Meta::class);
    }

    public function recomendaciones()
    {
        return $this->hasMany(Recomendacion::class);
    }
}
