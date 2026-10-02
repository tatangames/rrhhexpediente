<?php

namespace App\Http\Controllers\Permiso;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\PermisoCompensatorio;
use App\Models\PermisoConsultaMedica;
use App\Models\PermisoEnfermedad;
use App\Models\PermisoIncapacidad;
use App\Models\PermisoOtro;
use App\Models\PermisoPersonal;
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
        return view('backend.permisos.config.unidad.vistaunidad');
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



    // Devuelve los empleados de la unidad y las demás unidades (posibles destinos)
    public function empleadosUnidadPermisos(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $unidad = PermisosUnidades::where('id', $request->id)->first();

        if (!$unidad) {
            return ['success' => 0];
        }

        $empleados = DB::table('permisos_empleados as e')
            ->join('permisos_cargos as c', 'c.id', '=', 'e.id_cargo')
            ->where('e.id_unidad', $unidad->id)
            ->orderBy('e.nombre', 'ASC')
            ->select('e.id', 'e.nombre', 'c.nombre as cargo')
            ->get();

        $unidades = PermisosUnidades::where('id', '!=', $unidad->id)
            ->orderBy('nombre', 'ASC')
            ->get(['id', 'nombre']);

        return [
            'success'   => 1,
            'unidad'    => $unidad,
            'empleados' => $empleados,
            'unidades'  => $unidades,
        ];
    }

// Traslada los empleados a las unidades elegidas y borra la unidad
// success: 1 = ok, 2 = traslado incompleto/inválido, 0 = datos inválidos, 99 = error
    public function borrarUnidadPermisos(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        // Formato: { id_empleado: id_unidad_destino }
        $traslados = $request->input('traslados', []);

        if (!is_array($traslados)) {
            $traslados = [];
        }

        DB::beginTransaction();

        try {
            $unidad = PermisosUnidades::where('id', $request->id)->lockForUpdate()->first();

            if (!$unidad) {
                DB::rollback();
                return ['success' => 0];
            }

            // Empleados que HOY pertenecen a la unidad (por si cambió desde que se abrió el modal)
            $empleados = PermisosEmpleados::where('id_unidad', $unidad->id)->lockForUpdate()->get();

            // Cada empleado debe traer un destino válido y distinto a la unidad que se borra
            foreach ($empleados as $emp) {
                $destino = $traslados[$emp->id] ?? null;

                if (!$destino
                    || (int) $destino === (int) $unidad->id
                    || !PermisosUnidades::where('id', $destino)->exists()) {
                    DB::rollback();
                    return ['success' => 2];
                }
            }

            foreach ($empleados as $emp) {
                PermisosEmpleados::where('id', $emp->id)->update([
                    'id_unidad' => $traslados[$emp->id],
                ]);
            }

            $unidad->delete();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }








    // ========= EMPLEADOS =========================================================

    public function indexEmpleados()
    {
        $arrayCargo = PermisosCargos::orderBy('nombre', 'ASC')->get();
        $arrayUnidad = PermisosUnidades::orderBy('nombre', 'ASC')->get();

        return view('backend.permisos.empleados.vistaempleados', compact('arrayCargo', 'arrayUnidad'));
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




    // Tablas de permisos que tienen id_empleado
    private function modelosPermisosEmpleado(): array
    {
        return [
            'Personales'      => PermisoPersonal::class,
            'Compensatorios'  => PermisoCompensatorio::class,
            'Enfermedad'      => PermisoEnfermedad::class,
            'Consulta médica' => PermisoConsultaMedica::class,
            'Incapacidades'   => PermisoIncapacidad::class,
            'Otros permisos'  => PermisoOtro::class,
        ];
    }

    // Devuelve el resumen de permisos del empleado y la lista de posibles destinos
    public function resumenBorrarEmpleados(Request $request)
    {
        $validar = Validator::make($request->all(), ['id' => 'required']);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $empleado = PermisosEmpleados::with(['unidad', 'cargo'])->find($request->id);

        if (!$empleado) {
            return ['success' => 0];
        }

        $resumen = [];
        $total   = 0;

        foreach ($this->modelosPermisosEmpleado() as $tipo => $modelo) {
            $cantidad = $modelo::where('id_empleado', $empleado->id)->count();
            $total   += $cantidad;
            $resumen[] = ['tipo' => $tipo, 'total' => $cantidad];
        }

        $otros = PermisosEmpleados::with(['unidad', 'cargo'])
            ->where('id', '!=', $empleado->id)
            ->orderBy('nombre', 'ASC')
            ->get()
            ->map(fn($e) => [
                'id'     => $e->id,
                'nombre' => $e->nombre,
                'unidad' => $e->unidad?->nombre,
                'cargo'  => $e->cargo?->nombre,
            ]);

        return [
            'success'  => 1,
            'empleado' => [
                'id'     => $empleado->id,
                'nombre' => $empleado->nombre,
                'unidad' => $empleado->unidad?->nombre,
                'cargo'  => $empleado->cargo?->nombre,
            ],
            'resumen'  => $resumen,
            'total'    => $total,
            'otros'    => $otros,
        ];
    }

    // Traslada todos los permisos al empleado destino y borra al empleado
    // success: 1 = ok, 2 = destino inválido, 0 = datos inválidos, 99 = error
    public function borrarEmpleados(Request $request)
    {
        $validar = Validator::make($request->all(), ['id' => 'required']);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {
            $origen = PermisosEmpleados::where('id', $request->id)->lockForUpdate()->first();

            if (!$origen) {
                DB::rollback();
                return ['success' => 0];
            }

            // ¿Tiene permisos hoy? (por si cambió desde que se abrió el modal)
            $total = 0;
            foreach ($this->modelosPermisosEmpleado() as $modelo) {
                $total += $modelo::where('id_empleado', $origen->id)->count();
            }

            if ($total > 0) {
                $idDestino = $request->id_destino;

                if (!$idDestino || (int) $idDestino === (int) $origen->id) {
                    DB::rollback();
                    return ['success' => 2];
                }

                $destino = PermisosEmpleados::where('id', $idDestino)->lockForUpdate()->first();

                if (!$destino) {
                    DB::rollback();
                    return ['success' => 2];
                }

                foreach ($this->modelosPermisosEmpleado() as $modelo) {
                    $modelo::where('id_empleado', $origen->id)
                        ->update(['id_empleado' => $destino->id]);
                }
            }

            $origen->delete();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }









}
