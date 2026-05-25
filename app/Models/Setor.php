<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setor extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'setores';

    protected $fillable = [
        'nome',
        'active'
    ];
}
