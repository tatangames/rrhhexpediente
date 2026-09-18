<?php

namespace App\Http\Controllers\Permiso;

use App\Http\Controllers\Controller;
use App\Models\PermisosEmpleados;
use App\Models\PermisosUnidades;
use App\Models\InformacionGeneral;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PermisoPersonal;
use App\Models\PermisoCompensatorio;
use App\Models\PermisoEnfermedad;
use App\Models\PermisoConsultaMedica;
use App\Models\PermisoIncapacidad;
use App\Models\PermisoOtro;

class ReportesPdfUnidadesPermisosController extends Controller
{
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
    //  RUTA PRINCIPAL: valida, detecta tipo y delega
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
            return $this->pdfTodos(null, $desde, $hasta, $idUnidad);
        }

        return match ($tipo) {
            1 => $this->pdfPersonal(null, $desde, $hasta, $idUnidad),
            2 => $this->pdfCompensatorio(null, $desde, $hasta, $idUnidad),
            3 => $this->pdfEnfermedad(null, $desde, $hasta, $idUnidad),
            4 => $this->pdfConsultaMedica(null, $desde, $hasta, $idUnidad),
            5 => $this->pdfIncapacidad(null, $desde, $hasta, $idUnidad),
            6 => $this->pdfOtros(null, $desde, $hasta, $idUnidad),
        };
    }

    // ─────────────────────────────────────────────────────────────
    //  0. TODOS LOS TIPOS DE PERMISO (un PDF con secciones)
    // ─────────────────────────────────────────────────────────────
    private function pdfTodos($idEmpleado, $desde, $hasta, $idUnidad = null)
    {
        $fmt = fn($fecha) => $fecha ? Carbon::parse($fecha)->format('d-m-Y') : '-';

        $mpdf = $this->mpdfHorizontal('Reporte - Todos los Permisos');
        $html = $this->htmlCabecera('REPORTE GENERAL DE PERMISOS', $desde, $hasta);

        // ── 1. PERSONALES ────────────────────────────────────────
        $personales = $this->filtrarPorFechaPermiso(
            PermisoPersonal::with('empleado')
                ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
                ->when($idUnidad, fn($q) => $q->whereHas('empleado', fn($qq) => $qq->where('id_unidad', $idUnidad))),
            $desde, $hasta
        )->get();

        $html .= "<h4 style='font-size:11px;background:#d0d3d8;padding:4px 6px;margin:10px 0 4px;'>
                1. PERMISOS PERSONALES
              </h4>
              <table width='100%' border='1' cellpadding='3' style='border-collapse:collapse;font-size:8.5px;'>
                <tr style='background:#8a8f97;color:#fff;font-weight:bold;text-align:center;'>
                    <td width='3%'>#</td>
                    <td width='19%'>EMPLEADO</td>
                    <td width='12%'>UNIDAD</td>
                    <td width='12%'>CARGO</td>
                    <td width='10%'>CONDICIÓN</td>
                    <td width='8%'>GOCE</td>
                    <td width='9%'>INICIO</td>
                    <td width='9%'>FIN</td>
                    <td width='7%'>H.INICIO</td>
                    <td width='7%'>H.FIN</td>
                    <td width='11%'>RAZÓN</td>
                </tr>";

        foreach ($personales as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $goce        = $p->goce ? 'SÍ' : 'NO';
            $fechaInicio = $p->condicion ? $fmt($p->fecha_fraccionado) : $fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '-' : $fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg          = $i % 2 === 0 ? '#f9f9f9' : '#fff';
            $html .= "<tr style='background:{$bg};'>
            <td align='center'>" . ($i + 1) . "</td>
            <td>{$p->empleado->nombre}</td><td>{$p->unidad}</td><td>{$p->cargo}</td>
            <td align='center'>{$condicion}</td>
            <td align='center'>{$goce}</td><td align='center'>{$fechaInicio}</td>
            <td align='center'>{$fechaFin}</td><td align='center'>{$horaInicio}</td>
            <td align='center'>{$horaFin}</td><td>{$p->razon}</td>
        </tr>";
        }
        $html .= "</table>" . $this->htmlTotalRegistros(count($personales));

        // ── 2. COMPENSATORIOS ────────────────────────────────────
        $compensatorios = $this->filtrarPorFechaPermiso(
            PermisoCompensatorio::with('empleado')
                ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
                ->when($idUnidad, fn($q) => $q->whereHas('empleado', fn($qq) => $qq->where('id_unidad', $idUnidad))),
            $desde, $hasta
        )->get();

        $html .= "<h4 style='font-size:11px;background:#d0d3d8;padding:4px 6px;margin:10px 0 4px;'>
                2. PERMISOS COMPENSATORIOS
              </h4>
              <table width='100%' border='1' cellpadding='3' style='border-collapse:collapse;font-size:8.5px;'>
                <tr style='background:#8a8f97;color:#fff;font-weight:bold;text-align:center;'>
                    <td width='3%'>#</td>
                    <td width='22%'>EMPLEADO</td>
                    <td width='13%'>UNIDAD</td>
                    <td width='13%'>CARGO</td>
                    <td width='11%'>CONDICIÓN</td>
                    <td width='10%'>INICIO</td>
                    <td width='10%'>FIN</td>
                    <td width='7%'>H.INICIO</td>
                    <td width='7%'>H.FIN</td>
                    <td width='7%'>RAZÓN</td>
                </tr>";

        foreach ($compensatorios as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? $fmt($p->fecha_fraccionado) : $fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '-' : $fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg          = $i % 2 === 0 ? '#f9f9f9' : '#fff';
            $html .= "<tr style='background:{$bg};'>
            <td align='center'>" . ($i + 1) . "</td>
            <td>{$p->empleado->nombre}</td><td>{$p->unidad}</td><td>{$p->cargo}</td>
            <td align='center'>{$condicion}</td>
            <td align='center'>{$fechaInicio}</td><td align='center'>{$fechaFin}</td>
            <td align='center'>{$horaInicio}</td><td align='center'>{$horaFin}</td>
            <td>{$p->razon}</td>
        </tr>";
        }
        $html .= "</table>" . $this->htmlTotalRegistros(count($compensatorios));

        // ── 3. ENFERMEDAD ────────────────────────────────────────
        $enfermedades = $this->filtrarPorFechaPermiso(
            PermisoEnfermedad::with('empleado')
                ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
                ->when($idUnidad, fn($q) => $q->whereHas('empleado', fn($qq) => $qq->where('id_unidad', $idUnidad))),
            $desde, $hasta
        )->get();

        $html .= "<h4 style='font-size:11px;background:#d0d3d8;padding:4px 6px;margin:10px 0 4px;'>
                3. PERMISOS POR ENFERMEDAD
              </h4>
              <table width='100%' border='1' cellpadding='3' style='border-collapse:collapse;font-size:8.5px;'>
                <tr style='background:#8a8f97;color:#fff;font-weight:bold;text-align:center;'>
                    <td width='3%'>#</td>
                    <td width='15%'>EMPLEADO</td>
                    <td width='10%'>UNIDAD</td>
                    <td width='10%'>CARGO</td>
                    <td width='9%'>CONDICIÓN</td>
                    <td width='11%'>UNIDAD ATENCIÓN</td>
                    <td width='11%'>ESPECIALIDAD</td>
                    <td width='11%'>COND. MÉDICA</td>
                    <td width='8%'>INICIO</td>
                    <td width='8%'>FIN</td>
                    <td width='6%'>H.INI</td>
                    <td width='6%'>H.FIN</td>
                </tr>";

        foreach ($enfermedades as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? $fmt($p->fecha_fraccionado) : $fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '-' : $fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg          = $i % 2 === 0 ? '#f9f9f9' : '#fff';
            $html .= "<tr style='background:{$bg};'>
            <td align='center'>" . ($i + 1) . "</td>
            <td>{$p->empleado->nombre}</td><td>{$p->unidad}</td><td>{$p->cargo}</td>
            <td align='center'>{$condicion}</td>
            <td>{$p->unidad_atencion}</td><td>{$p->especialidad}</td><td>{$p->condicion_medica}</td>
            <td align='center'>{$fechaInicio}</td><td align='center'>{$fechaFin}</td>
            <td align='center'>{$horaInicio}</td><td align='center'>{$horaFin}</td>
        </tr>";
        }
        $html .= "</table>" . $this->htmlTotalRegistros(count($enfermedades));

        // ── 4. CONSULTA MÉDICA ───────────────────────────────────
        $consultas = $this->filtrarPorFechaPermiso(
            PermisoConsultaMedica::with('empleado')
                ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
                ->when($idUnidad, fn($q) => $q->whereHas('empleado', fn($qq) => $qq->where('id_unidad', $idUnidad))),
            $desde, $hasta
        )->get();

        $html .= "<h4 style='font-size:11px;background:#d0d3d8;padding:4px 6px;margin:10px 0 4px;'>
                4. PERMISOS - CONSULTA MÉDICA
              </h4>
              <table width='100%' border='1' cellpadding='3' style='border-collapse:collapse;font-size:8.5px;'>
                <tr style='background:#8a8f97;color:#fff;font-weight:bold;text-align:center;'>
                    <td width='3%'>#</td>
                    <td width='15%'>EMPLEADO</td>
                    <td width='10%'>UNIDAD</td>
                    <td width='10%'>CARGO</td>
                    <td width='9%'>CONDICIÓN</td>
                    <td width='11%'>UNIDAD ATENCIÓN</td>
                    <td width='11%'>ESPECIALIDAD</td>
                    <td width='11%'>COND. MÉDICA</td>
                    <td width='8%'>INICIO</td>
                    <td width='8%'>FIN</td>
                    <td width='6%'>H.INI</td>
                    <td width='6%'>H.FIN</td>
                </tr>";

        foreach ($consultas as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? $fmt($p->fecha_fraccionado) : $fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '-' : $fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg          = $i % 2 === 0 ? '#f9f9f9' : '#fff';
            $html .= "<tr style='background:{$bg};'>
            <td align='center'>" . ($i + 1) . "</td>
            <td>{$p->empleado->nombre}</td><td>{$p->unidad}</td><td>{$p->cargo}</td>
            <td align='center'>{$condicion}</td>
            <td>{$p->unidad_atencion}</td><td>{$p->especialidad}</td><td>{$p->condicion_medica}</td>
            <td align='center'>{$fechaInicio}</td><td align='center'>{$fechaFin}</td>
            <td align='center'>{$horaInicio}</td><td align='center'>{$horaFin}</td>
        </tr>";
        }
        $html .= "</table>" . $this->htmlTotalRegistros(count($consultas));

        // ── 5. INCAPACIDADES ─────────────────────────────────────
        // (no tiene condicion/fecha_fraccionado: solapamiento directo)
        $incapacidades = PermisoIncapacidad::with(['empleado', 'tipoIncapacidad', 'riesgo'])
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->when($idUnidad, fn($q) => $q->whereHas('empleado', fn($qq) => $qq->where('id_unidad', $idUnidad)))
            ->whereDate('fecha_inicio', '<=', $hasta)
            ->whereDate('fecha_fin', '>=', $desde)
            ->orderBy('fecha_inicio')
            ->get();

        $html .= "<h4 style='font-size:11px;background:#d0d3d8;padding:4px 6px;margin:10px 0 4px;'>
                5. PERMISOS POR INCAPACIDAD
              </h4>
              <table width='100%' border='1' cellpadding='3' style='border-collapse:collapse;font-size:8.5px;'>
                <tr style='background:#8a8f97;color:#fff;font-weight:bold;text-align:center;'>
                    <td width='3%'>#</td>
                    <td width='17%'>EMPLEADO</td>
                    <td width='10%'>UNIDAD</td>
                    <td width='10%'>CARGO</td>
                    <td width='11%'>TIPO INCAPACIDAD</td>
                    <td width='9%'>RIESGO</td>
                    <td width='12%'>DIAGNÓSTICO</td>
                    <td width='6%'>N° DOC.</td>
                    <td width='8%'>INICIO</td>
                    <td width='8%'>FIN</td>
                    <td width='4%'>DÍAS</td>
                    <td width='10%'>HOSPITALIZ.</td>
                </tr>";

        foreach ($incapacidades as $i => $p) {
            $hospitaliza = $p->hospitalizacion
                ? "SÍ ({$fmt($p->fecha_inicio_hospitalizacion)} al {$fmt($p->fecha_fin_hospitalizacion)})"
                : 'NO';
            $bg = $i % 2 === 0 ? '#f9f9f9' : '#fff';
            $html .= "<tr style='background:{$bg};'>
            <td align='center'>" . ($i + 1) . "</td>
            <td>{$p->empleado?->nombre}</td><td>{$p->unidad}</td><td>{$p->cargo}</td>
            <td>{$p->tipoIncapacidad?->nombre}</td><td>{$p->riesgo?->nombre}</td>
            <td>{$p->diagnostico}</td><td align='center'>{$p->numero}</td>
            <td align='center'>{$fmt($p->fecha_inicio)}</td>
            <td align='center'>{$fmt($p->fecha_fin)}</td>
            <td align='center'>{$p->dias}</td>
            <td align='center'>{$hospitaliza}</td>
        </tr>";
        }
        $totalDias = $incapacidades->sum('dias');
        $html .= "</table>
        <br>
        <table width='28%' border='1' cellpadding='3'
               style='border-collapse:collapse;font-size:8.5px;margin-left:auto;'>
            <tr style='background:#e8e8e8;font-weight:bold;'>
                <td>Total registros</td>
                <td align='center'>" . count($incapacidades) . "</td>
            </tr>
            <tr style='background:#e8e8e8;font-weight:bold;'>
                <td>Total días incapacidad</td>
                <td align='center'>{$totalDias}</td>
            </tr>
        </table>";

        // ── 6. OTROS ─────────────────────────────────────────────
        $otros = $this->filtrarPorFechaPermiso(
            PermisoOtro::with('empleado')
                ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
                ->when($idUnidad, fn($q) => $q->whereHas('empleado', fn($qq) => $qq->where('id_unidad', $idUnidad))),
            $desde, $hasta
        )->get();

        $html .= "<h4 style='font-size:11px;background:#d0d3d8;padding:4px 6px;margin:10px 0 4px;'>
                6. OTROS PERMISOS
              </h4>
              <table width='100%' border='1' cellpadding='3' style='border-collapse:collapse;font-size:8.5px;'>
                <tr style='background:#8a8f97;color:#fff;font-weight:bold;text-align:center;'>
                    <td width='3%'>#</td>
                    <td width='22%'>EMPLEADO</td>
                    <td width='13%'>UNIDAD</td>
                    <td width='13%'>CARGO</td>
                    <td width='11%'>CONDICIÓN</td>
                    <td width='10%'>INICIO</td>
                    <td width='10%'>FIN</td>
                    <td width='7%'>H.INICIO</td>
                    <td width='7%'>H.FIN</td>
                    <td width='7%'>RAZÓN</td>
                </tr>";

        foreach ($otros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? $fmt($p->fecha_fraccionado) : $fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '-' : $fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg          = $i % 2 === 0 ? '#f9f9f9' : '#fff';
            $html .= "<tr style='background:{$bg};'>
            <td align='center'>" . ($i + 1) . "</td>
            <td>{$p->empleado->nombre}</td><td>{$p->unidad}</td><td>{$p->cargo}</td>
            <td align='center'>{$condicion}</td>
            <td align='center'>{$fechaInicio}</td><td align='center'>{$fechaFin}</td>
            <td align='center'>{$horaInicio}</td><td align='center'>{$horaFin}</td>
            <td>{$p->razon}</td>
        </tr>";
        }
        $html .= "</table>" . $this->htmlTotalRegistros(count($otros));

        // ── RESUMEN FINAL ─────────────────────────────────────────
        $totalGeneral = count($personales) + count($compensatorios) + count($enfermedades)
            + count($consultas) + count($incapacidades) + count($otros);

        $html .= "
        <br>
        <table width='35%' border='1' cellpadding='4'
               style='border-collapse:collapse;font-size:9px;margin-left:auto;'>
            <tr style='background:#8a8f97;color:#fff;font-weight:bold;'>
                <td colspan='2' align='center'>RESUMEN GENERAL</td>
            </tr>
            <tr style='background:#f9f9f9;'>
                <td>Permisos Personales</td>
                <td align='center'>" . count($personales) . "</td>
            </tr>
            <tr>
                <td>Permisos Compensatorios</td>
                <td align='center'>" . count($compensatorios) . "</td>
            </tr>
            <tr style='background:#f9f9f9;'>
                <td>Permisos por Enfermedad</td>
                <td align='center'>" . count($enfermedades) . "</td>
            </tr>
            <tr>
                <td>Consultas Médicas</td>
                <td align='center'>" . count($consultas) . "</td>
            </tr>
            <tr style='background:#f9f9f9;'>
                <td>Incapacidades</td>
                <td align='center'>" . count($incapacidades) . "</td>
            </tr>
            <tr>
                <td>Otros Permisos</td>
                <td align='center'>" . count($otros) . "</td>
            </tr>
            <tr style='background:#d0d3d8;font-weight:bold;'>
                <td>TOTAL GENERAL</td>
                <td align='center'>{$totalGeneral}</td>
            </tr>
        </table>";

        $html .= $this->htmlFirma();

        $mpdf->WriteHTML($html);

        return $mpdf->Output('Reporte_General_Permisos.pdf', 'I');
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: mPDF en modo HORIZONTAL (LETTER-L)
    // ─────────────────────────────────────────────────────────────
    private function mpdfHorizontal(string $titulo = 'Reporte Permiso'): \Mpdf\Mpdf
    {
        $mpdf = new \Mpdf\Mpdf([
            'tempDir'       => sys_get_temp_dir(),
            'format'        => 'LETTER-L',
            'orientation'   => 'L',
            'margin_left'   => 12,
            'margin_right'  => 12,
            'margin_top'    => 10,
            'margin_bottom' => 15,
        ]);
        $mpdf->SetTitle($titulo);
        return $mpdf;
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: cabecera institucional
    // ─────────────────────────────────────────────────────────────
    private function htmlCabecera(string $tituloPDF, string $desde, string $hasta): string
    {
        $logo        = public_path('images/logo.png');
        $fechaDesde  = Carbon::parse($desde)->format('d/m/Y');
        $fechaHasta  = Carbon::parse($hasta)->format('d/m/Y');
        $fechaEmisio = now()->format('d/m/Y');

        return "
    <table width='100%' style='border-collapse:collapse; font-family:Arial,sans-serif;'>
        <tr>
            <td style='width:22%; border:0.8px solid #000; padding:5px 8px;'>
                <table width='100%'>
                    <tr>
                        <td style='width:30%;'>
                            <img src='{$logo}' style='height:38px;'>
                        </td>
                        <td style='width:70%; color:#104e8c; font-size:13px; font-weight:bold; line-height:1.3;'>
                            SANTA ANA NORTE<br>EL SALVADOR
                        </td>
                    </tr>
                </table>
            </td>
            <td style='width:56%; border-top:0.8px solid #000; border-bottom:0.8px solid #000;
                        padding:6px 8px; text-align:center; font-size:14px; font-weight:bold;'>
                {$tituloPDF}<br>
                <span style='font-size:11px; font-weight:normal;'>
                    Período: {$fechaDesde} — {$fechaHasta}
                </span>
            </td>
            <td style='width:22%; border:0.8px solid #000; padding:0; vertical-align:middle;'>
                <table width='100%' style='font-size:9px;'>
                    <tr>
                        <td style='border-right:0.8px solid #000; font-size: 13px; padding:3px 5px;'><strong>Emisión:</strong></td>
                        <td style='padding:3px 5px; font-size: 13px; text-align:center;'>{$fechaEmisio}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table><br>";
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: pie con total de registros
    // ─────────────────────────────────────────────────────────────
    private function htmlTotalRegistros(int $total): string
    {
        return "<br><p style='font-size:9px; text-align:right;'>
                    <strong>Total de registros:</strong> {$total}
                </p>";
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: bloque de firma dinámico (desde InformacionGeneral)
    // ─────────────────────────────────────────────────────────────
    private function htmlFirma(): string
    {
        $informacionGeneral = InformacionGeneral::where('id', 1)->first();

        $margenFirma = (int) ($informacionGeneral->px_firmas ?? 40);
        $saltoPagina = (bool) ($informacionGeneral->salto_pagina ?? false);
        $jefe        = $informacionGeneral->jefe ?? '';
        $cargo       = $informacionGeneral->cargo ?? '';
        $area        = $informacionGeneral->area ?? '';

        // Igual que en ReportesController: usar page-break-before en el estilo del div
        $estiloSalto = $saltoPagina ? 'page-break-before: always;' : '';

        return "
        <div style='
            {$estiloSalto}
            padding-top: {$margenFirma}px;
            font-family: Arial, sans-serif;
        '>

            <table width='100%' style='border-collapse: collapse;'>
                <tr>

                    <td width='45%'></td>

                    <td width='38%' style='
                        text-align: center;
                        font-size: 11px;
                    '>

                        __________________________________________<br>

                        <span style='
                            font-weight: bold;
                            font-size: 14px;
                        '>
                            {$jefe}
                        </span><br>

                        <span style='
                            font-weight: normal;
                            font-size: 14px;
                        '>
                            {$cargo}
                        </span><br>

                        <div style='
                            border-top: 1.5px solid #000;
                            margin-top: 5px;
                        '></div>

                        <span style='font-size: 14px;'>
                            {$area}
                        </span>

                    </td>

                    <td width='17%'></td>

                </tr>
            </table>

        </div>
    ";
    }

    // ─────────────────────────────────────────────────────────────
    //  1. PERMISO PERSONAL
    // ─────────────────────────────────────────────────────────────
    private function pdfPersonal($idEmpleado, $desde, $hasta, $idUnidad = null)
    {
        $registros = $this->filtrarPorFechaPermiso(
            PermisoPersonal::with('empleado')
                ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
                ->when($idUnidad, fn($q) => $q->whereHas('empleado', fn($qq) => $qq->where('id_unidad', $idUnidad))),
            $desde, $hasta
        )->get();

        $mpdf = $this->mpdfHorizontal('Reporte - Permisos Personales');
        $html = $this->htmlCabecera('REPORTE DE PERMISOS PERSONALES', $desde, $hasta);

        $html .= "
        <table width='100%' border='1' cellpadding='4' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#8a8f97; color:#fff; font-weight:bold; text-align:center;'>
                <td width='3%'>#</td>
                <td width='19%'>EMPLEADO</td>
                <td width='12%'>UNIDAD</td>
                <td width='12%'>CARGO</td>
                <td width='9%'>CONDICIÓN</td>
                <td width='8%'>GOCE SALARIAL</td>
                <td width='9%'>FECHA INICIO</td>
                <td width='9%'>FECHA FIN</td>
                <td width='6%'>HORA INICIO</td>
                <td width='6%'>HORA FIN</td>
                <td width='10%'>RAZÓN</td>
            </tr>";

        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $goce        = $p->goce ? 'SÍ' : 'NO';
            $fechaInicio = $p->condicion
                ? ($p->fecha_fraccionado ? Carbon::parse($p->fecha_fraccionado)->format('d-m-Y') : '-')
                : ($p->fecha_inicio ? Carbon::parse($p->fecha_inicio)->format('d-m-Y') : '-');
            $fechaFin    = $p->condicion
                ? '-'
                : ($p->fecha_fin ? Carbon::parse($p->fecha_fin)->format('d-m-Y') : '-');
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg          = $i % 2 === 0 ? '#f9f9f9' : '#fff';

            $html .= "
            <tr style='background:{$bg};'>
                <td align='center'>" . ($i + 1) . "</td>
                <td>{$p->empleado->nombre}</td>
                <td>{$p->unidad}</td>
                <td>{$p->cargo}</td>
                <td align='center'>{$condicion}</td>
                <td align='center'>{$goce}</td>
                <td align='center'>{$fechaInicio}</td>
                <td align='center'>{$fechaFin}</td>
                <td align='center'>{$horaInicio}</td>
                <td align='center'>{$horaFin}</td>
                <td>{$p->razon}</td>
            </tr>";
        }

        $html .= "</table>" . $this->htmlTotalRegistros(count($registros));
        $html .= $this->htmlFirma();
        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Permisos_Personales.pdf', 'I');
    }

    // ─────────────────────────────────────────────────────────────
    //  2. PERMISO COMPENSATORIO
    // ─────────────────────────────────────────────────────────────
    private function pdfCompensatorio($idEmpleado, $desde, $hasta, $idUnidad = null)
    {
        $registros = $this->filtrarPorFechaPermiso(
            PermisoCompensatorio::with('empleado')
                ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
                ->when($idUnidad, fn($q) => $q->whereHas('empleado', fn($qq) => $qq->where('id_unidad', $idUnidad))),
            $desde, $hasta
        )->get();

        $mpdf = $this->mpdfHorizontal('Reporte - Permisos Compensatorios');
        $html = $this->htmlCabecera('REPORTE DE PERMISOS COMPENSATORIOS', $desde, $hasta);

        $html .= "
        <table width='100%' border='1' cellpadding='4' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#8a8f97; color:#fff; font-weight:bold; text-align:center;'>
                <td width='3%'>#</td>
                <td width='21%'>EMPLEADO</td>
                <td width='14%'>UNIDAD</td>
                <td width='14%'>CARGO</td>
                <td width='11%'>CONDICIÓN</td>
                <td width='10%'>FECHA INICIO</td>
                <td width='10%'>FECHA FIN</td>
                <td width='6%'>HORA INICIO</td>
                <td width='6%'>HORA FIN</td>
                <td width='8%'>RAZÓN</td>
            </tr>";

        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion
                ? ($p->fecha_fraccionado ? Carbon::parse($p->fecha_fraccionado)->format('d-m-Y') : '-')
                : ($p->fecha_inicio ? Carbon::parse($p->fecha_inicio)->format('d-m-Y') : '-');
            $fechaFin    = $p->condicion
                ? '-'
                : ($p->fecha_fin ? Carbon::parse($p->fecha_fin)->format('d-m-Y') : '-');
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg          = $i % 2 === 0 ? '#f9f9f9' : '#fff';

            $html .= "
            <tr style='background:{$bg};'>
                <td align='center'>" . ($i + 1) . "</td>
                <td>{$p->empleado->nombre}</td>
                <td>{$p->unidad}</td>
                <td>{$p->cargo}</td>
                <td align='center'>{$condicion}</td>
                <td align='center'>{$fechaInicio}</td>
                <td align='center'>{$fechaFin}</td>
                <td align='center'>{$horaInicio}</td>
                <td align='center'>{$horaFin}</td>
                <td>{$p->razon}</td>
            </tr>";
        }

        $html .= "</table>" . $this->htmlTotalRegistros(count($registros));
        $html .= $this->htmlFirma();
        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Permisos_Compensatorios.pdf', 'I');
    }

    // ─────────────────────────────────────────────────────────────
    //  3. PERMISO ENFERMEDAD
    // ─────────────────────────────────────────────────────────────
    private function pdfEnfermedad($idEmpleado, $desde, $hasta, $idUnidad = null)
    {
        $registros = $this->filtrarPorFechaPermiso(
            PermisoEnfermedad::with('empleado')
                ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
                ->when($idUnidad, fn($q) => $q->whereHas('empleado', fn($qq) => $qq->where('id_unidad', $idUnidad))),
            $desde, $hasta
        )->get();

        $fmt = fn($fecha) => $fecha ? Carbon::parse($fecha)->format('d-m-Y') : '-';

        $mpdf = $this->mpdfHorizontal('Reporte - Permisos por Enfermedad');
        $html = $this->htmlCabecera('REPORTE DE PERMISOS POR ENFERMEDAD', $desde, $hasta);

        $html .= "
        <table width='100%' border='1' cellpadding='4' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#8a8f97; color:#fff; font-weight:bold; text-align:center;'>
                <td width='3%'>#</td>
                <td width='16%'>EMPLEADO</td>
                <td width='11%'>UNIDAD</td>
                <td width='11%'>CARGO</td>
                <td width='9%'>CONDICIÓN</td>
                <td width='11%'>UNIDAD ATENCIÓN</td>
                <td width='11%'>ESPECIALIDAD</td>
                <td width='11%'>COND. MÉDICA</td>
                <td width='8%'>INICIO</td>
                <td width='8%'>FIN</td>
                <td width='7%'>H. INICIO</td>
                <td width='7%'>H. FIN</td>
            </tr>";

        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? $fmt($p->fecha_fraccionado) : $fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '-' : $fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg          = $i % 2 === 0 ? '#f9f9f9' : '#fff';

            $html .= "
            <tr style='background:{$bg};'>
                <td align='center'>" . ($i + 1) . "</td>
                <td>{$p->empleado->nombre}</td>
                <td>{$p->unidad}</td>
                <td>{$p->cargo}</td>
                <td align='center'>{$condicion}</td>
                <td>{$p->unidad_atencion}</td>
                <td>{$p->especialidad}</td>
                <td>{$p->condicion_medica}</td>
                <td align='center'>{$fechaInicio}</td>
                <td align='center'>{$fechaFin}</td>
                <td align='center'>{$horaInicio}</td>
                <td align='center'>{$horaFin}</td>
            </tr>";
        }

        $html .= "</table>" . $this->htmlTotalRegistros(count($registros));
        $html .= $this->htmlFirma();
        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Permisos_Enfermedad.pdf', 'I');
    }

    // ─────────────────────────────────────────────────────────────
    //  4. PERMISO CONSULTA MÉDICA
    // ─────────────────────────────────────────────────────────────
    private function pdfConsultaMedica($idEmpleado, $desde, $hasta, $idUnidad = null)
    {
        $registros = $this->filtrarPorFechaPermiso(
            PermisoConsultaMedica::with('empleado')
                ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
                ->when($idUnidad, fn($q) => $q->whereHas('empleado', fn($qq) => $qq->where('id_unidad', $idUnidad))),
            $desde, $hasta
        )->get();

        $fmt = fn($fecha) => $fecha ? Carbon::parse($fecha)->format('d-m-Y') : '-';

        $mpdf = $this->mpdfHorizontal('Reporte - Consulta Médica');
        $html = $this->htmlCabecera('REPORTE DE PERMISOS - CONSULTA MÉDICA', $desde, $hasta);

        $html .= "
        <table width='100%' border='1' cellpadding='4' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#8a8f97; color:#fff; font-weight:bold; text-align:center;'>
                <td width='3%'>#</td>
                <td width='16%'>EMPLEADO</td>
                <td width='11%'>UNIDAD</td>
                <td width='11%'>CARGO</td>
                <td width='9%'>CONDICIÓN</td>
                <td width='11%'>UNIDAD ATENCIÓN</td>
                <td width='11%'>ESPECIALIDAD</td>
                <td width='11%'>COND. MÉDICA</td>
                <td width='8%'>INICIO</td>
                <td width='8%'>FIN</td>
                <td width='7%'>H. INICIO</td>
                <td width='7%'>H. FIN</td>
            </tr>";

        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? $fmt($p->fecha_fraccionado) : $fmt($p->fecha_inicio);
            $fechaFin    = $p->condicion ? '-' : $fmt($p->fecha_fin);
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg          = $i % 2 === 0 ? '#f9f9f9' : '#fff';

            $html .= "
            <tr style='background:{$bg};'>
                <td align='center'>" . ($i + 1) . "</td>
                <td>{$p->empleado->nombre}</td>
                <td>{$p->unidad}</td>
                <td>{$p->cargo}</td>
                <td align='center'>{$condicion}</td>
                <td>{$p->unidad_atencion}</td>
                <td>{$p->especialidad}</td>
                <td>{$p->condicion_medica}</td>
                <td align='center'>{$fechaInicio}</td>
                <td align='center'>{$fechaFin}</td>
                <td align='center'>{$horaInicio}</td>
                <td align='center'>{$horaFin}</td>
            </tr>";
        }

        $html .= "</table>" . $this->htmlTotalRegistros(count($registros));
        $html .= $this->htmlFirma();
        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Consulta_Medica.pdf', 'I');
    }

    // ─────────────────────────────────────────────────────────────
    //  5. PERMISO INCAPACIDAD
    //  (no tiene condicion/fecha_fraccionado: se filtra por
    //  solapamiento directo de fecha_inicio / fecha_fin)
    // ─────────────────────────────────────────────────────────────
    private function pdfIncapacidad($idEmpleado, $desde, $hasta, $idUnidad = null)
    {
        $registros = PermisoIncapacidad::with(['empleado', 'tipoIncapacidad', 'riesgo'])
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->when($idUnidad, fn($q) => $q->whereHas('empleado', fn($qq) => $qq->where('id_unidad', $idUnidad)))
            ->whereDate('fecha_inicio', '<=', $hasta)
            ->whereDate('fecha_fin', '>=', $desde)
            ->orderBy('fecha_inicio')
            ->get();

        $fmt = fn($fecha) => $fecha ? Carbon::parse($fecha)->format('d-m-Y') : '';

        $mpdf = $this->mpdfHorizontal('Reporte - Incapacidades');
        $html = $this->htmlCabecera('REPORTE DE PERMISOS POR INCAPACIDAD', $desde, $hasta);

        $html .= "
        <table width='100%' border='1' cellpadding='4' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#8a8f97; color:#fff; font-weight:bold; text-align:center;'>
                <td width='3%'>#</td>
                <td width='18%'>EMPLEADO</td>
                <td width='11%'>UNIDAD</td>
                <td width='11%'>CARGO</td>
                <td width='11%'>TIPO INCAPACIDAD</td>
                <td width='9%'>RIESGO</td>
                <td width='13%'>DIAGNÓSTICO</td>
                <td width='6%'>N° DOC.</td>
                <td width='8%'>INICIO</td>
                <td width='8%'>FIN</td>
                <td width='4%'>DÍAS</td>
                <td width='11%'>HOSPITALIZACIÓN</td>
            </tr>";

        foreach ($registros as $i => $p) {
            $hospitaliza = $p->hospitalizacion
                ? "SÍ ({$fmt($p->fecha_inicio_hospitalizacion)} al {$fmt($p->fecha_fin_hospitalizacion)})"
                : 'NO';
            $bg = $i % 2 === 0 ? '#f9f9f9' : '#fff';

            $html .= "
            <tr style='background:{$bg};'>
                <td align='center'>" . ($i + 1) . "</td>
                <td>{$p->empleado?->nombre}</td>
                <td>{$p->unidad}</td>
                <td>{$p->cargo}</td>
                <td>{$p->tipoIncapacidad?->nombre}</td>
                <td>{$p->riesgo?->nombre}</td>
                <td>{$p->diagnostico}</td>
                <td align='center'>{$p->numero}</td>
                <td align='center'>{$fmt($p->fecha_inicio)}</td>
                <td align='center'>{$fmt($p->fecha_fin)}</td>
                <td align='center'>{$p->dias}</td>
                <td align='center'>{$hospitaliza}</td>
            </tr>";
        }

        $totalDias = $registros->sum('dias');

        $html .= "</table>
            <br>
            <table width='28%' border='1' cellpadding='4'
                   style='border-collapse:collapse;font-size:9px;margin-left:auto;'>
                <tr style='background:#e8e8e8; font-weight:bold;'>
                    <td>Total registros</td>
                    <td align='center'>" . count($registros) . "</td>
                </tr>
                <tr style='background:#e8e8e8; font-weight:bold;'>
                    <td>Total días incapacidad</td>
                    <td align='center'>{$totalDias}</td>
                </tr>
            </table>";

        $html .= $this->htmlFirma();
        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Incapacidades.pdf', 'I');
    }

    // ─────────────────────────────────────────────────────────────
    //  6. PERMISO OTROS
    // ─────────────────────────────────────────────────────────────
    private function pdfOtros($idEmpleado, $desde, $hasta, $idUnidad = null)
    {
        $registros = $this->filtrarPorFechaPermiso(
            PermisoOtro::with('empleado')
                ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
                ->when($idUnidad, fn($q) => $q->whereHas('empleado', fn($qq) => $qq->where('id_unidad', $idUnidad))),
            $desde, $hasta
        )->get();

        $mpdf = $this->mpdfHorizontal('Reporte - Otros Permisos');
        $html = $this->htmlCabecera('REPORTE DE OTROS PERMISOS', $desde, $hasta);

        $html .= "
        <table width='100%' border='1' cellpadding='4' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#8a8f97; color:#fff; font-weight:bold; text-align:center;'>
                <td width='3%'>#</td>
                <td width='21%'>EMPLEADO</td>
                <td width='14%'>UNIDAD</td>
                <td width='14%'>CARGO</td>
                <td width='11%'>CONDICIÓN</td>
                <td width='10%'>FECHA INICIO</td>
                <td width='10%'>FECHA FIN</td>
                <td width='6%'>HORA INICIO</td>
                <td width='6%'>HORA FIN</td>
                <td width='8%'>RAZÓN</td>
            </tr>";

        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion
                ? ($p->fecha_fraccionado ? Carbon::parse($p->fecha_fraccionado)->format('d-m-Y') : '-')
                : ($p->fecha_inicio ? Carbon::parse($p->fecha_inicio)->format('d-m-Y') : '-');
            $fechaFin    = $p->condicion
                ? '-'
                : ($p->fecha_fin ? Carbon::parse($p->fecha_fin)->format('d-m-Y') : '-');
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg          = $i % 2 === 0 ? '#f9f9f9' : '#fff';

            $html .= "
            <tr style='background:{$bg};'>
                <td align='center'>" . ($i + 1) . "</td>
                <td>{$p->empleado->nombre}</td>
                <td>{$p->unidad}</td>
                <td>{$p->cargo}</td>
                <td align='center'>{$condicion}</td>
                <td align='center'>{$fechaInicio}</td>
                <td align='center'>{$fechaFin}</td>
                <td align='center'>{$horaInicio}</td>
                <td align='center'>{$horaFin}</td>
                <td>{$p->razon}</td>
            </tr>";
        }

        $html .= "</table>" . $this->htmlTotalRegistros(count($registros));
        $html .= $this->htmlFirma();
        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Otros_Permisos.pdf', 'I');
    }
}
