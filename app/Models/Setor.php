<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setor extends Model
{
    use HasFactory;

    protected $table = 'setores';

    protected $fillable = ['local_id', 'nome', 'active'];

    public function local()
    {
        return $this->belongsTo(Local::class);
    }
}
