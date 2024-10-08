<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    protected $table = "times";

    protected $fillable = [
        'id', 'nome', 'quantidade_pontos', 'data_inscricao'
    ];

    public function getResults($nome = null, $id = 0)
    {
        $query = $this;
        if ($nome) {
            $query = $query->where('nome', 'LIKE', "%{$nome}%");
        }

        if ($id) {
            $query = $query->where('id', $id);
        }

        return $query->get();
    }

    use HasFactory;
}
