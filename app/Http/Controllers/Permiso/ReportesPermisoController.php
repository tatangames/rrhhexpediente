<?php

namespace App\Http\Controllers\Permiso;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportesPermisoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    private function getTemaPredeterminado(){
        return Auth::guard('admin')->user()->tema;
    }


    public function indexReportesGeneral(){
        $temaPredeterminado =  $this->getTemaPredeterminado();

        $arrayEmpleados = Empleado::orderBy('nombre', 'ASC')->get();

        return view('backend.permisos.reportes.vistageneralreportes', compact('temaPredeterminado', 'arrayEmpleados'));
    }


    // ─────────────────────────────────────────────────────────────
    //  Helper: instancia mPDF en modo HORIZONTAL (LETTER-L)
    // ─────────────────────────────────────────────────────────────
    private function mpdfHorizontal(string $titulo = 'Reporte Permiso'): \Mpdf\Mpdf
    {
        $mpdf = new \Mpdf\Mpdf([
            'tempDir'       => sys_get_temp_dir(),
            'format'        => 'LETTER-L',   // LETTER en modo landscape
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
    //  Helper: cabecera institucional (igual en todos los PDFs)
    // ─────────────────────────────────────────────────────────────
    private function htmlCabecera(string $tituloPDF): string
    {
        $logo = public_path('images/gobiernologo.jpg');
        $fecha = now()->format('d/m/Y');

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
                    {$tituloPDF}
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
                            <td style='border-right:0.8px solid #000; padding:3px 5px;'><strong>Vigencia:</strong></td>
                            <td style='padding:3px 5px; text-align:center;'>{$fecha}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table><br>";
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: fila de condición (Día completo / Fraccionado)
    // ─────────────────────────────────────────────────────────────
    private function htmlCondicion(object $permiso): string
    {
        $esDiaCompleto  = !$permiso->condicion;  // 0 = Día completo
        $esFraccionado  = $permiso->condicion;   // 1 = Fraccionado

        $checkDC = $esDiaCompleto ? '&#10003;' : '';
        $checkFR = $esFraccionado ? '&#10003;' : '';

        $desde  = $esDiaCompleto ? ($permiso->fecha_inicio ?? '-') : '-';
        $hasta  = $esDiaCompleto ? ($permiso->fecha_fin    ?? '-') : '-';
        $diaFR  = $esFraccionado ? ($permiso->fecha_fraccionado ?? '-') : '-';
        $horaI  = $esFraccionado ? ($permiso->hora_inicio ?? '-') : '-';
        $horaF  = $esFraccionado ? ($permiso->hora_fin    ?? '-') : '-';

        return "
        <table width='100%' border='1' cellpadding='4' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#e8e8e8; font-weight:bold; text-align:center;'>
                <td width='5%'>CONDICIÓN</td>
                <td width='20%'>FECHA INICIO</td>
                <td width='20%'>FECHA FIN</td>
                <td width='5%'>FRACCIONADO</td>
                <td width='20%'>FECHA</td>
                <td width='15%'>HORA INICIO</td>
                <td width='15%'>HORA FIN</td>
            </tr>
            <tr style='text-align:center;'>
                <td>{$checkDC} Día Completo</td>
                <td>{$desde}</td>
                <td>{$hasta}</td>
                <td>{$checkFR} Sí</td>
                <td>{$diaFR}</td>
                <td>{$horaI}</td>
                <td>{$horaF}</td>
            </tr>
        </table>";
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: sección de firma
    // ─────────────────────────────────────────────────────────────
    private function htmlFirmas(): string
    {
        return "
        <br><br>
        <table width='100%' style='font-size:9px; font-family:Arial,sans-serif;'>
            <tr>
                <td width='30%' align='center'>
                    <div style='border-top:1px solid #000; margin-top:40px; padding-top:4px;'>
                        Firma del Empleado
                    </div>
                </td>
                <td width='10%'></td>
                <td width='30%' align='center'>
                    <div style='border-top:1px solid #000; margin-top:40px; padding-top:4px;'>
                        Jefe Inmediato
                    </div>
                </td>
                <td width='10%'></td>
                <td width='30%' align='center'>
                    <div style='border-top:1px solid #000; margin-top:40px; padding-top:4px;'>
                        Recursos Humanos
                    </div>
                </td>
            </tr>
        </table>";
    }

    // ─────────────────────────────────────────────────────────────
    //  RUTA PRINCIPAL: detecta tipo y delega
    // ─────────────────────────────────────────────────────────────
    public function generarReportePermisoPDF(Request $request)
    {
        $tipo       = (int) $request->tipo_permiso;   // 1-6
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
            default => abort(400, 'Tipo de permiso no válido'),
        };
    }

    // ─────────────────────────────────────────────────────────────
    //  1. PERMISO PERSONAL
    // ─────────────────────────────────────────────────────────────
    private function pdfPersonal($idEmpleado, $desde, $hasta)
    {
        $query = PermisoPersonal::with('empleado')
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->when($desde,      fn($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta,      fn($q) => $q->whereDate('fecha', '<=', $hasta))
            ->orderBy('fecha')
            ->get();

        $mpdf = $this->mpdfHorizontal('Reporte - Permisos Personales');
        $html = $this->htmlCabecera('REPORTE DE PERMISOS PERSONALES');

        // Tabla resumen
        $html .= "
        <table width='100%' border='1' cellpadding='5' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#104e8c; color:#fff; font-weight:bold; text-align:center;'>
                <td>#</td>
                <td>EMPLEADO</td>
                <td>UNIDAD</td>
                <td>CARGO</td>
                <td>FECHA</td>
                <td>CONDICIÓN</td>
                <td>GOCE SALARIAL</td>
                <td>FECHA INICIO</td>
                <td>FECHA FIN</td>
                <td>HORA INICIO</td>
                <td>HORA FIN</td>
                <td>RAZÓN</td>
            </tr>";

        foreach ($query as $i => $p) {
            $condicion    = $p->condicion ? 'Fraccionado' : 'Día Completo';
            $goce         = $p->goce ? 'SÍ' : 'NO';
            $fechaInicio  = $p->condicion ? ($p->fecha_fraccionado ?? '-') : ($p->fecha_inicio ?? '-');
            $fechaFin     = $p->condicion ? '-'                            : ($p->fecha_fin    ?? '-');
            $horaInicio   = $p->condicion ? ($p->hora_inicio ?? '-') : '-';
            $horaFin      = $p->condicion ? ($p->hora_fin    ?? '-') : '-';
            $bg           = $i % 2 === 0 ? '#f9f9f9' : '#fff';

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

        $html .= "</table>";
        $html .= "<br><p style='font-size:9px;'><strong>Total de registros:</strong> " . count($query) . "</p>";

        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Permisos_Personales.pdf', 'I');
    }

    // ─────────────────────────────────────────────────────────────
    //  2. PERMISO COMPENSATORIO
    // ─────────────────────────────────────────────────────────────
    private function pdfCompensatorio($idEmpleado, $desde, $hasta)
    {
        $query = PermisoCompensatorio::with('empleado')
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->when($desde,      fn($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta,      fn($q) => $q->whereDate('fecha', '<=', $hasta))
            ->orderBy('fecha')
            ->get();

        $mpdf = $this->mpdfHorizontal('Reporte - Permisos Compensatorios');
        $html = $this->htmlCabecera('REPORTE DE PERMISOS COMPENSATORIOS');

        $html .= "
        <table width='100%' border='1' cellpadding='5' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#104e8c; color:#fff; font-weight:bold; text-align:center;'>
                <td>#</td>
                <td>EMPLEADO</td>
                <td>UNIDAD</td>
                <td>CARGO</td>
                <td>FECHA</td>
                <td>CONDICIÓN</td>
                <td>FECHA INICIO</td>
                <td>FECHA FIN</td>
                <td>HORA INICIO</td>
                <td>HORA FIN</td>
                <td>RAZÓN</td>
            </tr>";

        foreach ($query as $i => $p) {
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

        $html .= "</table>";
        $html .= "<br><p style='font-size:9px;'><strong>Total de registros:</strong> " . count($query) . "</p>";

        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Permisos_Compensatorios.pdf', 'I');
    }

    // ─────────────────────────────────────────────────────────────
    //  3. PERMISO ENFERMEDAD
    // ─────────────────────────────────────────────────────────────
    private function pdfEnfermedad($idEmpleado, $desde, $hasta)
    {
        $query = PermisoEnfermedad::with('empleado')
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->when($desde,      fn($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta,      fn($q) => $q->whereDate('fecha', '<=', $hasta))
            ->orderBy('fecha')
            ->get();

        $mpdf = $this->mpdfHorizontal('Reporte - Permisos por Enfermedad');
        $html = $this->htmlCabecera('REPORTE DE PERMISOS POR ENFERMEDAD');

        $html .= "
        <table width='100%' border='1' cellpadding='5' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#104e8c; color:#fff; font-weight:bold; text-align:center;'>
                <td>#</td>
                <td>EMPLEADO</td>
                <td>UNIDAD</td>
                <td>CARGO</td>
                <td>FECHA</td>
                <td>CONDICIÓN</td>
                <td>UNIDAD ATENCIÓN</td>
                <td>ESPECIALIDAD</td>
                <td>CONDICIÓN MÉDICA</td>
                <td>FECHA INICIO</td>
                <td>FECHA FIN</td>
                <td>HORA INICIO</td>
                <td>HORA FIN</td>
            </tr>";

        foreach ($query as $i => $p) {
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

        $html .= "</table>";
        $html .= "<br><p style='font-size:9px;'><strong>Total de registros:</strong> " . count($query) . "</p>";

        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Permisos_Enfermedad.pdf', 'I');
    }

    // ─────────────────────────────────────────────────────────────
    //  4. PERMISO CONSULTA MÉDICA
    // ─────────────────────────────────────────────────────────────
    private function pdfConsultaMedica($idEmpleado, $desde, $hasta)
    {
        $query = PermisoConsultaMedica::with('empleado')
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->when($desde,      fn($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta,      fn($q) => $q->whereDate('fecha', '<=', $hasta))
            ->orderBy('fecha')
            ->get();

        $mpdf = $this->mpdfHorizontal('Reporte - Consulta Médica');
        $html = $this->htmlCabecera('REPORTE DE PERMISOS - CONSULTA MÉDICA');

        $html .= "
        <table width='100%' border='1' cellpadding='5' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#104e8c; color:#fff; font-weight:bold; text-align:center;'>
                <td>#</td>
                <td>EMPLEADO</td>
                <td>UNIDAD</td>
                <td>CARGO</td>
                <td>FECHA</td>
                <td>CONDICIÓN</td>
                <td>UNIDAD ATENCIÓN</td>
                <td>ESPECIALIDAD</td>
                <td>CONDICIÓN MÉDICA</td>
                <td>FECHA INICIO</td>
                <td>FECHA FIN</td>
                <td>HORA INICIO</td>
                <td>HORA FIN</td>
            </tr>";

        foreach ($query as $i => $p) {
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

        $html .= "</table>";
        $html .= "<br><p style='font-size:9px;'><strong>Total de registros:</strong> " . count($query) . "</p>";

        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Consulta_Medica.pdf', 'I');
    }

    // ─────────────────────────────────────────────────────────────
    //  5. PERMISO INCAPACIDAD
    // ─────────────────────────────────────────────────────────────
    private function pdfIncapacidad($idEmpleado, $desde, $hasta)
    {
        $query = PermisoIncapacidad::with(['empleado', 'tipoIncapacidad', 'riesgo'])
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->when($desde,      fn($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta,      fn($q) => $q->whereDate('fecha', '<=', $hasta))
            ->orderBy('fecha')
            ->get();

        $mpdf = $this->mpdfHorizontal('Reporte - Incapacidades');
        $html = $this->htmlCabecera('REPORTE DE PERMISOS POR INCAPACIDAD');

        $html .= "
        <table width='100%' border='1' cellpadding='5' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#104e8c; color:#fff; font-weight:bold; text-align:center;'>
                <td>#</td>
                <td>EMPLEADO</td>
                <td>UNIDAD</td>
                <td>CARGO</td>
                <td>FECHA</td>
                <td>TIPO INCAPACIDAD</td>
                <td>RIESGO</td>
                <td>DIAGNÓSTICO</td>
                <td>N° DOC.</td>
                <td>INICIO</td>
                <td>FIN</td>
                <td>DÍAS</td>
                <td>HOSPITALIZACIÓN</td>
            </tr>";

        foreach ($query as $i => $p) {
            $hospitaliza = $p->hospitalizacion
                ? "SÍ ({$p->fecha_inicio_hospitalizacion} - {$p->fecha_fin_hospitalizacion})"
                : 'NO';
            $bg = $i % 2 === 0 ? '#f9f9f9' : '#fff';

            $html .= "
            <tr style='background:{$bg};'>
                <td align='center'>" . ($i + 1) . "</td>
                <td>{$p->empleado->nombre}</td>
                <td>{$p->unidad}</td>
                <td>{$p->cargo}</td>
                <td align='center'>{$p->fecha}</td>
                <td>{$p->tipoIncapacidad->nombre}</td>
                <td>{$p->riesgo->nombre}</td>
                <td>{$p->diagnostico}</td>
                <td align='center'>{$p->numero}</td>
                <td align='center'>{$p->fecha_inicio}</td>
                <td align='center'>{$p->fecha_fin}</td>
                <td align='center'>{$p->dias}</td>
                <td align='center'>{$hospitaliza}</td>
            </tr>";
        }

        $totalDias = $query->sum('dias');
        $html .= "</table>";
        $html .= "
        <br>
        <table width='30%' border='1' cellpadding='4' style='border-collapse:collapse;font-size:9px;margin-left:auto;'>
            <tr style='background:#e8e8e8;font-weight:bold;'>
                <td>Total registros</td><td align='center'>" . count($query) . "</td>
            </tr>
            <tr style='background:#e8e8e8;font-weight:bold;'>
                <td>Total días incapacidad</td><td align='center'>{$totalDias}</td>
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
        $query = PermisoOtros::with('empleado')
            ->when($idEmpleado, fn($q) => $q->where('id_empleado', $idEmpleado))
            ->when($desde,      fn($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta,      fn($q) => $q->whereDate('fecha', '<=', $hasta))
            ->orderBy('fecha')
            ->get();

        $mpdf = $this->mpdfHorizontal('Reporte - Otros Permisos');
        $html = $this->htmlCabecera('REPORTE DE OTROS PERMISOS');

        $html .= "
        <table width='100%' border='1' cellpadding='5' style='border-collapse:collapse;font-size:9px;'>
            <tr style='background:#104e8c; color:#fff; font-weight:bold; text-align:center;'>
                <td>#</td>
                <td>EMPLEADO</td>
                <td>UNIDAD</td>
                <td>CARGO</td>
                <td>FECHA</td>
                <td>CONDICIÓN</td>
                <td>FECHA INICIO</td>
                <td>FECHA FIN</td>
                <td>HORA INICIO</td>
                <td>HORA FIN</td>
                <td>RAZÓN</td>
            </tr>";

        foreach ($query as $i => $p) {
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

        $html .= "</table>";
        $html .= "<br><p style='font-size:9px;'><strong>Total de registros:</strong> " . count($query) . "</p>";

        $mpdf->WriteHTML($html);
        return $mpdf->Output('Reporte_Otros_Permisos.pdf', 'I');
    }



}
