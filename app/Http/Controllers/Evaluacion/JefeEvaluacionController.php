<?php

namespace App\Http\Controllers\Evaluacion;

use App\Http\Controllers\Controller;
use App\Models\EvaluacionCargo;
use App\Models\EvaluacionDependencias;
use App\Models\EvaluacionTitulo;
use App\Models\EvaluacionUnidad;
use Illuminate\Http\Request;


class JefeEvaluacionController extends Controller
{


    // FICHA DE EVALUACION DE DESEMPEÑO NIVEL DIRECCION

    public function indexLlenarEvaluacionNivelDireccion()
    {

        $arrayUnidades = EvaluacionUnidad::orderBy('nombre', 'ASC')->get();
        $arrayCargos = EvaluacionCargo::orderBy('nombre', 'ASC')->get();
        $arrayDependencias = EvaluacionDependencias::orderBy('nombre', 'ASC')->get();

        $evaluaciones = EvaluacionTitulo::with(['detalles' => function ($q) {
            $q->orderBy('posicion');
        }])
            ->where('id_evaluacion_tipo', 1)
            ->where('estado', true)
            ->orderBy('posicion')
            ->get();

        return view('frontend.evaluacion.vistadesempeno', compact(
            'evaluaciones',
            'arrayDependencias',
            'arrayCargos',
            'arrayUnidades'
        ));
    }




    public function pdfFichaDesempeno(Request $request)
    {
        $respuestas = json_decode($request->respuestas, true) ?? [];

        if (empty($respuestas)) {
            abort(400, 'No se recibieron respuestas.');
        }

        // Cada respuesta: ['evaluacion_id' => X, 'detalle_ids' => [..]]
        $seleccionadas = collect($respuestas)->keyBy('evaluacion_id');

        $evaluacionIds = collect($respuestas)
            ->pluck('evaluacion_id')
            ->unique()
            ->values();

        // Solo factores del tipo 1 (nivel dirección) y activos
        $factores = EvaluacionTitulo::whereIn('id', $evaluacionIds)
            ->where('id_evaluacion_tipo', 1)
            ->where('estado', true)
            ->orderBy('posicion')
            ->with(['detalles' => function ($q) {
                $q->orderBy('posicion');
            }])
            ->get();

        // Formatea puntos: 5.00 => 5 | 4.50 => 4.5
        $fmtPuntos = function ($valor) {
            return rtrim(rtrim(number_format((float)$valor, 2, '.', ''), '0'), '.');
        };

        $tablaFactores = [];
        $totalGeneral = 0;
        $contador = 1;

        foreach ($factores as $factor) {
            $respuesta = $seleccionadas->get($factor->id);

            // Ids marcados (múltiples)
            $idsSeleccionados = array_map('intval', (array) ($respuesta['detalle_ids'] ?? []));

            // Los puntos se toman de la BD (no del navegador) para evitar manipulación
            $totalFactor = (float) $factor->detalles
                ->whereIn('id', $idsSeleccionados)
                ->sum('puntos');

            $totalGeneral += $totalFactor;

            $tablaFactores[] = [
                'numero' => $contador,
                'factor' => $factor,
                'detalles_seleccionados' => $idsSeleccionados,
                'total_factor' => $totalFactor,
            ];
            $contador++;
        }

        $totalGeneralFormateado = $fmtPuntos($totalGeneral);

        // Calificación según tabla
        if ($totalGeneral <= 20) {
            $calificacion = 'DEFICIENTE';
        } elseif ($totalGeneral <= 40) {
            $calificacion = 'REGULAR';
        } elseif ($totalGeneral <= 60) {
            $calificacion = 'BUENO';
        } elseif ($totalGeneral <= 80) {
            $calificacion = 'MUY BUENO';
        } else {
            $calificacion = 'EXCELENTE';
        }

        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => sys_get_temp_dir(),
            'format' => 'LETTER',
            'orientation' => 'P',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 10,
            'margin_bottom' => 15,
        ]);

        $mpdf->SetTitle('Evaluacion de Desempeño');

        $logoalcaldia = public_path('images/logo.png');
        $fechaEvaluacion = now()->format('d-m-Y');

        // Datos escritos por el usuario, escapados para el HTML del PDF
        $nombreCompleto = e($request->nombre_completo);
        $puesto = e($request->puesto);
        $unidad = e($request->unidad);
        $dependencia = e($request->dependencia);
        $jefeInmediato = e($request->jefe_inmediato);
        $periodo = e($request->periodo);

        $html = "
       <table width='100%' style='border-collapse:collapse; font-family: Arial, sans-serif;'>
        <tr>
            <td style='width:25%; border:0.8px solid #000; padding:6px 8px;'>
                <table width='100%'>
                    <tr>
                        <td style='width:30%; text-align:left;'>
                            <img src='{$logoalcaldia}' style='height:38px'>
                        </td>
                        <td style='width:70%; text-align:left; color:#104e8c; font-size:13px; font-weight:bold; line-height:1.3;'>
                            SANTA ANA NORTE<br>EL SALVADOR
                        </td>
                    </tr>
                </table>
            </td>
            <td style='width:50%; border-top:0.8px solid #000; border-bottom:0.8px solid #000; padding:6px 8px; text-align:center; font-size:15px; font-weight:bold;'>
                FICHA DE EVALUACION DE DESEMPEÑO NIVEL DIRECCIÓN<br>
            </td>
            <td style='width:25%; border:0.8px solid #000; padding:0; vertical-align:top;'>
                <table width='100%' style='font-size:10px;'>
                    <tr>
                        <td width='40%' style='border-right:0.8px solid #000; border-bottom:0.8px solid #000; padding:4px 6px;'><strong>Código:</strong></td>
                        <td width='60%' style='border-bottom:0.8px solid #000; padding:4px 6px; text-align:center; font-weight: bold'>TALE-001-FICH</td>
                    </tr>
                    <tr>
                        <td style='border-right:0.8px solid #000; border-bottom:0.8px solid #000; padding:4px 6px;'><strong>Versión:</strong></td>
                        <td style='border-bottom:0.8px solid #000; padding:4px 6px; text-align:center; font-weight: bold'>002</td>
                    </tr>
                    <tr>
                        <td style='border-right:0.8px solid #000; padding:4px 6px;'><strong>Fecha de vigencia:</strong></td>
                        <td style='padding:4px 6px; text-align:center; font-weight: bold'>22/10/2025</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>";

        // SECCION I
        $html .= "
