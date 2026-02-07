<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Igreja extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; // Global connection
    protected $table = 'igrejas_global';

    protected $fillable = [
        'local_id',
        'codigo_ccb',
        'nome',
        'cidade',
        'bairro',
        'setor'
    ];

    public function local()
    {
        return $this->belongsTo(Local::class);
    }
}
