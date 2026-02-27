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

class ReportesPermisoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getTemaPredeterminado()
    {
        return Auth::guard('admin')->user()->tema;
    }

    public function indexReportesGeneral()
    {
        $temaPredeterminado = $this->getTemaPredeterminado();
        $arrayEmpleados     = Empleado::orderBy('nombre', 'ASC')->get();

        return view('backend.permisos.reportes.vistageneralreportes',
            compact('temaPredeterminado', 'arrayEmpleados'));
    }

    // ─────────────────────────────────────────────────────────────
    //  RUTA PRINCIPAL: valida, detecta tipo y delega
    // ─────────────────────────────────────────────────────────────
    public function generarReportePermisoPDF(Request $request)
    {
        // Validación backend — las fechas son obligatorias
        $request->validate([
            'tipo_permiso' => 'required|integer|between:1,6',
            'fecha_desde'  => 'required|date',
            'fecha_hasta'  => 'required|date|after_or_equal:fecha_desde',
            'id_empleado'  => 'nullable|integer|exists:empleados,id',
        ], [
            'fecha_desde.required'          => 'La fecha de inicio es requerida.',
            'fecha_hasta.required'          => 'La fecha de fin es requerida.',
            'fecha_hasta.after_or_equal'    => 'La fecha "Hasta" debe ser mayor o igual a "Desde".',
            'tipo_permiso.required'         => 'Seleccione el tipo de permiso.',
        ]);

        $tipo       = (int) $request->tipo_permiso;
        $idEmpleado = $request->id_empleado;
        $desde      = $request->fecha_desde;
        $hasta      = $request->fecha_hasta;

        return match ($tipo) {
            1 => $this->pdfPersonal($idEmpleado, $desde, $hasta),
            2 => $this->pdfCompensatorio($idEmpleado, $desde, $hasta),
            3 => $this->pdfEnfermedad($idEmpleado, $desde, $hasta),
            4 => $this->pdfConsultaMedica($idEmpleado, $desde, $hasta),
            5 => $this->pdfIncapacidad($idEmpleado, $desde, $hasta),
            6 => $this->pdfOtros($idEmpleado, $desde, $hasta),
        };
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
                    <span style='font-size:10px; font-weight:normal;'>
                        Período: {$fechaDesde} — {$fechaHasta}
                    </span>
                </td>
                <td style='width:22%; border:0.8px solid #000; padding:0; vertical-align:top;'>
                    <table width='100%' style='font-size:9px;'>
                        <tr>
                            <td style='border-right:0.8px solid #000; border-bottom:0.8px solid #000; padding:3px 5px;'><strong>Código:</strong></td>
                            <td style='border-bottom:0.8px solid #000; padding:3px 5px; text-align:center;'>TALE-001-FICH</td>
                        </tr>
                        <tr>
                            <td style='border-right:0.8px solid #000; border-bottom:0.8px solid #000; padding:3px 5px;'><strong>Versión:</strong></td>
                            <td style='border-bottom:0.8px solid #000; padding:3px 5px; text-align:center;'>000</td>
                        </tr>
                        <tr>
                            <td style='border-right:0.8px solid #000; padding:3px 5px;'><strong>Emisión:</strong></td>
                            <td style='padding:3px 5px; text-align:center;'>{$fechaEmisio}</td>
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
    //  1. PERMISO PERSONAL
    // ─────────────────────────────────────────────────────────────
    private function pdfPersonal($idEmpleado, $desde, $hasta)
    {
        $registros = PermisoPersonal::with('empleado')
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha')
            ->get();

        $mpdf = $this->mpdfHorizontal('Reporte - Permisos Personales');
        $html = $this->htmlCabecera('REPORTE DE PERMISOS PERSONALES', $desde, $hasta);

        $html .= "
        <table width='100%' border='1' cellpadding='4' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#8a8f97; color:#fff; font-weight:bold; text-align:center;'>
                <td width='3%'>#</td>
                <td width='18%'>EMPLEADO</td>
                <td width='12%'>UNIDAD</td>
                <td width='12%'>CARGO</td>
                <td width='7%'>FECHA DOC.</td>
                <td width='9%'>CONDICIÓN</td>
                <td width='8%'>GOCE SALARIAL</td>
                <td width='8%'>FECHA INICIO</td>
                <td width='8%'>FECHA FIN</td>
                <td width='6%'>HORA INICIO</td>
                <td width='6%'>HORA FIN</td>
                <td width='10%'>RAZÓN</td>
            </tr>";

        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $goce        = $p->goce ? 'SÍ' : 'NO';
            $fechaInicio = $p->condicion ? ($p->fecha_fraccionado ?? '-') : ($p->fecha_inicio ?? '-');
            $fechaFin    = $p->condicion ? '-' : ($p->fecha_fin ?? '-');
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg          = $i % 2 === 0 ? '#f9f9f9' : '#fff';

            $html .= "
            <tr style='background:{$bg};'>
                <td align='center'>" . ($i + 1) . "</td>
                <td>{$p->empleado->nombre}</td>
                <td>{$p->unidad}</td>
                <td>{$p->cargo}</td>
                <td align='center'>{$p->fecha}</td>
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
        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Permisos_Personales.pdf', 'I');
    }

    // ─────────────────────────────────────────────────────────────
    //  2. PERMISO COMPENSATORIO
    // ─────────────────────────────────────────────────────────────
    private function pdfCompensatorio($idEmpleado, $desde, $hasta)
    {
        $registros = PermisoCompensatorio::with('empleado')
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha')
            ->get();

        $mpdf = $this->mpdfHorizontal('Reporte - Permisos Compensatorios');
        $html = $this->htmlCabecera('REPORTE DE PERMISOS COMPENSATORIOS', $desde, $hasta);

        $html .= "
        <table width='100%' border='1' cellpadding='4' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#8a8f97; color:#fff; font-weight:bold; text-align:center;'>
                <td width='3%'>#</td>
                <td width='20%'>EMPLEADO</td>
                <td width='13%'>UNIDAD</td>
                <td width='13%'>CARGO</td>
                <td width='8%'>FECHA DOC.</td>
                <td width='10%'>CONDICIÓN</td>
                <td width='9%'>FECHA INICIO</td>
                <td width='9%'>FECHA FIN</td>
                <td width='6%'>HORA INICIO</td>
                <td width='6%'>HORA FIN</td>
                <td width='10%'>RAZÓN</td>
            </tr>";

        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? ($p->fecha_fraccionado ?? '-') : ($p->fecha_inicio ?? '-');
            $fechaFin    = $p->condicion ? '-' : ($p->fecha_fin ?? '-');
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg          = $i % 2 === 0 ? '#f9f9f9' : '#fff';

            $html .= "
            <tr style='background:{$bg};'>
                <td align='center'>" . ($i + 1) . "</td>
                <td>{$p->empleado->nombre}</td>
                <td>{$p->unidad}</td>
                <td>{$p->cargo}</td>
                <td align='center'>{$p->fecha}</td>
                <td align='center'>{$condicion}</td>
                <td align='center'>{$fechaInicio}</td>
                <td align='center'>{$fechaFin}</td>
                <td align='center'>{$horaInicio}</td>
                <td align='center'>{$horaFin}</td>
                <td>{$p->razon}</td>
            </tr>";
        }

        $html .= "</table>" . $this->htmlTotalRegistros(count($registros));
        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Permisos_Compensatorios.pdf', 'I');
    }

    // ─────────────────────────────────────────────────────────────
    //  3. PERMISO ENFERMEDAD
    // ─────────────────────────────────────────────────────────────
    private function pdfEnfermedad($idEmpleado, $desde, $hasta)
    {
        $registros = PermisoEnfermedad::with('empleado')
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha')
            ->get();

        $mpdf = $this->mpdfHorizontal('Reporte - Permisos por Enfermedad');
        $html = $this->htmlCabecera('REPORTE DE PERMISOS POR ENFERMEDAD', $desde, $hasta);

        $html .= "
        <table width='100%' border='1' cellpadding='4' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#8a8f97; color:#fff; font-weight:bold; text-align:center;'>
                <td width='3%'>#</td>
                <td width='15%'>EMPLEADO</td>
                <td width='10%'>UNIDAD</td>
                <td width='10%'>CARGO</td>
                <td width='7%'>FECHA DOC.</td>
                <td width='8%'>CONDICIÓN</td>
                <td width='10%'>UNIDAD ATENCIÓN</td>
                <td width='10%'>ESPECIALIDAD</td>
                <td width='10%'>COND. MÉDICA</td>
                <td width='7%'>INICIO</td>
                <td width='7%'>FIN</td>
                <td width='6%'>H. INICIO</td>
                <td width='6%'>H. FIN</td>
            </tr>";

        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? ($p->fecha_fraccionado ?? '-') : ($p->fecha_inicio ?? '-');
            $fechaFin    = $p->condicion ? '-' : ($p->fecha_fin ?? '-');
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg          = $i % 2 === 0 ? '#f9f9f9' : '#fff';

            $html .= "
            <tr style='background:{$bg};'>
                <td align='center'>" . ($i + 1) . "</td>
                <td>{$p->empleado->nombre}</td>
                <td>{$p->unidad}</td>
                <td>{$p->cargo}</td>
                <td align='center'>{$p->fecha}</td>
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
        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Permisos_Enfermedad.pdf', 'I');
    }

    // ─────────────────────────────────────────────────────────────
    //  4. PERMISO CONSULTA MÉDICA
    // ─────────────────────────────────────────────────────────────
    private function pdfConsultaMedica($idEmpleado, $desde, $hasta)
    {
        $registros = PermisoConsultaMedica::with('empleado')
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha')
            ->get();

        $mpdf = $this->mpdfHorizontal('Reporte - Consulta Médica');
        $html = $this->htmlCabecera('REPORTE DE PERMISOS - CONSULTA MÉDICA', $desde, $hasta);

        $html .= "
        <table width='100%' border='1' cellpadding='4' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#8a8f97; color:#fff; font-weight:bold; text-align:center;'>
                <td width='3%'>#</td>
                <td width='15%'>EMPLEADO</td>
                <td width='10%'>UNIDAD</td>
                <td width='10%'>CARGO</td>
                <td width='7%'>FECHA DOC.</td>
                <td width='8%'>CONDICIÓN</td>
                <td width='10%'>UNIDAD ATENCIÓN</td>
                <td width='10%'>ESPECIALIDAD</td>
                <td width='10%'>COND. MÉDICA</td>
                <td width='7%'>INICIO</td>
                <td width='7%'>FIN</td>
                <td width='6%'>H. INICIO</td>
                <td width='6%'>H. FIN</td>
            </tr>";

        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? ($p->fecha_fraccionado ?? '-') : ($p->fecha_inicio ?? '-');
            $fechaFin    = $p->condicion ? '-' : ($p->fecha_fin ?? '-');
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg          = $i % 2 === 0 ? '#f9f9f9' : '#fff';

            $html .= "
            <tr style='background:{$bg};'>
                <td align='center'>" . ($i + 1) . "</td>
                <td>{$p->empleado->nombre}</td>
                <td>{$p->unidad}</td>
                <td>{$p->cargo}</td>
                <td align='center'>{$p->fecha}</td>
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
        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Consulta_Medica.pdf', 'I');
    }

    // ─────────────────────────────────────────────────────────────
    //  5. PERMISO INCAPACIDAD
    // ─────────────────────────────────────────────────────────────
    private function pdfIncapacidad($idEmpleado, $desde, $hasta)
    {
        $registros = PermisoIncapacidad::with(['empleado', 'tipoIncapacidad', 'riesgo'])
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha')
            ->get();

        // Helper: formatea fecha d-m-Y o retorna vacío si es null
        $fmt = fn($fecha) => $fecha ? \Carbon\Carbon::parse($fecha)->format('d-m-Y') : '';

        $mpdf = $this->mpdfHorizontal('Reporte - Incapacidades');
        $html = $this->htmlCabecera('REPORTE DE PERMISOS POR INCAPACIDAD', $desde, $hasta);

        $html .= "
    <table width='100%' border='1' cellpadding='4' style='border-collapse:collapse;font-size:9px;'>
        <tr style='background:#8a8f97; color:#fff; font-weight:bold; text-align:center;'>
            <td width='3%'>#</td>
            <td width='17%'>EMPLEADO</td>
            <td width='10%'>UNIDAD</td>
            <td width='10%'>CARGO</td>
            <td width='7%'>FECHA DOC.</td>
            <td width='10%'>TIPO INCAPACIDAD</td>
            <td width='8%'>RIESGO</td>
            <td width='12%'>DIAGNÓSTICO</td>
            <td width='5%'>N° DOC.</td>
            <td width='7%'>INICIO</td>
            <td width='7%'>FIN</td>
            <td width='4%'>DÍAS</td>
            <td width='10%'>HOSPITALIZACIÓN</td>
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
            <td align='center'>{$fmt($p->fecha)}</td>
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

        $html .= "</table>";
        $html .= "
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

        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Incapacidades.pdf', 'I');
    }

    // ─────────────────────────────────────────────────────────────
    //  6. PERMISO OTROS
    // ─────────────────────────────────────────────────────────────
    private function pdfOtros($idEmpleado, $desde, $hasta)
    {
        $registros = PermisoOtro::with('empleado')
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha')
            ->get();

        $mpdf = $this->mpdfHorizontal('Reporte - Otros Permisos');
        $html = $this->htmlCabecera('REPORTE DE OTROS PERMISOS', $desde, $hasta);

        $html .= "
        <table width='100%' border='1' cellpadding='4' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#8a8f97; color:#fff; font-weight:bold; text-align:center;'>
                <td width='3%'>#</td>
                <td width='20%'>EMPLEADO</td>
                <td width='13%'>UNIDAD</td>
                <td width='13%'>CARGO</td>
                <td width='8%'>FECHA DOC.</td>
                <td width='10%'>CONDICIÓN</td>
                <td width='9%'>FECHA INICIO</td>
                <td width='9%'>FECHA FIN</td>
                <td width='6%'>HORA INICIO</td>
                <td width='6%'>HORA FIN</td>
                <td width='10%'>RAZÓN</td>
            </tr>";

        foreach ($registros as $i => $p) {
            $condicion   = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $fechaInicio = $p->condicion ? ($p->fecha_fraccionado ?? '-') : ($p->fecha_inicio ?? '-');
            $fechaFin    = $p->condicion ? '-' : ($p->fecha_fin ?? '-');
            $horaInicio  = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin     = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg          = $i % 2 === 0 ? '#f9f9f9' : '#fff';

            $html .= "
            <tr style='background:{$bg};'>
                <td align='center'>" . ($i + 1) . "</td>
                <td>{$p->empleado->nombre}</td>
                <td>{$p->unidad}</td>
                <td>{$p->cargo}</td>
                <td align='center'>{$p->fecha}</td>
                <td align='center'>{$condicion}</td>
                <td align='center'>{$fechaInicio}</td>
                <td align='center'>{$fechaFin}</td>
                <td align='center'>{$horaInicio}</td>
                <td align='center'>{$horaFin}</td>
                <td>{$p->razon}</td>
            </tr>";
        }

        $html .= "</table>" . $this->htmlTotalRegistros(count($registros));
        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Otros_Permisos.pdf', 'I');
    }
}
