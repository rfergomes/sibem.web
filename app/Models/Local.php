<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $table = 'locais';

    protected $fillable = [
        'regional_id',
        'nome',
        'db_host',
        'db_name',
        'db_user',
        'db_password',
        'active'
    ];

    public function regional()
    {
        return $this->belongsTo(Regional::class);
    }
}
