<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Table(
    'verify_errors', 
    key: 'id'
)]
#[Fillable([
    'data',
    'type_id',
])]
class VerifyError extends Model
{
    /** @use HasFactory<\Database\Factories\CheckFactory> */
    use HasFactory;
}
