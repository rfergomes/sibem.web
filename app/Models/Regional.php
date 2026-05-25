<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regional extends Model
{
    use HasFactory;

    protected $connection = 'mysql_sys';
    protected $table = 'admrgs_v2';

    protected $fillable = [
        'admrg_id',
        'adm_regional',
        'uf'
    ];

    // Accessors for backward compatibility
    public function getNomeAttribute()
    {
        return $this->adm_regional;
    }

    public function setNomeAttribute($value)
    {
        $this->attributes['adm_regional'] = $value;
    }

    public function locais()
    {
        return $this->hasMany(Local::class, 'admrg_id', 'admrg_id');
    }
}
