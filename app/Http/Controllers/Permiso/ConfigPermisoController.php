<?php

namespace App\Http\Controllers\Permiso;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\PermisoRiesgo;
use App\Models\PermisosCargos;
use App\Models\PermisosEmpleados;
use App\Models\PermisosTipoIncapacidad;
use App\Models\PermisosUnidades;
use App\Models\PermisoTipoPermisos;
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
        $arrayTipoPermiso = PermisoTipoPermisos::orderBy('nombre', 'ASC')->get();
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
            $dato = new PermisoTipoPermisos();
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

        $info = PermisoTipoPermisos::where('id', $request->id)->first();

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

        PermisoTipoPermisos::where('id', $request->id)->update([
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
        $arrayRiesgos = PermisoRiesgo::orderBy('nombre', 'ASC')->get();
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
            $dato = new PermisoRiesgo();
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

        $info = PermisoRiesgo::where('id', $request->id)->first();

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

        PermisoRiesgo::where('id', $request->id)->update([
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
        $arrayTipoIncapacidad = PermisosTipoIncapacidad::orderBy('nombre', 'ASC')->get();
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
            $dato = new PermisosTipoIncapacidad();
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

        $info = PermisosTipoIncapacidad::where('id', $request->id)->first();

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

        PermisosTipoIncapacidad::where('id', $request->id)->update([
            'nombre' => $request->nombre,
        ]);

        return ['success' => 1];
    }





    public function vistaCargoPermisos()
    {
        $temaPredeterminado = $this->getTemaPredeterminado();
        return view('backend.permisos.config.cargo.vistacargo', compact('temaPredeterminado'));
    }

    public function tablaCargoPermisos()
    {
        $listado = PermisosCargos::orderBy('nombre', 'ASC')->get();

        return view('backend.permisos.config.cargo.tablacargo', compact('listado'));
    }

    public function nuevoCargoPermisos(Request $request)
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
            $dato = new PermisosCargos();
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

    public function infoCargoPermisos(Request $request){
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = PermisosCargos::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }


    public function actualizarCargoPermisos(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        PermisosCargos::where('id', $request->id)->update([
            'nombre' => $request->nombre,
        ]);

        return ['success' => 1];
    }




    // =================== UNIDAD ==========================================


    public function indexUnidadPermisos()
    {
        $temaPredeterminado =  $this->getTemaPredeterminado();
        return view('backend.permisos.config.unidad.vistaunidad', compact('temaPredeterminado'));
    }

    public function tablaUnidadPermisos()
    {
        $arrayUnidades = PermisosUnidades::orderBy('nombre', 'ASC')->get();
        return view('backend.permisos.config.unidad.tablaunidad', compact('arrayUnidades'));
    }


    public function nuevoUnidadPermisos(Request $request)
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
            $dato = new PermisosUnidades();
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

    public function informacionUnidadPermisos(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = PermisosUnidades::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarUnidadPermisos(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        PermisosUnidades::where('id', $request->id)->update([
            'nombre' => $request->nombre,
        ]);

        return ['success' => 1];
    }











    // ========= EMPLEADOS =========================================================

    public function indexEmpleados()
    {
        $temaPredeterminado = $this->getTemaPredeterminado();

        $arrayCargo = PermisosCargos::orderBy('nombre', 'ASC')->get();
        $arrayUnidad = PermisosUnidades::orderBy('nombre', 'ASC')->get();

        return view('backend.permisos.empleados.vistaempleados', compact('temaPredeterminado', 'arrayCargo', 'arrayUnidad'));
    }

    public function tablaEmpleados()
    {
        $arrayEmpleados = PermisosEmpleados::with(['unidad', 'cargo'])
            ->orderBy('nombre', 'ASC')
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
            $dato = new PermisosEmpleados();
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

        $info = PermisosEmpleados::where('id', $request->id)->first();
        $arrayUnidad = PermisosUnidades::orderBy('nombre', 'ASC')->get();
        $arrayCargo = PermisosCargos::orderBy('nombre', 'ASC')->get();

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

        PermisosEmpleados::where('id', $request->id)->update([
            'nombre' => $request->nombre,
            'id_unidad' => $request->unidad,
            'id_cargo' => $request->cargo,
        ]);

        return ['success' => 1];
    }












}
