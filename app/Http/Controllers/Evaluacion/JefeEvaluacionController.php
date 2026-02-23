<?php

namespace App\Http\Controllers\Evaluacion;

use App\Http\Controllers\Controller;
use App\Models\Evaluacion;
use Illuminate\Http\Request;

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
        $respuestas = json_decode($request->respuestas, true);
        $puntajeTotal = collect($respuestas)->sum('puntos');

        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => sys_get_temp_dir(),
            'format' => 'LETTER',
            'orientation' => 'L'
        ]);

        $mpdf->SetTitle('Evaluación de Desempeño');
        $mpdf->showImageErrors = false;

        $logoalcaldia = public_path('images/gobiernologo.jpg');

        $tabla = "
    <table width='100%' style='border-collapse:collapse; font-family: Arial, sans-serif;'>
        <tr>
            <td style='width:25%; border:0.8px solid #000; padding:6px 8px;'>
                <img src='{$logoalcaldia}' style='height:38px'>
            </td>
            <td style='width:50%; border:0.8px solid #000; padding:6px 8px; text-align:center; font-size:15px; font-weight:bold;'>
                FICHA DE EVALUACIÓN DE DESEMPEÑO
            </td>
            <td style='width:25%; border:0.8px solid #000; padding:6px 8px; font-size:10px;'>
                Periodo: {$request->periodo}
            </td>
        </tr>
    </table>
    <br>";

        $tabla .= "
    <table width='100%' border='1' cellpadding='5' style='border-collapse:collapse; font-size:11px;'>
        <tr><td><strong>Empleado:</strong> {$request->nombre_completo}</td></tr>
        <tr><td><strong>Puesto:</strong> {$request->puesto}</td></tr>
        <tr><td><strong>Unidad:</strong> {$request->unidad}</td></tr>
        <tr><td><strong>Dependencia:</strong> {$request->dependencia}</td></tr>
        <tr><td><strong>Jefe Inmediato:</strong> {$request->jefe_inmediato}</td></tr>
    </table><br>";

        $tabla .= "
    <table width='100%' border='1' cellpadding='4' style='border-collapse:collapse; font-size:10px;'>
        <tr style='background:#f0f0f0; font-weight:bold;'>
            <td>#</td>
            <td>ID Evaluación</td>
            <td>ID Detalle</td>
            <td>Puntos</td>
        </tr>";

        foreach ($respuestas as $index => $r) {
            $tabla .= "
        <tr>
            <td>" . ($index + 1) . "</td>
            <td>{$r['evaluacion_id']}</td>
            <td>{$r['detalle_id']}</td>
            <td>{$r['puntos']}</td>
        </tr>";
        }

        $tabla .= "
        <tr style='font-weight:bold;'>
            <td colspan='3' align='right'>PUNTAJE TOTAL</td>
            <td>{$puntajeTotal}</td>
        </tr>
    </table>";

        $mpdf->SetFooter('Página {PAGENO} de {nbpg}');
        $mpdf->WriteHTML($tabla);

        // 🔥 ABRIR EN NAVEGADOR
        $mpdf->Output('Evaluacion.pdf', 'I');
    }





















}
