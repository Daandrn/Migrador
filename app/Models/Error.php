<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Table(
    'errors', 
    key: 'id'
)]
#[Fillable([
    'data',
    'tipo_id',
])]
class Error extends Model
{
    /** @use HasFactory<\Database\Factories\CheckFactory> */
    use HasFactory;
}
