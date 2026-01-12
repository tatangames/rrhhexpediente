<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Distrito;
use App\Models\FichaBeneficiario;
use App\Models\FichaEmpleado;
use App\Models\Media;
use App\Models\NivelAcademico;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class FichaController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    private function getTemaPredeterminado(){
        return Auth::guard('admin')->user()->tema;
    }

    public function vistaFichaForm(){
        $temaPredeterminado = $this->getTemaPredeterminado();

        $id = Auth::guard('admin')->id();

        $arrayDistritos = Distrito::orderBy('nombre', 'ASC')->get();
        $arrayCargos = Cargo::orderBy('nombre', 'ASC')->get();
        $arrayUnidades = Unidad::orderBy('nombre', 'ASC')->get();
        $arrayNiveles = NivelAcademico::orderBy('nombre', 'ASC')->get();

        $arrayInfo = FichaEmpleado::where('id_administrador', $id)->first();
        $arrayBeneficiarios = FichaBeneficiario::where('id_administrador', $id)->get();

        return view('backend.empleado.ficha.vistaficha', compact('temaPredeterminado', 'arrayInfo',
        'arrayDistritos', 'arrayCargos', 'arrayUnidades', 'arrayNiveles', 'arrayBeneficiarios'));
    }


    public function actualizarFicha(Request $request)
    {
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $idAdmin  = Auth::guard('admin')->id();

            FichaEmpleado::where('id_administrador', $idAdmin)->update([
                'id_distrito' => $request->selectDistrito,
                'id_cargo' => $request->selectCargo,
                'id_unidad' => $request->selectUnidad,
                'id_nivelacademico' => $request->selectAcademico,

                'nombre' => $request->nombre,
                'dui' => $request->dui,
                'fecha_ingreso' => $request->fechaIngreso,
                'salario_actual' => $request->salario,

                'fecha_nacimiento' => $request->fechaNacimiento,
                'lugar_nacimiento' => $request->lugarNacimiento,
                'otro_nivelacademico' => $request->otroNivel,
                'profesion' => $request->profesion,
                'direccion' => $request->direccionActual,

                'estado_civil' => $request->selectCivil,
                'celular' => $request->celular,
                'caso_emergencia' => $request->casoEmergencia,
                'celular_emergencia' => $request->contactoEmergencia,
                'tipo_padecimiento' => $request->tipoPadecimiento,
            ]);

            // 2️⃣ Eliminar beneficiarios actuales
            DB::table('ficha_beneficiario')
                ->where('id_administrador', $idAdmin)
                ->delete();

            // 3️⃣ Insertar nuevos beneficiarios
            $beneficiarios = json_decode($request->beneficiarios, true);

            foreach ($beneficiarios as $b) {
                DB::table('ficha_beneficiario')->insert([
                    'id_administrador' => $idAdmin,
                    'nombre' => $b['nombre'],
                    'parentesco' => $b['parentesco'],
                    'edad' => $b['edad'],
                    'porcentaje' => $b['porcentaje'],
                ]);
            }


            DB::commit();
            return ['success' => 1];

        } catch (\Throwable $e) {
            DB::rollback();
            Log::error('Actualizar Ficha: '.$e->getMessage());

            return ['success' => 99];
        }
    }

    public function pdfFicha()
    {
        $idAdmin = Auth::guard('admin')->id();

        $infoFicha = FichaEmpleado::where('id_administrador', $idAdmin)->first();
        $arrayBeneficiarios = FichaBeneficiario::where('id_administrador', $idAdmin)->get();

        if (!$infoFicha) {
            abort(404);
        }

        // =========================
        // NOMBRES RELACIONADOS
        // =========================
        $nombreCargo = Cargo::where('id', $infoFicha->id_cargo)->value('nombre');
        $nombreUnidad = Unidad::where('id', $infoFicha->id_unidad)->value('nombre');
        $nombreDistrito = Distrito::where('id', $infoFicha->id_distrito)->value('nombre');
        $nombreNivelAcademico = NivelAcademico::where('id', $infoFicha->id_nivelacademico)->value('nombre');

        // =========================
        // FORMATOS Y CÁLCULOS
        // =========================
        $fechaIngreso = $infoFicha->fecha_ingreso
            ? date('d-m-Y', strtotime($infoFicha->fecha_ingreso))
            : '';

        $fechaNacimiento = $infoFicha->fecha_nacimiento
            ? date('d-m-Y', strtotime($infoFicha->fecha_nacimiento))
            : '';

        $edad = '';
        if (!empty($infoFicha->fecha_nacimiento)) {
            $edad = \Carbon\Carbon::parse($infoFicha->fecha_nacimiento)->age;
        }

        $nivelAcademico = ($infoFicha->id_nivelacademico == 7)
            ? $infoFicha->otro_nivelacademico
            : $nombreNivelAcademico;

        $estadoCivil = match ($infoFicha->estado_civil) {
            1 => 'Soltero',
            2 => 'Casado',
            3 => 'Divorciado',
            4 => 'Viudo',
            5 => 'Acompañado',
            default => '',
        };

        $tienePadecimiento = !empty($infoFicha->tipo_padecimiento) ? 'SÍ' : 'NO';

        // =========================
        // mPDF
        // =========================
        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => sys_get_temp_dir(),
            'format' => 'LETTER',
            'orientation' => 'P'
        ]);

        $mpdf->SetTitle('Ficha del Empleado');

        $logoalcaldia = public_path('images/gobiernologo.jpg');

        // =========================
        // HTML PDF
        // =========================
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
                <td style='width:50%; border-top:0.8px solid #000; border-bottom:0.8px solid #000; padding:6px 8px; text-align:center;
                font-size:15px; font-weight:normal;'>
                    FICHA DE ACTUALIZACIÓN DE DATOS DE PERSONAL PERMANENTE<br>
                </td>
                <td style='width:25%; border:0.8px solid #000; padding:0; vertical-align:top;'>
                    <table width='100%' style='font-size:10px;'>
                        <tr>
                            <td width='40%' style='border-right:0.8px solid #000; border-bottom:0.8px solid #000; padding:4px 6px;'><strong>Código:</strong></td>
                            <td width='60%' style='border-bottom:0.8px solid #000; padding:4px 6px; text-align:center;'>TALE-003-FICH</td>
                        </tr>
                        <tr>
                            <td style='border-right:0.8px solid #000; border-bottom:0.8px solid #000; padding:4px 6px;'><strong>Versión:</strong></td>
                            <td style='border-bottom:0.8px solid #000; padding:4px 6px; text-align:center;'>000</td>
                        </tr>
                        <tr>
                            <td style='border-right:0.8px solid #000; padding:4px 6px;'><strong>Fecha de vigencia:</strong></td>
                            <td style='padding:4px 6px; text-align:center;'>02/12/2025</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>






    <br><br>

    <table width='100%' border='1' cellpadding='6' style='border-collapse:collapse; font-size:11px;'>
        <tr>
            <td colspan='4'>
                <strong>NOMBRE DEL EMPLEADO:</strong>
                <span style='font-size: 12px;'> $infoFicha->nombre </span>
            </td>
        </tr>
        <tr>
            <td colspan='2'><strong>CARGO:</strong> {$nombreCargo}</td>
            <td colspan='2'><strong>DUI/NIT:</strong> {$infoFicha->dui}</td>
        </tr>
        <tr>
            <td colspan='2'><strong>UNIDAD:</strong> {$nombreUnidad}</td>
            <td colspan='2'><strong>DISTRITO:</strong> {$nombreDistrito}</td>
        </tr>
        <tr>
            <td colspan='2'><strong>FECHA DE INGRESO:</strong> {$fechaIngreso}</td>
            <td colspan='2'><strong>SALARIO ACTUAL:</strong> $ " . number_format($infoFicha->salario_actual, 2) . "</td>
        </tr>
    </table>

    <br>

    <div style='text-align:center; font-weight:bold;'>INFORMACIÓN PARTICULAR</div>

    <table width='100%' border='1' cellpadding='6' style='border-collapse:collapse; font-size:11px;'>
        <tr>
            <td colspan='3'><strong>FECHA Y LUGAR DE NACIMIENTO:</strong> {$fechaNacimiento} - {$infoFicha->lugar_nacimiento}</td>
            <td><strong>EDAD:</strong> {$edad}</td>
        </tr>
        <tr>
            <td colspan='2'><strong>NIVEL ACADÉMICO:</strong> {$nivelAcademico}</td>
            <td colspan='2'><strong>PROFESIÓN:</strong> {$infoFicha->profesion}</td>
        </tr>
        <tr>
            <td colspan='4'><strong>DIRECCIÓN ACTUAL:</strong> {$infoFicha->direccion}</td>
        </tr>
        <tr>
            <td colspan='2'><strong>ESTADO CIVIL:</strong> {$estadoCivil}</td>
            <td colspan='2'><strong>CELULAR:</strong> {$infoFicha->celular}</td>
        </tr>
        <tr>
            <td colspan='2'><strong>EN CASO DE EMERGENCIA LLAMAR A:</strong> {$infoFicha->caso_emergencia}</td>
            <td colspan='2'><strong>CELULAR:</strong> {$infoFicha->celular_emergencia}</td>
        </tr>
        <tr>
            <td colspan='4'><strong>¿PADECE ALGUNA ENFERMEDAD CRÓNICA?</strong> {$tienePadecimiento}</td>
        </tr>
        <tr>
            <td colspan='4'><strong>TIPO DE PADECIMIENTO:</strong> {$infoFicha->tipo_padecimiento}</td>
        </tr>
    </table>

    <br>

    <div style='text-align:center; font-weight:bold;'>DATOS BENEFICIARIOS</div>

    <table width='100%' border='1' cellpadding='6' style='border-collapse:collapse; font-size:11px;'>
        <tr align='center' style='font-weight:bold;'>
            <td style='font-weight: bold'>N°</td>
            <td style='font-weight: bold'>NOMBRE</td>
            <td style='font-weight: bold'>PARENTESCO</td>
            <td style='font-weight: bold'>EDAD</td>
            <td style='font-weight: bold'>PORCENTAJE</td>
        </tr>
    ";

        $i = 1;
        foreach ($arrayBeneficiarios as $b) {
            $html .= "
        <tr>
            <td align='center' style='font-size: 12px'>{$i}</td>
            <td style='font-size: 12px'>{$b->nombre}</td>
            <td style='font-size: 12px'>{$b->parentesco}</td>
            <td align='center' style='font-size: 12px'>{$b->edad}</td>
            <td align='center' style='font-size: 12px'>{$b->porcentaje}%</td>
        </tr>
        ";
            $i++;
        }

        $html .= "
    </table>

    <br><br>

    <div style='font-size:13px;'>
        Declaro bajo juramento que los datos anteriormente presentados son brindados por mi persona.
    </div>

    <br><br><br><br><br><br><br>

    <div style='text-align:center;'>
        f. _______________________________<br>
        <small style='font-size: 13px; font-weight: bold'>{$infoFicha->nombre}</small>
    </div>
    ";

       // $mpdf->setFooter('Página {PAGENO} de {nb}');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }







    //****************************** EXPEDIENTE **************************************

    public function vistaExpedienteForm()
    {
        $temaPredeterminado = $this->getTemaPredeterminado();

        $id = Auth::guard('admin')->id();

        $documentos = Media::where('id_administrador', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backend.empleado.expediente.vistaexpediente', compact('temaPredeterminado', 'documentos'));
    }


    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:307200',
        ]);

        $adminId = Auth::guard('admin')->id();
        $guardados = [];

        foreach ($request->file('files') as $file) {

            $nombreOriginal = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $nombreFinal = time().'_'.Str::random(10).'.'.$extension;

            $ruta = $file->storeAs(
                '/',
                $nombreFinal,
                'expedientes'
            );

            $media = Media::create([
                'nombre_original'   => $nombreOriginal,
                'archivo'           => $nombreFinal,
                'ruta'              => $ruta,
                'disk'              => 'expedientes',
                'size'              => $file->getSize(),
                'id_administrador'  => $adminId,
            ]);

            $guardados[] = [
                'id' => $media->id,
                'nombre' => $nombreOriginal,
                'url' => Storage::disk('expedientes')->url($ruta),
            ];
        }

        return response()->json([
            'success' => true,
            'files' => $guardados
        ]);
    }


    public function destroy($id)
    {
        $adminId = Auth::guard('admin')->id();

        $media = Media::where('id', $id)
            ->where('id_administrador', $adminId)
            ->firstOrFail();

        if ($media->id_administrador !== $adminId) {
            abort(403, 'No autorizado');
        }

        // Borra archivo físico
        if (Storage::disk($media->disk)->exists($media->ruta)) {
            Storage::disk($media->disk)->delete($media->ruta);
        }

        // Borra registro BD
        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Documento eliminado correctamente'
        ]);
    }


    public function download($id)
    {
        $adminId = Auth::guard('admin')->id();

        $media = Media::where('id', $id)
            ->where('id_administrador', $adminId)
            ->firstOrFail();

        if ($media->id_administrador !== $adminId) {
            abort(403, 'No autorizado');
        }

        return Storage::disk($media->disk)
            ->download($media->ruta, $media->nombre_original);
    }













}
