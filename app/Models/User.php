<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token', 'two_factor_recovery_codes', 'two_factor_secret'];
    protected $appends = ['profile_photo_url'];
    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }

    public function perfil()
    {
        return $this->hasOne(Perfil::class, 'utilizador_id');
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }
    public function salasCriadas()
    {
        return $this->hasMany(Sala::class, 'criado_por_utilizador_id');
    }
    public function salas()
    {
        return $this->belongsToMany(Sala::class, 'sala_utilizadores')->withPivot('role_na_sala');
    }
    public function mensagensEnviadas()
    {
        return $this->hasMany(Mensagem::class, 'remetente_id');
    }
    public function initials(): string
    {
        return collect(explode(' ', $this->name))->map(fn(string $segment) => mb_substr($segment, 0, 1))->join('');
    }

    public function isAdmin(): bool
    {
        // Agora, a permissão é baseada no campo da base de dados.
        // O "!!" converte o 1/0 para um verdadeiro true/false.
        return !!$this->is_admin;
    }
}
