<?php

namespace App\Imports;

use App\Models\Bem;
use App\Models\Igreja;
use App\Models\Dependencia;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class BensImport implements OnEachRow, WithStartRow, WithChunkReading, SkipsEmptyRows, SkipsOnFailure, SkipsOnError
{
    use SkipsFailures, SkipsErrors;

    protected $inventarioId;
    protected $idIgrejaInventario;

    public function __construct($inventarioId = null)
    {
        $this->inventarioId = $inventarioId;
        if ($this->inventarioId) {
            $inv = \App\Models\Inventario::find($this->inventarioId);
            $this->idIgrejaInventario = $inv ? $inv->id_igreja : null;
        }
    }

    /**
     * @param Row $row
     */
    public function onRow(Row $row)
    {
        $rowData = $row->toArray();

        $idBem = null;
        $descricao = null;
        $idStatus = 1; // Default
        $idIgreja = null;
        $setorEncontrado = null;
        $idDependencia = null;

        // 1. Find ID (First column resembling an ID)
        $idIndex = -1;
        foreach ($rowData as $index => $cell) {
            $val = (string) $cell;
            if (preg_match('/[0-9]{2,}/', $val) || preg_match('/[0-9]+[\/-][0-9]+/', $val)) {
                $sanitizedInfo = trim(str_replace(['-', ' / ', '/'], ['', '', ''], $val));
                if (ctype_digit($sanitizedInfo) && strlen($sanitizedInfo) > 3) {
                    $idBem = substr($sanitizedInfo, 0, 20);
                    $idIndex = $index;
                    break;
                }
            }
        }

        if (empty($idBem)) {
            return null;
        }

        // 2. Find Description
        for ($i = $idIndex + 1; $i < count($rowData); $i++) {
            $val = isset($rowData[$i]) ? trim((string) $rowData[$i]) : '';
            if (!empty($val) && strlen($val) > 2 && preg_match('/[a-zA-Z]/', $val)) {
                if (!preg_match('/^\d{2}-\d{4}/', $val)) {
                    $descricao = $val;
                    break;
                }
            }
        }

        if (empty($descricao)) {
            return null;
        }

        // 3. Find Localidade
        foreach ($rowData as $val) {
            $val = (string) $val;
            if (preg_match('/(\d{2}-\d{4})/', $val, $matches)) {
                $idIgreja = str_replace('-', '', $matches[1]);
                break;
            }
        }

        // 4. Find Sector
        foreach ($rowData as $val) {
            $val = (string) $val;
            if (preg_match('/SETOR\s+([0-9]+[A-Z]?)/i', $val, $matches)) {
                $setorEncontrado = strtoupper($matches[1]);
                break;
            }
        }

        if ($idIgreja && $setorEncontrado) {
            Igreja::where('codigo_ccb', $idIgreja)->update(['setor' => $setorEncontrado]);
        }

        // 5. Find Status
        foreach ($rowData as $val) {
            $val = mb_strtolower(trim((string) $val));
            if (in_array($val, ['ativo', 'baixado', 'ruim', 'sucata', 'inativo', 'leilao', 'furtado', 'roubado'])) {
                $idStatus = $this->parseStatus($val);
                break;
            }
        }

        // UPSERT LOGIC
        Bem::updateOrCreate(
            ['id_bem' => $idBem],
            [
                'descricao' => $descricao,
                'id_status' => $idStatus,
                'id_igreja' => $idIgreja,
                'origem' => 'importado',
                'data_importacao' => now(),
            ]
        );
    }

    public function startRow(): int
    {
        return 25;
    }

    private function parseStatus($status)
    {
        if (!is_string($status))
            return 1;
        $status = mb_strtolower(trim($status));
        if ($status === '')
            return 1;

        if (preg_match('/(baixado|ruim|sucata|inativo|leilao|furtado|roubado)/', $status)) {
            return 0; // Inativo
        }
        return 1; // Ativo
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function onFailure(Failure ...$failures)
    {
    }
    public function onError(\Throwable $e)
    {
    }
}
