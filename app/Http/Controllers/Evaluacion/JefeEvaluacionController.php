<?php

namespace App\Http\Controllers\Evaluacion;

use App\Http\Controllers\Controller;
use App\Models\Evaluacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JefeEvaluacionController extends Controller
{



    public function indexLlenarEvaluacion()
    {
        $evaluaciones = Evaluacion::with(['detalles' => function ($q) {
            $q->orderBy('posicion');
        }])
            ->where('estado', true)
            ->orderBy('posicion')
            ->get();

        return view('backend.evaluacion.jefe.vistacamposevaluar', compact('evaluaciones'));
    }





    public function registrarEvaluacion(Request $request)
    {
        $respuestas = json_decode($request->respuestas, true) ?? [];

        if (empty($respuestas)) {
            abort(400, 'No se recibieron respuestas.');
        }

        $seleccionadas = collect($respuestas)->keyBy('evaluacion_id');

        $evaluacionIds = collect($respuestas)
            ->pluck('evaluacion_id')
            ->unique()
            ->values();

        $factores = Evaluacion::whereIn('id', $evaluacionIds)
            ->where('estado', true)
            ->orderBy('posicion')
            ->with(['detalles' => function ($q) {
                $q->orderBy('posicion');
            }])
            ->get();

        $tablaFactores = [];
        $totalGeneral  = 0;
        $contador      = 1;

        foreach ($factores as $factor) {
            $respuesta             = $seleccionadas->get($factor->id);
            $detalleIdSeleccionado = $respuesta['detalle_id'] ?? null;
            $puntos                = isset($respuesta['puntos']) ? (int)$respuesta['puntos'] : 0;
            $calculo               = ($puntos * 10) / 8;
            $calculoFormateado     = number_format($calculo, 2, '.', '');
            $totalGeneral         += $calculo;

            $tablaFactores[] = [
                'numero'               => $contador,
                'factor'               => $factor,
                'detalle_seleccionado' => $detalleIdSeleccionado,
                'puntos'               => $puntos,
                'formula_texto'        => '( ' . $puntos . ' x 10 ) / 8 =',
                'resultado'            => $calculoFormateado,
            ];
            $contador++;
        }

        $totalGeneralFormateado = number_format($totalGeneral, 2, '.', '');

        if ($totalGeneral <= 10)      { $calificacion = 'DEFICIENTE'; }
        elseif ($totalGeneral <= 20)  { $calificacion = 'REGULAR'; }
        elseif ($totalGeneral <= 30)  { $calificacion = 'BUENO'; }
        elseif ($totalGeneral <= 40)  { $calificacion = 'MUY BUENO'; }
        else                          { $calificacion = 'EXCELENTE'; }

        $mpdf = new \Mpdf\Mpdf([
            'tempDir'       => sys_get_temp_dir(),
            'format'        => 'LETTER',
            'orientation'   => 'P',
            'margin_left'   => 12,
            'margin_right'  => 12,
            'margin_top'    => 10,
            'margin_bottom' => 15,
        ]);

        $mpdf->SetTitle('Evaluacion de Desempeno');

        $logoalcaldia    = public_path('images/gobiernologo.jpg');
        $fechaEvaluacion = now()->format('d-m-Y');

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
                    FICHA DE EVALUACION DE DESEMPEÑO<br>
                </td>
                <td style='width:25%; border:0.8px solid #000; padding:0; vertical-align:top;'>
                    <table width='100%' style='font-size:10px;'>
                        <tr>
                            <td width='40%' style='border-right:0.8px solid #000; border-bottom:0.8px solid #000; padding:4px 6px;'><strong>Código:</strong></td>
                            <td width='60%' style='border-bottom:0.8px solid #000; padding:4px 6px; text-align:center;'>TALE-001-FICH</td>
                        </tr>
                        <tr>
                            <td style='border-right:0.8px solid #000; border-bottom:0.8px solid #000; padding:4px 6px;'><strong>Versión:</strong></td>
                            <td style='border-bottom:0.8px solid #000; padding:4px 6px; text-align:center;'>000</td>
                        </tr>
                        <tr>
                            <td style='border-right:0.8px solid #000; padding:4px 6px;'><strong>Fecha de vigencia:</strong></td>
                            <td style='padding:4px 6px; text-align:center;'>22/10/2025</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>";

        // SECCION I
        $html .= "
    <p style='font-weight:bold;font-size:10px;text-align:center;'>I.- DATOS DE IDENTIFICACION</p>
    <table width='100%' border='1' cellpadding='5' style='border-collapse:collapse;font-size:9px;'>
        <tr><td width='42%'><strong>NOMBRE COMPLETO</strong></td><td>{$request->nombre_completo}</td></tr>
        <tr><td><strong>PUESTO</strong></td><td>{$request->puesto}</td></tr>
        <tr><td><strong>UNIDAD</strong></td><td>{$request->unidad}</td></tr>
        <tr><td><strong>DEPENDENCIA</strong></td><td>{$request->dependencia}</td></tr>
        <tr><td><strong>JEFE INMEDIATO</strong></td><td>{$request->jefe_inmediato}</td></tr>
        <tr><td><strong>PERIODO</strong></td><td>{$request->periodo}</td></tr>
    </table><br>";

        // SECCION II - FACTORES
        foreach ($tablaFactores as $fila) {
            $factor              = $fila['factor'];
            $detalleSeleccionado = $fila['detalle_seleccionado'];

            $html .= '<p style="font-size:10px;font-weight:bold;margin-top:10px;">FACTOR '
                . $fila['numero'] . ': ' . strtoupper($factor->nombre) . '</p>';

            if ($factor->descripcion) {
                $html .= '<p style="font-size:8px;font-style:italic;">' . $factor->descripcion . '</p>';
            }

            $html .= '<table width="100%" border="1" cellpadding="4" style="border-collapse:collapse;font-size:9px;">';

            foreach ($factor->detalles as $detalle) {
                $marca = ((int)$detalle->id === (int)$detalleSeleccionado) ? 'X' : '';
                $html .= '<tr>'
                    . '<td width="5%" align="center" style="font-weight:bold;">' . $detalle->puntos . '-</td>'
                    . '<td width="5%" align="center">' . $marca . '</td>'
                    . '<td>' . $detalle->nombre . '</td>'
                    . '</tr>';
            }

            $html .= '</table>';
        }

        // TOTAL Y CALIFICACION GENERAL
        $html .= '
    <br>
    <table width="60%" border="1" cellpadding="6" style="border-collapse:collapse;font-size:11px;margin:auto;">
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
            <td>' . $fechaEvaluacion . '</td>
        </tr>
    </table>';

        // PROPUESTAS DE MEJORA
        $html .= '
    <pagebreak>
    <p style="font-size:9px;font-weight:bold;margin:4px 0 2px 0;">PROPUESTAS DE MEJORA:</p>
    <table width="100%" style="border-collapse:collapse;">
        <tr><td style="border-bottom:0.6px solid #000;height:18px;"></td></tr>
        <tr><td style="border-bottom:0.6px solid #000;height:18px;"></td></tr>
        <tr><td style="border-bottom:0.6px solid #000;height:18px;"></td></tr>
        <tr><td style="border-bottom:0.6px solid #000;height:18px;"></td></tr>
    </table>';

        // FIRMA EVALUADOR
        $html .= '
    <table width="100%" style="margin-top:50px;">
        <tr>
            <td width="50%"></td>
            <td width="50%" align="center" style="font-size:9px;">
                F.________________________________<br><br>Evaluador
            </td>
        </tr>
    </table>
    <br>';

        // COMENTARIOS
        $html .= '
    <p style="font-size:9px;font-weight:bold;margin:4px 0 2px 0;">COMENTARIOS</p>
    <table width="100%" style="border-collapse:collapse;">
        <tr><td style="border-bottom:0.6px solid #000;height:18px;"></td></tr>
        <tr><td style="border-bottom:0.6px solid #000;height:18px;"></td></tr>
        <tr><td style="border-bottom:0.6px solid #000;height:18px;"></td></tr>
        <tr><td style="border-bottom:0.6px solid #000;height:18px;"></td></tr>
    </table>';

        // FIRMA EVALUADO
        $html .= '
    <table width="100%" style="margin-top:50px;">
        <tr>
            <td width="50%"></td>
            <td width="50%" align="center" style="font-size:9px;">
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
        <tr>
            <td style="padding:6px 6px;">&nbsp;</td>
            <td style="padding:6px 6px;">&nbsp;</td>
            <td style="padding:6px 6px;">&nbsp;</td>
        </tr>
    </table>
    <br>';

        // OBSERVACIONES
        $html .= '
    <p style="font-size:9px;font-weight:bold;margin:4px 0 2px 0;">Observaciones:</p>
    <table width="100%" style="border-collapse:collapse;">
        <tr><td style="border-bottom:0.6px solid #000;height:18px;"></td></tr>
        <tr><td style="border-bottom:0.6px solid #000;height:18px;"></td></tr>
        <tr><td style="border-bottom:0.6px solid #000;height:18px;"></td></tr>
        <tr><td style="border-bottom:0.6px solid #000;height:18px;"></td></tr>
    </table>';

        $mpdf->WriteHTML($html);
        return $mpdf->Output('Evaluacion.pdf', 'I');
    }















}
