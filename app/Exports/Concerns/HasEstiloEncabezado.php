<?php

namespace App\Exports\Concerns;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

trait HasEstiloEncabezado
{
    public function styles(Worksheet $sheet): array
    {
        $ultimaColumna = $sheet->getHighestColumn();
        $ultimaFila    = $sheet->getHighestRow();

        // Encabezado: fila 1 con fondo oscuro y texto blanco
        $sheet->getStyle("A1:{$ultimaColumna}1")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1E3A5F'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Filas de datos: bordes sutiles y altura
        $sheet->getStyle("A2:{$ultimaColumna}{$ultimaFila}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFD0D0D0'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Filas alternas con fondo gris muy claro
        for ($fila = 2; $fila <= $ultimaFila; $fila++) {
            if ($fila % 2 === 0) {
                $sheet->getStyle("A{$fila}:{$ultimaColumna}{$fila}")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF5F5F5'],
                    ],
                ]);
            }
        }

        // Auto-ancho para todas las columnas
        foreach (range('A', $ultimaColumna) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Altura de fila de encabezado
        $sheet->getRowDimension(1)->setRowHeight(22);

        return [];
    }
}
