<?php

namespace App\Http\Controllers\Permiso;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Empleado;
use App\Models\Riesgo;
use App\Models\TipoIncapacidad;
use App\Models\TipoPermiso;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ConfigPermisoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    private function getTemaPredeterminado(){
        return Auth::guard('admin')->user()->tema;
    }



    public function indexTipoPermiso()
    {
        $temaPredeterminado =  $this->getTemaPredeterminado();
        return view('backend.permisos.config.tipopermiso.vistatipopermiso', compact('temaPredeterminado'));
    }

    public function tablaTipoPermiso()
    {
        $arrayTipoPermiso = TipoPermiso::orderBy('nombre', 'ASC')->get();
        return view('backend.permisos.config.tipopermiso.tablatipopermiso', compact('arrayTipoPermiso'));
    }

    public function nuevoTipoPermiso(Request $request)
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
            $dato = new TipoPermiso();
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

    public function informacionTipoPermiso(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = TipoPermiso::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarTipoPermiso(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        TipoPermiso::where('id', $request->id)->update([
            'nombre' => $request->nombre,
        ]);

        return ['success' => 1];
    }




    // ========= RIESGOS =========================================================



    public function indexRiesgos()
    {
        $temaPredeterminado =  $this->getTemaPredeterminado();
        return view('backend.permisos.config.riesgos.vistariesgos', compact('temaPredeterminado'));
    }

    public function tablaRiesgos()
    {
        $arrayRiesgos = Riesgo::orderBy('nombre', 'ASC')->get();
        return view('backend.permisos.config.riesgos.tablariesgos', compact('arrayRiesgos'));
    }

    public function nuevoRiesgos(Request $request)
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
            $dato = new Riesgo();
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

    public function informacionRiesgos(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = Riesgo::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarRiesgos(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Riesgo::where('id', $request->id)->update([
            'nombre' => $request->nombre,
        ]);

        return ['success' => 1];
    }




    // ========= TIPO DE INCAPACIDAD =========================================================

    public function indexTipoIncapacidad()
    {
        $temaPredeterminado = $this->getTemaPredeterminado();
        return view('backend.permisos.config.tipoincapacidad.vistaincapacidad', compact('temaPredeterminado'));
    }

    public function tablaTipoIncapacidad()
    {
        $arrayTipoIncapacidad = TipoIncapacidad::orderBy('nombre', 'ASC')->get();
        return view('backend.permisos.config.tipoincapacidad.tablaincapacidad', compact('arrayTipoIncapacidad'));
    }

    public function nuevoTipoIncapacidad(Request $request)
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
            $dato = new TipoIncapacidad();
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

    public function informacionTipoIncapacidad(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = TipoIncapacidad::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarTipoIncapacidad(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        TipoIncapacidad::where('id', $request->id)->update([
            'nombre' => $request->nombre,
        ]);

        return ['success' => 1];
    }





    // ========= EMPLEADOS =========================================================

    public function indexEmpleados()
    {
        $temaPredeterminado = $this->getTemaPredeterminado();

        $arrayCargo = Cargo::orderBy('nombre', 'ASC')->get();
        $arrayUnidad = Unidad::orderBy('nombre', 'ASC')->get();

        return view('backend.permisos.empleados.vistaempleados', compact('temaPredeterminado', 'arrayCargo', 'arrayUnidad'));
    }

    public function tablaEmpleados()
    {
        $arrayEmpleados = Empleado::orderBy('nombre', 'ASC')
            ->get()
            ->map(function ($item) {

                return [
                    'id' => $item->id,
                    'nombre' => $item->nombre,
                    'unidad' => $item->unidad->nombre ?? null,
                    'cargo' => $item->cargo->nombre ?? null,
                ];
            });

        return view('backend.permisos.empleados.tablaempleados', compact('arrayEmpleados'));
    }

    public function nuevoEmpleados(Request $request)
    {
        $regla = array(
            'nombre' => 'required',
            'unidad' => 'required',
            'cargo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new Empleado();
            $dato->nombre = $request->nombre;
            $dato->id_unidad = $request->unidad;
            $dato->id_cargo = $request->cargo;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function informacionEmpleados(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = Empleado::where('id', $request->id)->first();
        $arrayUnidad = Unidad::orderBy('nombre', 'ASC')->get();
        $arrayCargo = Cargo::orderBy('nombre', 'ASC')->get();

        return ['success' => 1, 'info' => $info, 'arrayUnidad' => $arrayUnidad, 'arrayCargo' => $arrayCargo];
    }

    public function actualizarEmpleados(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'unidad' => 'required',
            'cargo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Empleado::where('id', $request->id)->update([
            'nombre' => $request->nombre,
            'id_unidad' => $request->unidad,
            'id_cargo' => $request->cargo,
        ]);

        return ['success' => 1];
    }












}
