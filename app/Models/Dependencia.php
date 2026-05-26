<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependencia extends Model
{
    use HasFactory;

    protected $connection = 'mysql_sys';
    protected $table = 'dependencias_v2';

    protected $fillable = [
        'dependencia_id',
        'descricao'
    ];
}
