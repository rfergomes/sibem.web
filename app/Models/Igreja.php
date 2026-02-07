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
        'setor',
        'razao_social',
        'cnpj',
        'logradouro',
        'numero',
        'uf',
        'observacao',
        'id_status',
        'id_tipo'
    ];

    /**
     * Get the formatted SIGA code.
     * Example: 220317 -> BR 22-0317
     */
    public function getCodSigaAttribute()
    {
        if (!$this->codigo_ccb) {
            return null;
        }

        // Remove non-numeric characters just in case
        $code = preg_replace('/\D/', '', $this->codigo_ccb);

        // Ensure it has at least 4 digits to split (adjust logic as needed)
        if (strlen($code) < 4) {
            return "BR {$code}";
        }

        $part1 = substr($code, 0, 2);
        $part2 = substr($code, 2);

        return "BR {$part1}-{$part2}";
    }

    public function local()
    {
        return $this->belongsTo(Local::class);
    }
}
