<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Table(
    'clients', 
    key: 'id'
)]
#[Fillable([
    'host',
    'port',
    'user',
    'password',
    'db_name',
    'driver',
    'active',
])]
class Client extends Model
{
    /** @use HasFactory<\Database\Factories\CheckFactory> */
    use HasFactory;
}
