<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaContabil extends Model
{
    use HasFactory;

    protected $table = 'contas_contabeis';
    public $incrementing = false; // IDs are 1101, 1102 etc
    protected $fillable = ['id', 'nome'];

    public function tiposBens()
    {
        return $this->hasMany(TipoBem::class, 'conta_contabil_id');
    }
}
