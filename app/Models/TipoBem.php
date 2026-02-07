<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoBem extends Model
{
    use HasFactory;

    protected $table = 'tipos_bens';
    public $incrementing = false; // Specific codes as IDs
    protected $fillable = ['id', 'nome', 'conta_contabil_id'];

    public function contaContabil()
    {
        return $this->belongsTo(ContaContabil::class, 'conta_contabil_id');
    }
}
