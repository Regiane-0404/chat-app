<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Perfil;
use App\Models\Role;
use App\Models\Sala;
use App\Models\Mensagem;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
    public function perfil()
    {
        //Análise: Estamos a dizer: "Um User tem um (hasOne) Perfil. A ligação entre nós é feita através da coluna utilizador_id na tabela perfis."
        return $this->hasOne(Perfil::class, 'utilizador_id');
    }

    public function roles()
    {  //Um User pertence a muitos (belongsToMany) Roles. A nossa ligação é feita através da tabela pivot role_user
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * As salas que o utilizador criou.
     */
    public function salasCriadas()
    {
        return $this->hasMany(Sala::class, 'criado_por_utilizador_id');
    }

    /**
     * As salas a que o utilizador pertence.
     */
    public function salas()
    {
        return $this->belongsToMany(Sala::class, 'sala_utilizadores')
            ->withPivot('role_na_sala')
            ->withTimestamps();
    }

    /**
     * As mensagens que o utilizador enviou.
     */
    public function mensagensEnviadas()
    {
        return $this->hasMany(Mensagem::class, 'remetente_id');
    }
}
