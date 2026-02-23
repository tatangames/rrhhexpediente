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





        // =========================
        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => sys_get_temp_dir(),
            'format' => 'LETTER',
            'orientation' => 'L'
        ]);



        // =========================
        // HTML PDF
        // =========================

        $mpdf->SetTitle('Reporte');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/gobiernologo.jpg';

        $tabla = "
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
                    $nombreEncabezado<br>
                </td>
                <td style='width:25%; border:0.8px solid #000; padding:0; vertical-align:top;'>
                    <table width='100%' style='font-size:10px;'>
                        <tr>
                            <td width='40%' style='border-right:0.8px solid #000; border-bottom:0.8px solid #000; padding:4px 6px;'><strong>Código:</strong></td>
                            <td width='60%' style='border-bottom:0.8px solid #000; padding:4px 6px; text-align:center;'>$nombreCodigo</td>
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

        $tabla .= '<p style="text-align:center; font-weight:bold;">' . $nombreTitulo . '</p>';


        $tabla .= "<table width='100%' style='border-collapse:collapse; font-size:10px;' border='1' cellpadding='4'>";

        if($tipo == 1){ // ACCESO

            $tabla .= "
    <tr style='background:#f0f0f0; font-weight:bold;'>
        <td>FECHA</td>
        <td>HORA</td>
        <td>OPERADOR</td>
        <td>TIPO DE ACCESO</td>
        <td>NOVEDAD</td>
        <td>EQUIPO INVOLUCRADO</td>
        <td>OBSERVACIONES</td>
        <td style='width: 10%'>FIRMA</td>
    </tr>";

            foreach($registros as $r){
                $tabla .= "
        <tr>
    <td style='padding:3px;'>".$this->fechaDMY($r->fecha)."</td>
    <td style='padding:3px;'>".$this->fechaHora($r->fecha)."</td>
    <td style='padding:3px;'>{$r->usuario}</td>
    <td style='padding:3px;'>{$r->tipo_acceso}</td>
    <td style='padding:3px;'>{$r->novedad}</td>
    <td style='padding:3px;'>{$r->equipo_involucrado}</td>
    <td style='padding:3px;'>{$r->observaciones}</td>
    <td style='height:30px;'></td>
</tr>";
            }
        }


        if($tipo == 2){ // MANTENIMIENTO

            $tabla .= "
            <tr style='background:#f0f0f0; font-weight:bold;'>
                <td>FECHA</td>
                <td>EQUIPO</td>
                <td>TIPO MTTO</td>
                <td>DESCRIPCIÓN</td>
                <td>TÉCNICO</td>
                <td>PRÓXIMO MTTO</td>
                <td>OBSERVACIONES</td>
            </tr>";

            foreach($registros as $r){
                $tabla .= "
        <tr>
            <td>".$this->fechaDMY($r->fecha)."</td>
            <td>{$r->equipo}</td>
            <td>".$this->estadoMantenimientoTexto($r->tipo_mantenimiento)."</td>
            <td>{$r->descripcion}</td>
            <td>{$r->usuario}</td>
             <td>".$this->fechaDMY($r->proximo_mantenimiento)."</td>
            <td>{$r->observaciones}</td>
        </tr>";
            }
        }


        if($tipo == 3){ // SOPORTE

            $tabla .= "
<tr style='background:#f0f0f0; font-weight:bold;'>
    <td>No.</td>
    <td>FECHA</td>
    <td>UNIDAD</td>
    <td>DESCRIPCIÓN</td>
    <td>TÉCNICO</td>
    <td>SOLUCIÓN</td>
    <td>ESTADO</td>
    <td>OBSERVACIONES</td>
</tr>";

            $i = 1; // contador correlativo

            foreach($registros as $r){
                $tabla .= "
                <tr>
                    <td style='text-align:center;'>".$i."</td>
                    <td>".$this->fechaDMY($r->fecha)."</td>
                    <td>{$r->unidad}</td>
                    <td>{$r->descripcion}</td>
                    <td>{$r->usuario}</td>
                    <td>{$r->solucion}</td>
                    <td>".$this->estadoSoporteTexto($r->estado)."</td>
                    <td>{$r->observaciones}</td>
                </tr>";
                $i++;
            }
        }




        if($tipo == 4){ // INCIDENCIAS

            $tabla .= "
    <tr style='background:#f0f0f0; font-weight:bold;'>
        <td>FECHA</td>
        <td>TIPO DE INCIDENTE</td>
        <td>SISTEMA AFECTADO</td>
        <td>NIVEL</td>
        <td>RESPONSABLE</td>
        <td>MEDIDAS CORRECTIVAS</td>
        <td>OBSERVACIONES</td>
    </tr>";

            foreach($registros as $r){
                $tabla .= "
        <tr>
            <td>".$this->fechaDMY($r->fecha)."</td>
            <td>{$r->tipo_incidente}</td>
            <td>{$r->sistema_afectado}</td>
            <td>".$this->estadoIncidenciasTexto($r->nivel)."</td>
            <td>{$r->usuario}</td>
            <td>{$r->medida_correctivas}</td>
            <td>{$r->observaciones}</td>
        </tr>";
            }
        }

        $mpdf->SetFooter('Página {PAGENO} de {nbpg}');
        $tabla .= "</table>";
        $mpdf->WriteHTML($tabla);
        $mpdf->Output();
    }


}
