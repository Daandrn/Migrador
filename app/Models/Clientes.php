<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Table(
    'clientes', 
    key: 'id'
)]
#[Fillable([
    'host',
    'porta',
    'usuario',
    'senha',
    'nome_banco',
    'drive',
    'ativo',
])]
class Clientes extends Model
{
    /** @use HasFactory<\Database\Factories\CheckFactory> */
    use HasFactory;
}
