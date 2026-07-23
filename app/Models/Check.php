<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Table(
    'checks', 
    key: 'id'
)]
#[Fillable([
    'descricao',
    'tipo_id',
    'consulta_sql',
    'ativo',
])]
class Check extends Model
{
    /** @use HasFactory<\Database\Factories\CheckFactory> */
    use HasFactory;
}
