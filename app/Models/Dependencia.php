<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependencia extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; // Table is in the shared ADM database (sibemo33_adm)
    protected $table = 'dependencias';

    protected $fillable = ['nome', 'active'];

    public function bens()
    {
        return $this->hasMany(Bem::class, 'id_dependencia', 'id');
    }
}