<p style='font-weight:bold;font-size:12px;text-align:center;'>I.- DATOS DE IDENTIFICACION</p>
<table width='100%' border='1' cellpadding='5' style='border-collapse:collapse;font-size:9px;'>
    <tr><td width='30%'><strong>NOMBRE COMPLETO:</strong></td><td width='70%'>{$nombreCompleto}</td></tr>
    <tr><td width='30%'><strong>PUESTO:</strong></td><td width='70%'>{$puesto}</td></tr>
    <tr><td width='30%'><strong>UNIDAD:</strong></td><td width='70%'>{$unidad}</td></tr>
    <tr><td width='30%'><strong>DEPENDENCIA:</strong></td><td width='70%'>{$dependencia}</td></tr>
    <tr><td width='30%'><strong>JEFE INMEDIATO:</strong></td><td width='70%'>{$jefeInmediato}</td></tr>
    <tr><td width='30%'><strong>PERIODO:</strong></td><td width='70%'>{$periodo}</td></tr>
</table><br>";

        // SECCION II - encabezado
        $html .= "
<p style='font-family:Arial,Helvetica,sans-serif;font-weight:bold;font-size:14px;text-align:center;margin:8px 0 2px 0;'>II. FACTORES DE EVALUACION</p>
<p style='font-family:Arial,Helvetica,sans-serif;font-style:italic;font-size:13px;text-align:left;margin:0 0 8px 0;'>Indicaciones: marque con una \"X\" los indicadores que el trabajador cumple, según el desempeño de sus labores.</p>
<br>";

        // SECCION II - FACTORES
        foreach ($tablaFactores as $fila) {
            $factor = $fila['factor'];
            $seleccionados = $fila['detalles_seleccionados'];

            $html .= '<p style="font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:bold;margin:14px 0 4px 0;">'
                . e(mb_strtoupper($factor->nombre, 'UTF-8'))
                . '</p>';

            if ($factor->descripcion) {
                $html .= '<p style="font-family:Arial,Helvetica,sans-serif;font-size:10px;text-align:justify;margin:0 0 8px 0;">'
                    . e($factor->descripcion) . '</p>';
            }

            $html .= '<table width="100%" border="1" cellpadding="3" style="border-collapse:collapse;font-family:Arial,Helvetica,sans-serif;font-size:10px;">';

            // Encabezado
            $html .= '<tr>'
                . '<td width="78%" align="center">INDICADORES</td>'
                . '<td width="12%" align="left">CUMPLE</td>'
                . '<td width="10%" align="center">%</td>'
                . '</tr>';

            // Indicadores
            foreach ($factor->detalles as $detalle) {
                $marcado = in_array((int) $detalle->id, $seleccionados, true);
                $marca = $marcado ? 'X' : '&nbsp;';

                $html .= '<tr>'
                    . '<td>' . e($detalle->nombre) . '</td>'
                    . '<td align="center" style="font-weight:bold;">' . $marca . '</td>'
                    . '<td align="center">' . $fmtPuntos($detalle->puntos) . '</td>'
                    . '</tr>';
            }

            // Total del bloque = suma de puntos marcados
            $html .= '<tr style="font-weight:bold;">'
                . '<td colspan="2" align="right">TOTAL</td>'
                . '<td align="center">' . $fmtPuntos($fila['total_factor']) . '</td>'
                . '</tr>';

            $html .= '</table>';
        }

        // TOTAL Y CALIFICACION GENERAL (resultado)
        $html .= '
    <br>
    <table width="60%" border="1" cellpadding="6" style="border-collapse:collapse;font-size:12px;margin:auto;">
        <tr style="font-weight:bold;">
            <td width="60%">TOTAL</td>
            <td width="40%" align="center">' . $totalGeneralFormateado . '</td>
        </tr>
        <tr style="font-weight:bold;">
            <td>CALIFICACION GENERAL</td>
            <td align="center">' . $calificacion . '</td>
        </tr>
    </table>
    <br>';

        // FECHA DE EVALUACION
        $html .= '
    <table width="100%" border="1" cellpadding="5" style="border-collapse:collapse;font-size:9px;">
        <tr>
            <td width="42%"><strong>FECHA DE EVALUACION</strong></td>
            <td style="font-weight: bold">' . $fechaEvaluacion . '</td>
        </tr>
    </table>
    <br><br><br>';

        // PROPUESTAS DE MEJORA
        $html .= '
    <p style="font-size:9px;font-weight:bold;margin:4px 0 2px 0;">PROPUESTAS DE MEJORA:</p>
    <table width="100%" style="border-collapse:collapse;">
        <tr><td style="border-bottom:0.6px solid #000;height:25px;"></td></tr>
        <tr><td style="border-bottom:0.6px solid #000;height:25px;"></td></tr>
        <tr><td style="border-bottom:0.6px solid #000;height:25px;"></td></tr>
        <tr><td style="border-bottom:0.6px solid #000;height:25px;"></td></tr>
    </table>';

        // FIRMA EVALUADOR
        $html .= '
    <table width="100%" style="margin-top:50px;">
        <tr>
            <td width="50%"></td>
            <td width="50%" align="center" style="font-size:12px;">
                F.________________________________<br><br>Evaluador
            </td>
        </tr>
    </table>
    <br>';

        // COMENTARIOS
        $html .= '
    <p style="font-size:9px;font-weight:bold;margin:4px 0 2px 0;">COMENTARIOS</p>
    <table width="100%" style="border-collapse:collapse;">
        <tr><td style="border-bottom:0.6px solid #000;height:25px;"></td></tr>
        <tr><td style="border-bottom:0.6px solid #000;height:25px;"></td></tr>
        <tr><td style="border-bottom:0.6px solid #000;height:25px;"></td></tr>
        <tr><td style="border-bottom:0.6px solid #000;height:25px;"></td></tr>
    </table>';

        // FIRMA EVALUADO
        $html .= '
    <table width="100%" style="margin-top:50px;">
        <tr>
            <td width="50%"></td>
            <td width="50%" align="center" style="font-size:12px;">
                F.________________________________<br><br>Evaluado
            </td>
        </tr>
    </table>
    <br><br>';

        // ESPACIO RESERVADO PARA RECURSOS HUMANOS
        $html .= '
        <p style="text-align:center;font-weight:bold;font-size:10px;">ESPACIO RESERVADO PARA RECURSOS HUMANOS</p>
        <table width="70%" border="1" cellpadding="6" style="border-collapse:collapse;font-size:9px;margin:auto;">
            <tr style="font-weight:bold;text-align:center;">
                <td width="34%">FECHA DE RECEPCION</td>
                <td width="33%">REVISADO POR:</td>
                <td width="33%">FECHA DE PROCESADO</td>
            </tr>
            <tr style="height:45px;">
                <td style="height:45px;">&nbsp;</td>
                <td style="height:45px;">&nbsp;</td>
                <td style="height:45px;">&nbsp;</td>
            </tr>
        </table>
        <br>';

        $mpdf->WriteHTML($html);
        return $mpdf->Output('Evaluacion.pdf', 'I');
    }








}
