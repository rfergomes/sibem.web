<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regional extends Model
{
    use HasFactory;

    protected $table = 'regionais';

    protected $fillable = [
        'nome',
        'active'
    ];

    public function locais()
    {
        return $this->hasMany(Local::class);
    }
}
