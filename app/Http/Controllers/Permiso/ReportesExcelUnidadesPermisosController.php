<?php

namespace App\Http\Controllers\Permiso;

use App\Http\Controllers\Controller;
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

class ReportesExcelUnidadesPermisosController extends Controller
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
    //  Helper: aplica el filtro por rango de fechas usando la
    //  fecha real del permiso (fecha_fraccionado / fecha_inicio-fin)
    //  en vez de la fecha de entrega del documento.
    // ─────────────────────────────────────────────────────────────
    private function filtrarPorFechaPermiso($query, $desde, $hasta)
    {
        return $query->where(function ($q) use ($desde, $hasta) {
            // Fraccionado: la fecha del permiso cae dentro del rango
            $q->where(function ($q2) use ($desde, $hasta) {
                $q2->where('condicion', 1)
                    ->whereDate('fecha_fraccionado', '>=', $desde)
                    ->whereDate('fecha_fraccionado', '<=', $hasta);
            })
                // Completo: el rango del permiso se solapa con el rango solicitado
                ->orWhere(function ($q2) use ($desde, $hasta) {
                    $q2->where('condicion', 0)
                        ->whereDate('fecha_inicio', '<=', $hasta)
                        ->whereDate('fecha_fin', '>=', $desde);
                });
        })->orderByRaw('COALESCE(fecha_fraccionado, fecha_inicio)');
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: filtra por unidad usando la relación con empleado
    // ─────────────────────────────────────────────────────────────
    private function filtrarPorUnidad($query, $idUnidad)
    {
        return $query->when($idUnidad, function ($q) use ($idUnidad) {
            $q->whereHas('empleado', fn($qq) => $qq->where('id_unidad', $idUnidad));
        });
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

        $sheet->mergeCells("A1:{$letraFin}1");
        $sheet->setCellValue('A1', $titulo);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF104E8C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells("A2:{$letraFin}2");
        $sheet->setCellValue('A2', 'Período: ' . $this->fmt($desde) . ' — ' . $this->fmt($hasta));
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

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
        $writer   = new Xlsx($spreadsheet);
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

    // ─────────────────────────────────────────────────────────────
    //  RUTA PRINCIPAL
    //  (se conserva el nombre "generarReportePermisoPDFPorUnidad"
    //  para coincidir con la ruta 'permiso.excel.generar.unidad')
    // ─────────────────────────────────────────────────────────────
    public function generarReportePermisoPDFPorUnidad(Request $request)
    {
        $request->validate([
            'tipo_permiso' => 'required|integer|between:0,6',
            'fecha_desde'  => 'required|date',
            'fecha_hasta'  => 'required|date|after_or_equal:fecha_desde',
            'id_unidad'    => 'nullable|integer',
        ], [
            'fecha_desde.required'       => 'La fecha de inicio es requerida.',
            'fecha_hasta.required'       => 'La fecha de fin es requerida.',
            'fecha_hasta.after_or_equal' => 'La fecha "Hasta" debe ser mayor o igual a "Desde".',
            'tipo_permiso.required'      => 'Seleccione el tipo de permiso.',
        ]);

        $tipo = (int) $request->tipo_permiso;

        $idUnidad = ($request->id_unidad && $request->id_unidad != '0')
            ? $request->id_unidad
            : null;

        $desde = $request->fecha_desde;
        $hasta = $request->fecha_hasta;

        if ($tipo === 0) {
            return $this->excelTodos($idUnidad, $desde, $hasta);
        }

        return match ($tipo) {
            1 => $this->excelPersonal($idUnidad, $desde, $hasta),
            2 => $this->excelCompensatorio($idUnidad, $desde, $hasta),
            3 => $this->excelEnfermedad($idUnidad, $desde, $hasta),
            4 => $this->excelConsultaMedica($idUnidad, $desde, $hasta),
            5 => $this->excelIncapacidad($idUnidad, $desde, $hasta),
            6 => $this->excelOtros($idUnidad, $desde, $hasta),
        };
    }

    // ─────────────────────────────────────────────────────────────
    //  0. TODOS LOS TIPOS (un Excel con 7 hojas) - POR UNIDAD
    // ─────────────────────────────────────────────────────────────
    private function excelTodos($idUnidad, $desde, $hasta): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // quita la hoja vacía por defecto

        // ── Consultas ─────────────────────────────────────────
        $personales = $this->filtrarPorFechaPermiso(
            $this->filtrarPorUnidad(PermisoPersonal::with('empleado'), $idUnidad),
            $desde, $hasta
        )->get();

        $compensatorios = $this->filtrarPorFechaPermiso(
            $this->filtrarPorUnidad(PermisoCompensatorio::with('empleado'), $idUnidad),
            $desde, $hasta
        )->get();

        $enfermedades = $this->filtrarPorFechaPermiso(
            $this->filtrarPorUnidad(PermisoEnfermedad::with('empleado'), $idUnidad),
            $desde, $hasta
        )->get();

        $consultas = $this->filtrarPorFechaPermiso(
            $this->filtrarPorUnidad(PermisoConsultaMedica::with('empleado'), $idUnidad),
            $desde, $hasta
        )->get();

        $incapacidades = $this->filtrarPorUnidad(
            PermisoIncapacidad::with(['empleado', 'tipoIncapacidad', 'riesgo']),
            $idUnidad
        )
            ->whereDate('fecha_inicio', '<=', $hasta)
            ->whereDate('fecha_fin', '>=', $desde)
            ->orderBy('fecha_inicio')
            ->get();

        $otros = $this->filtrarPorFechaPermiso(
            $this->filtrarPorUnidad(PermisoOtro::with('empleado'), $idUnidad),
            $desde, $hasta
        )->get();

        // ── Hoja 1: Personales ────────────────────────────────
        $sh1  = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Personales');
        $spreadsheet->addSheet($sh1);
        $cols = ['#','EMPLEADO','UNIDAD','CARGO','FECHA DOC.','CONDICIÓN',
            'GOCE SALARIAL','FECHA INICIO','FECHA FIN','HORA INICIO','HORA FIN','RAZÓN'];
        $widths = [5,28,18,18,12,14,13,13,13,11,11,30];
        $this->cabeceraHoja($sh1, 'REPORTE DE PERMISOS PERSONALES', $desde, $hasta, count($cols));
        $this->escribirEncabezados($sh1, $cols, $widths, 4);
        $fila = 5;
        foreach ($personales as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $goce        = $p->goce ? 'SÍ' : 'NO';
            $fechaInicio = $p->condicion ? $this->fmt($p->fecha_fraccionado) : $this->fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '' : $this->fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '') : '';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '') : '';
            $sh1->fromArray([
                $i+1, $p->empleado?->nombre, $p->unidad, $p->cargo,
                $this->fmt($p->fecha), $condicion, $goce,
                $fechaInicio, $fechaFin, $horaInicio, $horaFin, $p->razon,
            ], null, "A{$fila}");
            $this->estiloDatos($sh1, "A{$fila}:L{$fila}", $i % 2 === 0);
            $sh1->getStyle("A{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $colorGoce = $p->goce ? 'FF1a7a3a' : 'FFb02020';
            $sh1->getStyle("G{$fila}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => $colorGoce]],
            ]);
            $fila++;
        }
        $this->filaTotales($sh1, "A{$fila}", count($cols), count($personales));

        // ── Hoja 2: Compensatorios ────────────────────────────
        $sh2  = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Compensatorios');
        $spreadsheet->addSheet($sh2);
        $cols = ['#','EMPLEADO','UNIDAD','CARGO','FECHA DOC.','CONDICIÓN',
            'FECHA INICIO','FECHA FIN','HORA INICIO','HORA FIN','RAZÓN'];
        $widths = [5,28,18,18,12,14,13,13,11,11,30];
        $this->cabeceraHoja($sh2, 'REPORTE DE PERMISOS COMPENSATORIOS', $desde, $hasta, count($cols));
        $this->escribirEncabezados($sh2, $cols, $widths, 4);
        $fila = 5;
        foreach ($compensatorios as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? $this->fmt($p->fecha_fraccionado) : $this->fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '' : $this->fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '') : '';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '') : '';
            $sh2->fromArray([
                $i+1, $p->empleado?->nombre, $p->unidad, $p->cargo,
                $this->fmt($p->fecha), $condicion,
                $fechaInicio, $fechaFin, $horaInicio, $horaFin, $p->razon,
            ], null, "A{$fila}");
            $this->estiloDatos($sh2, "A{$fila}:K{$fila}", $i % 2 === 0);
            $sh2->getStyle("A{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $fila++;
        }
        $this->filaTotales($sh2, "A{$fila}", count($cols), count($compensatorios));

        // ── Hoja 3: Enfermedad ────────────────────────────────
        $sh3  = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Enfermedad');
        $spreadsheet->addSheet($sh3);
        $cols = ['#','EMPLEADO','UNIDAD','CARGO','FECHA DOC.','CONDICIÓN',
            'UNIDAD ATENCIÓN','ESPECIALIDAD','COND. MÉDICA','INICIO','FIN','H. INICIO','H. FIN'];
        $widths = [5,25,16,16,12,13,18,16,18,12,12,10,10];
        $this->cabeceraHoja($sh3, 'REPORTE DE PERMISOS POR ENFERMEDAD', $desde, $hasta, count($cols));
        $this->escribirEncabezados($sh3, $cols, $widths, 4);
        $fila = 5;
        foreach ($enfermedades as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? $this->fmt($p->fecha_fraccionado) : $this->fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '' : $this->fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '') : '';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '') : '';
            $sh3->fromArray([
                $i+1, $p->empleado?->nombre, $p->unidad, $p->cargo,
                $this->fmt($p->fecha), $condicion,
                $p->unidad_atencion, $p->especialidad, $p->condicion_medica,
                $fechaInicio, $fechaFin, $horaInicio, $horaFin,
            ], null, "A{$fila}");
            $this->estiloDatos($sh3, "A{$fila}:M{$fila}", $i % 2 === 0);
            $sh3->getStyle("A{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $fila++;
        }
        $this->filaTotales($sh3, "A{$fila}", count($cols), count($enfermedades));

        // ── Hoja 4: Consulta Médica ───────────────────────────
        $sh4  = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Consulta Médica');
        $spreadsheet->addSheet($sh4);
        $cols = ['#','EMPLEADO','UNIDAD','CARGO','FECHA DOC.','CONDICIÓN',
            'UNIDAD ATENCIÓN','ESPECIALIDAD','COND. MÉDICA','INICIO','FIN','H. INICIO','H. FIN'];
        $widths = [5,25,16,16,12,13,18,16,18,12,12,10,10];
        $this->cabeceraHoja($sh4, 'REPORTE DE PERMISOS - CONSULTA MÉDICA', $desde, $hasta, count($cols));
        $this->escribirEncabezados($sh4, $cols, $widths, 4);
        $fila = 5;
        foreach ($consultas as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? $this->fmt($p->fecha_fraccionado) : $this->fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '' : $this->fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '') : '';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '') : '';
            $sh4->fromArray([
                $i+1, $p->empleado?->nombre, $p->unidad, $p->cargo,
                $this->fmt($p->fecha), $condicion,
                $p->unidad_atencion, $p->especialidad, $p->condicion_medica,
                $fechaInicio, $fechaFin, $horaInicio, $horaFin,
            ], null, "A{$fila}");
            $this->estiloDatos($sh4, "A{$fila}:M{$fila}", $i % 2 === 0);
            $sh4->getStyle("A{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $fila++;
        }
        $this->filaTotales($sh4, "A{$fila}", count($cols), count($consultas));

        // ── Hoja 5: Incapacidades ─────────────────────────────
        $sh5  = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Incapacidades');
        $spreadsheet->addSheet($sh5);
        $cols = ['#','EMPLEADO','UNIDAD','CARGO','FECHA DOC.','TIPO INCAPACIDAD',
            'RIESGO','DIAGNÓSTICO','N° DOC.','INICIO','FIN','DÍAS','HOSPITALIZACIÓN'];
        $widths = [5,25,16,16,12,18,14,28,10,12,12,6,30];
        $this->cabeceraHoja($sh5, 'REPORTE DE PERMISOS POR INCAPACIDAD', $desde, $hasta, count($cols));
        $this->escribirEncabezados($sh5, $cols, $widths, 4);
        $fila = 5;
        foreach ($incapacidades as $i => $p) {
            $hospitaliza = $p->hospitalizacion
                ? 'SÍ ('.$this->fmt($p->fecha_inicio_hospitalizacion).' al '.$this->fmt($p->fecha_fin_hospitalizacion).')'
                : 'NO';
            $sh5->fromArray([
                $i+1, $p->empleado?->nombre, $p->unidad, $p->cargo,
                $this->fmt($p->fecha),
                $p->tipoIncapacidad?->nombre,
                $p->riesgo?->nombre,
                $p->diagnostico, $p->numero,
                $this->fmt($p->fecha_inicio),
                $this->fmt($p->fecha_fin),
                $p->dias, $hospitaliza,
            ], null, "A{$fila}");
            $this->estiloDatos($sh5, "A{$fila}:M{$fila}", $i % 2 === 0);
            $sh5->getStyle("A{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $fila++;
        }
        $letraFin = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($cols));
        $sh5->mergeCells("A{$fila}:{$letraFin}{$fila}");
        $sh5->setCellValue("A{$fila}", 'Total registros: '.count($incapacidades).'     |     Total días incapacidad: '.$incapacidades->sum('dias'));
        $sh5->getStyle("A{$fila}:{$letraFin}{$fila}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 8],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE8E8E8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // ── Hoja 6: Otros ─────────────────────────────────────
        $sh6  = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Otros Permisos');
        $spreadsheet->addSheet($sh6);
        $cols = ['#','EMPLEADO','UNIDAD','CARGO','FECHA DOC.','CONDICIÓN',
            'FECHA INICIO','FECHA FIN','HORA INICIO','HORA FIN','RAZÓN'];
        $widths = [5,28,18,18,12,14,13,13,11,11,30];
        $this->cabeceraHoja($sh6, 'REPORTE DE OTROS PERMISOS', $desde, $hasta, count($cols));
        $this->escribirEncabezados($sh6, $cols, $widths, 4);
        $fila = 5;
        foreach ($otros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? $this->fmt($p->fecha_fraccionado) : $this->fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '' : $this->fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '') : '';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '') : '';
            $sh6->fromArray([
                $i+1, $p->empleado?->nombre, $p->unidad, $p->cargo,
                $this->fmt($p->fecha), $condicion,
                $fechaInicio, $fechaFin, $horaInicio, $horaFin, $p->razon,
            ], null, "A{$fila}");
            $this->estiloDatos($sh6, "A{$fila}:K{$fila}", $i % 2 === 0);
            $sh6->getStyle("A{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $fila++;
        }
        $this->filaTotales($sh6, "A{$fila}", count($cols), count($otros));

        // ── Hoja 7: RESUMEN GENERAL ───────────────────────────
        $shR = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Resumen General');
        $spreadsheet->addSheet($shR);

        $this->cabeceraHoja($shR, 'RESUMEN GENERAL DE PERMISOS POR UNIDAD', $desde, $hasta, 3);

        $shR->setCellValue('A4', 'TIPO DE PERMISO');
        $shR->setCellValue('B4', 'TOTAL REGISTROS');
        $shR->setCellValue('C4', 'OBSERVACIÓN');
        $shR->getStyle('A4:C4')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF104E8C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $shR->getRowDimension(4)->setRowHeight(20);
        $shR->getColumnDimension('A')->setWidth(28);
        $shR->getColumnDimension('B')->setWidth(18);
        $shR->getColumnDimension('C')->setWidth(35);

        $totalDiasIncap = $incapacidades->sum('dias');
        $totalGeneral   = count($personales) + count($compensatorios) + count($enfermedades)
            + count($consultas) + count($incapacidades) + count($otros);

        $filas = [
            ['Permisos Personales',     count($personales),     ''],
            ['Permisos Compensatorios', count($compensatorios), ''],
            ['Permisos por Enfermedad', count($enfermedades),   ''],
            ['Consultas Médicas',       count($consultas),      ''],
            ['Incapacidades',           count($incapacidades),  "Total días incapacidad: {$totalDiasIncap}"],
            ['Otros Permisos',          count($otros),          ''],
        ];

        $fila = 5;
        foreach ($filas as $i => $row) {
            $bg = $i % 2 === 0 ? 'FFF2F2F2' : 'FFFFFFFF';
            $shR->setCellValue("A{$fila}", $row[0]);
            $shR->setCellValue("B{$fila}", $row[1]);
            $shR->setCellValue("C{$fila}", $row[2]);
            $shR->getStyle("A{$fila}:C{$fila}")->applyFromArray([
                'font'      => ['size' => 9],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $shR->getStyle("A{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $fila++;
        }

        $shR->setCellValue("A{$fila}", 'TOTAL GENERAL');
        $shR->setCellValue("B{$fila}", $totalGeneral);
        $shR->setCellValue("C{$fila}", '');
        $shR->getStyle("A{$fila}:C{$fila}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF104E8C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $shR->getRowDimension($fila)->setRowHeight(18);

        $spreadsheet->setActiveSheetIndexByName('Resumen General');

        return $this->descargar($spreadsheet, 'Reporte_General_Permisos_Unidad.xlsx');
    }

    // ─────────────────────────────────────────────────────────────
    //  1. PERMISO PERSONAL
    // ─────────────────────────────────────────────────────────────
    private function excelPersonal($idUnidad, $desde, $hasta): StreamedResponse
    {
        $registros = $this->filtrarPorFechaPermiso(
            $this->filtrarPorUnidad(PermisoPersonal::with('empleado'), $idUnidad),
            $desde, $hasta
        )->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Permisos Personales');

        $cols   = ['#','EMPLEADO','UNIDAD','CARGO','FECHA DOC.','CONDICIÓN',
            'GOCE SALARIAL','FECHA INICIO','FECHA FIN','HORA INICIO','HORA FIN','RAZÓN'];
        $widths = [5, 28, 18, 18, 12, 14, 13, 13, 13, 11, 11, 30];

        $this->cabeceraHoja($sheet, 'REPORTE DE PERMISOS PERSONALES', $desde, $hasta, count($cols));
        $this->escribirEncabezados($sheet, $cols, $widths, 4);

        $fila = 5;
        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $goce        = $p->goce ? 'SÍ' : 'NO';
            $fechaInicio = $p->condicion ? $this->fmt($p->fecha_fraccionado) : $this->fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '' : $this->fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '') : '';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '') : '';

            $sheet->fromArray([
                $i + 1, $p->empleado?->nombre, $p->unidad, $p->cargo,
                $this->fmt($p->fecha), $condicion, $goce,
                $fechaInicio, $fechaFin, $horaInicio, $horaFin, $p->razon,
            ], null, "A{$fila}");

            $this->estiloDatos($sheet, "A{$fila}:L{$fila}", $i % 2 === 0);
            $colorGoce = $p->goce ? 'FF1a7a3a' : 'FFb02020';
            $sheet->getStyle("G{$fila}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => $colorGoce]],
            ]);
            $sheet->getStyle("A{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $fila++;
        }

        $this->filaTotales($sheet, "A{$fila}", count($cols), count($registros));
        return $this->descargar($spreadsheet, 'Reporte_Permisos_Personales_Unidad.xlsx');
    }

    // ─────────────────────────────────────────────────────────────
    //  2. PERMISO COMPENSATORIO
    // ─────────────────────────────────────────────────────────────
    private function excelCompensatorio($idUnidad, $desde, $hasta): StreamedResponse
    {
        $registros = $this->filtrarPorFechaPermiso(
            $this->filtrarPorUnidad(PermisoCompensatorio::with('empleado'), $idUnidad),
            $desde, $hasta
        )->get();

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
        return $this->descargar($spreadsheet, 'Reporte_Permisos_Compensatorios_Unidad.xlsx');
    }

    // ─────────────────────────────────────────────────────────────
    //  3. PERMISO ENFERMEDAD
    // ─────────────────────────────────────────────────────────────
    private function excelEnfermedad($idUnidad, $desde, $hasta): StreamedResponse
    {
        $registros = $this->filtrarPorFechaPermiso(
            $this->filtrarPorUnidad(PermisoEnfermedad::with('empleado'), $idUnidad),
            $desde, $hasta
        )->get();

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
        return $this->descargar($spreadsheet, 'Reporte_Permisos_Enfermedad_Unidad.xlsx');
    }

    // ─────────────────────────────────────────────────────────────
    //  4. PERMISO CONSULTA MÉDICA
    // ─────────────────────────────────────────────────────────────
    private function excelConsultaMedica($idUnidad, $desde, $hasta): StreamedResponse
    {
        $registros = $this->filtrarPorFechaPermiso(
            $this->filtrarPorUnidad(PermisoConsultaMedica::with('empleado'), $idUnidad),
            $desde, $hasta
        )->get();

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
        return $this->descargar($spreadsheet, 'Reporte_Consulta_Medica_Unidad.xlsx');
    }

    // ─────────────────────────────────────────────────────────────
    //  5. PERMISO INCAPACIDAD
    //  (no tiene condicion/fecha_fraccionado: se filtra por
    //  solapamiento directo de fecha_inicio / fecha_fin)
    // ─────────────────────────────────────────────────────────────
    private function excelIncapacidad($idUnidad, $desde, $hasta): StreamedResponse
    {
        $registros = $this->filtrarPorUnidad(
            PermisoIncapacidad::with(['empleado', 'tipoIncapacidad', 'riesgo']),
            $idUnidad
        )
            ->whereDate('fecha_inicio', '<=', $hasta)
            ->whereDate('fecha_fin', '>=', $desde)
            ->orderBy('fecha_inicio')
            ->get();

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

        $letraFin = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($cols));
        $sheet->mergeCells("A{$fila}:{$letraFin}{$fila}");
        $sheet->setCellValue("A{$fila}", 'Total registros: ' . count($registros) . '     |     Total días incapacidad: ' . $registros->sum('dias'));
        $sheet->getStyle("A{$fila}:{$letraFin}{$fila}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 8],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE8E8E8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        return $this->descargar($spreadsheet, 'Reporte_Incapacidades_Unidad.xlsx');
    }

    // ─────────────────────────────────────────────────────────────
    //  6. PERMISO OTROS
    // ─────────────────────────────────────────────────────────────
    private function excelOtros($idUnidad, $desde, $hasta): StreamedResponse
    {
        $registros = $this->filtrarPorFechaPermiso(
            $this->filtrarPorUnidad(PermisoOtro::with('empleado'), $idUnidad),
            $desde, $hasta
        )->get();

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
        return $this->descargar($spreadsheet, 'Reporte_Otros_Permisos_Unidad.xlsx');
    }
}
