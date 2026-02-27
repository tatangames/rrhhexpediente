<?php

namespace App\Http\Controllers\Permiso;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PermisoPersonal;
use App\Models\PermisoCompensatorio;
use App\Models\PermisoEnfermedad;
use App\Models\PermisoConsultaMedica;
use App\Models\PermisoIncapacidad;
use App\Models\PermisoOtro;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportesExcelPermisoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: formatea fecha d-m-Y o retorna '' si es null
    // ─────────────────────────────────────────────────────────────
    private function fmt($fecha): string
    {
        return $fecha ? Carbon::parse($fecha)->format('d-m-Y') : '';
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: estilos de cabecera de columnas (gris oscuro)
    // ─────────────────────────────────────────────────────────────
    private function estiloHeader(object $sheet, string $rango): void
    {
        $sheet->getStyle($rango)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF8A8F97']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: estilos de fila de datos
    // ─────────────────────────────────────────────────────────────
    private function estiloDatos(object $sheet, string $rango, bool $par): void
    {
        $color = $par ? 'FFF9F9F9' : 'FFFFFFFF';
        $sheet->getStyle($rango)->applyFromArray([
            'font'      => ['size' => 8],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: fila de cabecera institucional (filas 1-3)
    // ─────────────────────────────────────────────────────────────
    private function cabeceraHoja(object $sheet, string $titulo, string $desde, string $hasta, int $totalCols): void
    {
        $letraFin = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);

        // Fila 1 – Título
        $sheet->mergeCells("A1:{$letraFin}1");
        $sheet->setCellValue('A1', $titulo);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF104E8C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Fila 2 – Período
        $sheet->mergeCells("A2:{$letraFin}2");
        $sheet->setCellValue('A2', 'Período: ' . $this->fmt($desde) . ' — ' . $this->fmt($hasta));
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Fila 3 – Emisión
        $sheet->mergeCells("A3:{$letraFin}3");
        $sheet->setCellValue('A3', 'Fecha de emisión: ' . now()->format('d-m-Y'));
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['size' => 8],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getRowDimension(2)->setRowHeight(14);
        $sheet->getRowDimension(3)->setRowHeight(12);
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: descarga el Spreadsheet como .xlsx
    // ─────────────────────────────────────────────────────────────
    private function descargar(Spreadsheet $spreadsheet, string $nombre): StreamedResponse
    {
        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition',
            'attachment; filename="' . $nombre . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        return $response;
    }

    // ─────────────────────────────────────────────────────────────
    //  RUTA PRINCIPAL
    // ─────────────────────────────────────────────────────────────

    public function generarReportePermisoEXCEL(Request $request)
    {
        $request->validate([
            'tipo_permiso' => 'required|integer|between:1,6',
            'fecha_desde'  => 'required|date',
            'fecha_hasta'  => 'required|date|after_or_equal:fecha_desde',
            'id_empleado'  => 'nullable|integer|exists:empleados,id',
        ]);

        $tipo       = (int) $request->tipo_permiso;
        $idEmpleado = $request->id_empleado;
        $desde      = $request->fecha_desde;
        $hasta      = $request->fecha_hasta;

        return match ($tipo) {
            1 => $this->excelPersonal($idEmpleado, $desde, $hasta),
            2 => $this->excelCompensatorio($idEmpleado, $desde, $hasta),
            3 => $this->excelEnfermedad($idEmpleado, $desde, $hasta),
            4 => $this->excelConsultaMedica($idEmpleado, $desde, $hasta),
            5 => $this->excelIncapacidad($idEmpleado, $desde, $hasta),
            6 => $this->excelOtros($idEmpleado, $desde, $hasta),
        };
    }

    // ─────────────────────────────────────────────────────────────
    //  1. PERMISO PERSONAL
    // ─────────────────────────────────────────────────────────────









    private function excelPersonal($idEmpleado, $desde, $hasta): StreamedResponse
    {
        $registros = PermisoPersonal::with('empleado')
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Permisos Personales');

        $cols   = ['#','EMPLEADO','UNIDAD','CARGO','FECHA DOC.','CONDICIÓN',
            'GOCE SALARIAL','FECHA INICIO','FECHA FIN','HORA INICIO','HORA FIN','RAZÓN'];
        $widths = [5, 28, 18, 18, 12, 14, 13, 13, 13, 11, 11, 30];

        $this->cabeceraHoja($sheet, 'REPORTE DE PERMISOS PERSONALES', $desde, $hasta, count($cols));
        $this->escribirEncabezados($sheet, $cols, $widths, 4);

        // ── Acumuladores por fecha ─────────────────────────────
        // ['2025-01-05' => ['dias'=>0,'minutos'=>0,'con_goce'=>0,'sin_goce'=>0]]
        $porFecha     = [];
        $totalDias    = 0;
        $totalMinutos = 0;

        $fila = 5;
        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $goce        = $p->goce ? 'SÍ' : 'NO';
            $fechaInicio = $p->condicion ? $this->fmt($p->fecha_fraccionado) : $this->fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '' : $this->fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '') : '';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '') : '';

            $clave = $p->fecha;
            if (!isset($porFecha[$clave])) {
                $porFecha[$clave] = ['dias' => 0, 'minutos' => 0, 'con_goce' => 0, 'sin_goce' => 0];
            }

            // Acumular goce
            $p->goce ? $porFecha[$clave]['con_goce']++ : $porFecha[$clave]['sin_goce']++;

            if (!$p->condicion) {
                if ($p->fecha_inicio && $p->fecha_fin) {
                    $dias = Carbon::parse($p->fecha_inicio)
                            ->diffInDays(Carbon::parse($p->fecha_fin)) + 1;
                    $porFecha[$clave]['dias'] += $dias;
                    $totalDias               += $dias;
                }
            } else {
                if ($p->hora_inicio && $p->hora_fin) {
                    $mins = Carbon::parse($p->hora_inicio)
                        ->diffInMinutes(Carbon::parse($p->hora_fin));
                    $porFecha[$clave]['minutos'] += $mins;
                    $totalMinutos               += $mins;
                }
            }

            $sheet->fromArray([
                $i + 1, $p->empleado?->nombre, $p->unidad, $p->cargo,
                $this->fmt($p->fecha), $condicion, $goce,
                $fechaInicio, $fechaFin, $horaInicio, $horaFin, $p->razon,
            ], null, "A{$fila}");

            $this->estiloDatos($sheet, "A{$fila}:L{$fila}", $i % 2 === 0);

            // Colorear celda GOCE SALARIAL (columna G = 7)
            $colorGoce = $p->goce ? 'FF1a7a3a' : 'FFb02020';  // verde / rojo
            $sheet->getStyle("G{$fila}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => $colorGoce]],
            ]);

            $sheet->getStyle("A{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $fila++;
        }

        // ── Separador ─────────────────────────────────────────
        $fila++;

        // ── Encabezado bloque resumen ──────────────────────────
        $letraFin = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($cols));

        $sheet->mergeCells("A{$fila}:{$letraFin}{$fila}");
        $sheet->setCellValue("A{$fila}", 'RESUMEN POR FECHA');
        $sheet->getStyle("A{$fila}:{$letraFin}{$fila}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $fila++;

        // ── Encabezados columnas resumen (ahora 5 columnas) ────
        $headersResumen = ['FECHA DOC.', 'DÍAS COMPLETOS', 'TIEMPO FRACCIONADO', 'CON GOCE', 'SIN GOCE'];
        foreach ($headersResumen as $idx => $titulo) {
            $letra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx + 1);
            $sheet->setCellValue("{$letra}{$fila}", $titulo);
        }
        $sheet->getStyle("A{$fila}:E{$fila}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF8A8F97']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $fila++;

        // ── Filas por fecha ────────────────────────────────────
        ksort($porFecha);

        $totalConGoce = 0;
        $totalSinGoce = 0;

        foreach ($porFecha as $fecha => $v) {
            $mins        = $v['minutos'];
            $horas       = intdiv($mins, 60);
            $minR        = $mins % 60;
            $tiempoTexto = $mins > 0 ? "{$mins} min ({$horas}h {$minR}m)" : '-';
            $diasTexto   = $v['dias'] > 0 ? $v['dias'] . ' día(s)' : '-';

            $totalConGoce += $v['con_goce'];
            $totalSinGoce += $v['sin_goce'];

            $sheet->setCellValue("A{$fila}", Carbon::parse($fecha)->format('d-m-Y'));
            $sheet->setCellValue("B{$fila}", $diasTexto);
            $sheet->setCellValue("C{$fila}", $tiempoTexto);
            $sheet->setCellValue("D{$fila}", $v['con_goce'] > 0 ? $v['con_goce'] : '-');
            $sheet->setCellValue("E{$fila}", $v['sin_goce'] > 0 ? $v['sin_goce'] : '-');

            $sheet->getStyle("A{$fila}:E{$fila}")->applyFromArray([
                'font'      => ['size' => 8],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            // Verde para CON GOCE, rojo para SIN GOCE (solo si tienen valor)
            if ($v['con_goce'] > 0) {
                $sheet->getStyle("D{$fila}")->getFont()
                    ->setBold(true)->getColor()->setARGB('FF1a7a3a');
            }
            if ($v['sin_goce'] > 0) {
                $sheet->getStyle("E{$fila}")->getFont()
                    ->setBold(true)->getColor()->setARGB('FFb02020');
            }

            $fila++;
        }

        // ── Fila TOTAL GENERAL ─────────────────────────────────
        $totalMinsH       = intdiv($totalMinutos, 60);
        $totalMinsR       = $totalMinutos % 60;
        $totalTiempoTexto = $totalMinutos > 0 ? "{$totalMinutos} min ({$totalMinsH}h {$totalMinsR}m)" : '-';
        $totalDiasTexto   = $totalDias > 0 ? "{$totalDias} día(s)" : '-';

        $sheet->setCellValue("A{$fila}", 'TOTAL GENERAL');
        $sheet->setCellValue("B{$fila}", $totalDiasTexto);
        $sheet->setCellValue("C{$fila}", $totalTiempoTexto);
        $sheet->setCellValue("D{$fila}", $totalConGoce > 0 ? $totalConGoce : '-');
        $sheet->setCellValue("E{$fila}", $totalSinGoce > 0 ? $totalSinGoce : '-');

        $sheet->getStyle("A{$fila}:E{$fila}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // ── Anchos de columnas resumen ─────────────────────────
        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(24);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);

        return $this->descargar($spreadsheet, 'Reporte_Permisos_Personales.xlsx');
    }













    // ─────────────────────────────────────────────────────────────
    //  2. PERMISO COMPENSATORIO
    // ─────────────────────────────────────────────────────────────
    private function excelCompensatorio($idEmpleado, $desde, $hasta): StreamedResponse
    {
        $registros = PermisoCompensatorio::with('empleado')
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Compensatorios');

        $cols   = ['#','EMPLEADO','UNIDAD','CARGO','FECHA DOC.','CONDICIÓN',
            'FECHA INICIO','FECHA FIN','HORA INICIO','HORA FIN','RAZÓN'];
        $widths = [5, 28, 18, 18, 12, 14, 13, 13, 11, 11, 30];

        $this->cabeceraHoja($sheet, 'REPORTE DE PERMISOS COMPENSATORIOS', $desde, $hasta, count($cols));
        $this->escribirEncabezados($sheet, $cols, $widths, 4);

        $fila = 5;
        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? $this->fmt($p->fecha_fraccionado) : $this->fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '' : $this->fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '') : '';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '') : '';

            $sheet->fromArray([
                $i + 1, $p->empleado?->nombre, $p->unidad, $p->cargo,
                $this->fmt($p->fecha), $condicion,
                $fechaInicio, $fechaFin, $horaInicio, $horaFin, $p->razon,
            ], null, "A{$fila}");

            $this->estiloDatos($sheet, "A{$fila}:K{$fila}", $i % 2 === 0);
            $sheet->getStyle("A{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $fila++;
        }

        $this->filaTotales($sheet, "A{$fila}", count($cols), count($registros));
        return $this->descargar($spreadsheet, 'Reporte_Permisos_Compensatorios.xlsx');
    }

    // ─────────────────────────────────────────────────────────────
    //  3. PERMISO ENFERMEDAD
    // ─────────────────────────────────────────────────────────────
    private function excelEnfermedad($idEmpleado, $desde, $hasta): StreamedResponse
    {
        $registros = PermisoEnfermedad::with('empleado')
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Enfermedad');

        $cols   = ['#','EMPLEADO','UNIDAD','CARGO','FECHA DOC.','CONDICIÓN',
            'UNIDAD ATENCIÓN','ESPECIALIDAD','COND. MÉDICA',
            'INICIO','FIN','H. INICIO','H. FIN'];
        $widths = [5, 25, 16, 16, 12, 13, 18, 16, 18, 12, 12, 10, 10];

        $this->cabeceraHoja($sheet, 'REPORTE DE PERMISOS POR ENFERMEDAD', $desde, $hasta, count($cols));
        $this->escribirEncabezados($sheet, $cols, $widths, 4);

        $fila = 5;
        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? $this->fmt($p->fecha_fraccionado) : $this->fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '' : $this->fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '') : '';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '') : '';

            $sheet->fromArray([
                $i + 1, $p->empleado?->nombre, $p->unidad, $p->cargo,
                $this->fmt($p->fecha), $condicion,
                $p->unidad_atencion, $p->especialidad, $p->condicion_medica,
                $fechaInicio, $fechaFin, $horaInicio, $horaFin,
            ], null, "A{$fila}");

            $this->estiloDatos($sheet, "A{$fila}:M{$fila}", $i % 2 === 0);
            $sheet->getStyle("A{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $fila++;
        }

        $this->filaTotales($sheet, "A{$fila}", count($cols), count($registros));
        return $this->descargar($spreadsheet, 'Reporte_Permisos_Enfermedad.xlsx');
    }

    // ─────────────────────────────────────────────────────────────
    //  4. PERMISO CONSULTA MÉDICA
    // ─────────────────────────────────────────────────────────────
    private function excelConsultaMedica($idEmpleado, $desde, $hasta): StreamedResponse
    {
        $registros = PermisoConsultaMedica::with('empleado')
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Consulta Médica');

        $cols   = ['#','EMPLEADO','UNIDAD','CARGO','FECHA DOC.','CONDICIÓN',
            'UNIDAD ATENCIÓN','ESPECIALIDAD','COND. MÉDICA',
            'INICIO','FIN','H. INICIO','H. FIN'];
        $widths = [5, 25, 16, 16, 12, 13, 18, 16, 18, 12, 12, 10, 10];

        $this->cabeceraHoja($sheet, 'REPORTE DE PERMISOS - CONSULTA MÉDICA', $desde, $hasta, count($cols));
        $this->escribirEncabezados($sheet, $cols, $widths, 4);

        $fila = 5;
        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? $this->fmt($p->fecha_fraccionado) : $this->fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '' : $this->fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '') : '';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '') : '';

            $sheet->fromArray([
                $i + 1, $p->empleado?->nombre, $p->unidad, $p->cargo,
                $this->fmt($p->fecha), $condicion,
                $p->unidad_atencion, $p->especialidad, $p->condicion_medica,
                $fechaInicio, $fechaFin, $horaInicio, $horaFin,
            ], null, "A{$fila}");

            $this->estiloDatos($sheet, "A{$fila}:M{$fila}", $i % 2 === 0);
            $sheet->getStyle("A{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $fila++;
        }

        $this->filaTotales($sheet, "A{$fila}", count($cols), count($registros));
        return $this->descargar($spreadsheet, 'Reporte_Consulta_Medica.xlsx');
    }

    // ─────────────────────────────────────────────────────────────
    //  5. PERMISO INCAPACIDAD
    // ─────────────────────────────────────────────────────────────
    private function excelIncapacidad($idEmpleado, $desde, $hasta): StreamedResponse
    {
        $registros = PermisoIncapacidad::with(['empleado', 'tipoIncapacidad', 'riesgo'])
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Incapacidades');

        $cols   = ['#','EMPLEADO','UNIDAD','CARGO','FECHA DOC.','TIPO INCAPACIDAD',
            'RIESGO','DIAGNÓSTICO','N° DOC.','INICIO','FIN','DÍAS','HOSPITALIZACIÓN'];
        $widths = [5, 25, 16, 16, 12, 18, 14, 28, 10, 12, 12, 6, 30];

        $this->cabeceraHoja($sheet, 'REPORTE DE PERMISOS POR INCAPACIDAD', $desde, $hasta, count($cols));
        $this->escribirEncabezados($sheet, $cols, $widths, 4);

        $fila = 5;
        foreach ($registros as $i => $p) {
            $hospitaliza = $p->hospitalizacion
                ? 'SÍ (' . $this->fmt($p->fecha_inicio_hospitalizacion) . ' al ' . $this->fmt($p->fecha_fin_hospitalizacion) . ')'
                : 'NO';

            $sheet->fromArray([
                $i + 1, $p->empleado?->nombre, $p->unidad, $p->cargo,
                $this->fmt($p->fecha),
                $p->tipoIncapacidad?->nombre,
                $p->riesgo?->nombre,
                $p->diagnostico, $p->numero,
                $this->fmt($p->fecha_inicio),
                $this->fmt($p->fecha_fin),
                $p->dias, $hospitaliza,
            ], null, "A{$fila}");

            $this->estiloDatos($sheet, "A{$fila}:M{$fila}", $i % 2 === 0);
            $sheet->getStyle("A{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $fila++;
        }

        // Fila totales — incapacidad tiene 2 (registros + días)
        $letraFin = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($cols));
        $sheet->mergeCells("A{$fila}:K{$fila}");
        $sheet->setCellValue("A{$fila}", 'Total registros: ' . count($registros) . '     |     Total días incapacidad: ' . $registros->sum('dias'));
        $sheet->getStyle("A{$fila}:{$letraFin}{$fila}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 8],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE8E8E8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        return $this->descargar($spreadsheet, 'Reporte_Incapacidades.xlsx');
    }

    // ─────────────────────────────────────────────────────────────
    //  6. PERMISO OTROS
    // ─────────────────────────────────────────────────────────────
    private function excelOtros($idEmpleado, $desde, $hasta): StreamedResponse
    {
        $registros = PermisoOtro::with('empleado')
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Otros Permisos');

        $cols   = ['#','EMPLEADO','UNIDAD','CARGO','FECHA DOC.','CONDICIÓN',
            'FECHA INICIO','FECHA FIN','HORA INICIO','HORA FIN','RAZÓN'];
        $widths = [5, 28, 18, 18, 12, 14, 13, 13, 11, 11, 30];

        $this->cabeceraHoja($sheet, 'REPORTE DE OTROS PERMISOS', $desde, $hasta, count($cols));
        $this->escribirEncabezados($sheet, $cols, $widths, 4);

        $fila = 5;
        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? $this->fmt($p->fecha_fraccionado) : $this->fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '' : $this->fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '') : '';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '') : '';

            $sheet->fromArray([
                $i + 1, $p->empleado?->nombre, $p->unidad, $p->cargo,
                $this->fmt($p->fecha), $condicion,
                $fechaInicio, $fechaFin, $horaInicio, $horaFin, $p->razon,
            ], null, "A{$fila}");

            $this->estiloDatos($sheet, "A{$fila}:K{$fila}", $i % 2 === 0);
            $sheet->getStyle("A{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $fila++;
        }

        $this->filaTotales($sheet, "A{$fila}", count($cols), count($registros));
        return $this->descargar($spreadsheet, 'Reporte_Otros_Permisos.xlsx');
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: escribe encabezados de columna en la fila dada
    // ─────────────────────────────────────────────────────────────
    private function escribirEncabezados(object $sheet, array $cols, array $widths, int $fila): void
    {
        foreach ($cols as $idx => $col) {
            $letra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx + 1);
            $sheet->setCellValue("{$letra}{$fila}", $col);
            $sheet->getColumnDimension($letra)->setWidth($widths[$idx]);
        }
        $letraFin = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($cols));
        $sheet->getRowDimension($fila)->setRowHeight(28);
        $this->estiloHeader($sheet, "A{$fila}:{$letraFin}{$fila}");
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: fila de total registros al final
    // ─────────────────────────────────────────────────────────────
    private function filaTotales(object $sheet, string $celda, int $totalCols, int $total): void
    {
        $fila     = (int) filter_var($celda, FILTER_SANITIZE_NUMBER_INT);
        $letraFin = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);

        $sheet->mergeCells("A{$fila}:{$letraFin}{$fila}");
        $sheet->setCellValue("A{$fila}", "Total de registros: {$total}");
        $sheet->getStyle("A{$fila}:{$letraFin}{$fila}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 8],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE8E8E8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
    }
}
