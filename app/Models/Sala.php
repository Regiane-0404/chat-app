<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sala extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'descricao',
        'avatar_url',
        'tipo',
        'criado_por_utilizador_id',
    ];


    protected $appends = ['avatar_url'];

    /**
     * Gera a URL para o avatar da sala.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_path) {
            return Storage::disk('public')->url($this->avatar_path);
        }

        // Gera um avatar padrão com a primeira letra do nome
        return 'https://ui-avatars.com/api/?name=' . urlencode(substr($this->nome, 0, 1)) . '&color=7F9CF5&background=EBF4FF';
    }
    public function criador()
    {
        return $this->belongsTo(User::class, 'criado_por_utilizador_id');
    }

    /**
     * Os utilizadores que são membros desta sala.
     */
    public function utilizadores()
    {
        return $this->belongsToMany(User::class, 'sala_utilizadores')
            ->withPivot('role_na_sala');
    }

    /**
     * As mensagens que pertencem a esta sala.
     */
    public function mensagens()
    {
        return $this->hasMany(Mensagem::class, 'sala_id');
    }
}
