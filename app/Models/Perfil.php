<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Perfil extends Model
{
    use HasFactory;
    protected $table = 'perfis';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'utilizador_id',
        'nome',
        'avatar_url',
        'estado',
    ];

    public function user()
    {
        // Este é o inverso. "Um Perfil pertence a um (belongsTo) User. 
        // A coluna que nos liga é a utilizador_id
        return $this->belongsTo(User::class, 'utilizador_id');
    }
}
