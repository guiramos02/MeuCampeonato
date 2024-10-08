<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jogo extends Model
{
    protected $table = "jogo";

    protected $fillable = [
        'id',
        'time_casa',
        'time_fora',
        'gols_time_casa',
        'gols_time_fora',
        'fase',
        'data_inicio_campeonato',
    ];

    public function timeCasa()
    {
        return $this->belongsTo(Time::class, 'time_casa');
    }

    public function timeFora()
    {
        return $this->belongsTo(Time::class, 'time_fora');
    }

    use HasFactory;
}
