<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Distrito;
use App\Models\FichaEmpleado;
use App\Models\NivelAcademico;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ConfiguracionController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    private function getTemaPredeterminado(){
        return Auth::guard('admin')->user()->tema;
    }

    public function vistaCargo()
    {
        $temaPredeterminado = $this->getTemaPredeterminado();
        return view('backend.admin.configuracion.cargo.vistacargo', compact('temaPredeterminado'));
    }

    public function tablaCargo()
    {
        $listado = Cargo::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.configuracion.cargo.tablacargo', compact('listado'));
    }

    public function nuevoCargo(Request $request)
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
            $dato = new Cargo();
            $dato->nombre = $request->nombre;
            $dato->visible = 1;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function infoCargo(Request $request){
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = Cargo::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }


    public function actualizarCargo(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'visible' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Cargo::where('id', $request->id)->update([
            'nombre' => $request->nombre,
            'visible' => $request->visible
        ]);

        return ['success' => 1];
    }




    // =================== UNIDAD ==========================================


    public function indexUnidad()
    {
        $arrayDistritos = Distrito::orderBy('nombre', 'ASC')->get();
        $temaPredeterminado =  $this->getTemaPredeterminado();
        return view('backend.admin.configuracion.unidad.vistaunidad', compact('temaPredeterminado', 'arrayDistritos'));
    }

    public function tablaUnidad()
    {
        $arrayUnidades = Unidad::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.configuracion.unidad.tablaunidad', compact('arrayUnidades'));
    }


    public function nuevoUnidad(Request $request)
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
            $dato = new Unidad();
            $dato->nombre = $request->nombre;
            $dato->visible = 1;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function informacionUnidad(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = Unidad::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarUnidad(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'visible' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Unidad::where('id', $request->id)->update([
            'nombre' => $request->nombre,
            'visible' => $request->visible
        ]);

        return ['success' => 1];
    }




    // =================== NIVEL ACADEMICO ==========================================


    public function indexNivelAcademico()
    {
        $temaPredeterminado =  $this->getTemaPredeterminado();
        return view('backend.admin.configuracion.nivelacademico.vistaacademico', compact('temaPredeterminado'));
    }

    public function tablaNivelAcademico()
    {
        $arrayNivel = NivelAcademico::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.configuracion.nivelacademico.tablaacademico', compact('arrayNivel'));
    }


    public function nuevoNivelAcademico(Request $request)
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
            $dato = new NivelAcademico();
            $dato->nombre = $request->nombre;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function informacionNivelAcademico(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = NivelAcademico::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarNivelAcademico(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        NivelAcademico::where('id', $request->id)->update([
            'nombre' => $request->nombre
        ]);

        return ['success' => 1];
    }

















}
