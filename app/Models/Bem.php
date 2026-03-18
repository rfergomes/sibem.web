<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bem extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'bens';
    protected $primaryKey = 'id_bem';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_bem',
        'descricao',
        'id_igreja',
        'id_dependencia',
        'id_tipo_bem',
        'id_status',
        'is_lote',
        'quantidade',
        'is_terceiro',
        'origem',
        'data_importacao'
    ];

    public function getEtiquetaFormatadaAttribute()
    {
        // Example: 21-0050 / 000086 104
        $local = (object) session('current_tenant_connection_data');
        if (!isset($local->id))
            return $this->id_bem;

        $regionalId = str_pad($local->regional_id ?? 0, 2, '0', STR_PAD_LEFT);
        $localId = str_pad($local->id, 4, '0', STR_PAD_LEFT);
        $seq = str_pad($this->id_bem ?? 0, 6, '0', STR_PAD_LEFT);
        $locCode = str_pad($this->id_dependencia ?? 0, 3, '0', STR_PAD_LEFT);

        return "{$regionalId}-{$localId} / {$seq} {$locCode}";
    }

    public function tipo()
    {
        return $this->belongsTo(TipoBem::class, 'id_tipo_bem');
    }

    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class, 'id_dependencia');
    }
}
