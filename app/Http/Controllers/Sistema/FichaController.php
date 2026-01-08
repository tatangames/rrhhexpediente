<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Distrito;
use App\Models\FichaBeneficiario;
use App\Models\FichaEmpleado;
use App\Models\NivelAcademico;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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




}
