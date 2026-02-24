<?php

namespace App\Http\Controllers\Permiso;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Empleado;
use App\Models\PermisoEnfermedad;
use App\Models\PermisoOtro;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HistorialPermisoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    private function getTemaPredeterminado(){
        return Auth::guard('admin')->user()->tema;
    }


    // =================================================================
    // **************     HISTORIAL - OTROS ****************
    // =================================================================


    public function indexHistorialPermisoOtros()
    {
        $temaPredeterminado =  $this->getTemaPredeterminado();

        return view('backend.permisos.historial.otros.vistapermisosotroseditar', compact('temaPredeterminado'));
    }

    public function tablaHistorialPermisoOtros()
    {
        $arrayPermisos = PermisoOtro::orderBy('fecha', 'asc')->get()
            ->map(function ($item) {


                $item->fecha = date('d-m-Y', strtotime($item->fecha));

                $infoEmpleado = Empleado::find($item->id_empleado);
                $nombreEmpleado = $infoEmpleado->nombre;

                $item->nombreEmpleado = $nombreEmpleado;

                return $item;
            });

        return view('backend.permisos.historial.otros.tablapermisosotroseditar', compact('arrayPermisos'));
    }

    public function informacionHistorialPermisoOtros(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = PermisoOtro::where('id', $request->id)->first();

        // Agregar nombre del empleado
        $empleado = Empleado::find($info->id_empleado);
        $info->nombre_empleado = $empleado->nombre ?? 'Sin nombre';

        $arrayEmpleados = Empleado::orderBy('nombre', 'asc')->get();

        return ['success' => 1, 'info' => $info, 'arrayEmpleados' => $arrayEmpleados];
    }


    public function actualizarHistorialPermisoOtros(Request $request)
    {
        $regla = array(
            'id'          => 'required',
            'empleado_id' => 'required',
            'condicion'   => 'required',
            'fechaEntrego'=> 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return response()->json(['success' => 0, 'message' => 'Datos inválidos']);
        }

        try {

            $permiso = PermisoOtro::find($request->id);

            if (!$permiso) {
                return response()->json(['success' => 0, 'message' => 'Registro no encontrado']);
            }

            $empleado = Empleado::find($request->empleado_id);

            if (!$empleado) {
                return response()->json(['success' => 0, 'message' => 'Empleado no encontrado']);
            }

            $unidad = Unidad::find($empleado->id_unidad);
            $cargo  = Cargo::find($empleado->id_cargo);

            $permiso->update([
                'id_empleado' => $request->empleado_id,
                'unidad'      => $unidad->nombre ?? null,
                'cargo'       => $cargo->nombre  ?? null,
                'fecha'       => $request->fechaEntrego,
                'condicion'   => $request->condicion,
                'fecha_inicio'=> $request->fecha_inicio,
                'fecha_fin'   => $request->fecha_fin,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin'    => $request->hora_fin,
                'razon'       => $request->razon,
            ]);

            return response()->json(['success' => 1]);

        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }


    public function borrarHistorialPermisoOtros(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        PermisoOtro::where('id', $request->id)->delete();


        return ['success' => 1];
    }



    // =================================================================
    // **************     HISTORIAL - ENFERMEDAD ****************
    // =================================================================


    public function indexHistorialPermisoEnfermedad()
    {
        $temaPredeterminado =  $this->getTemaPredeterminado();

        return view('backend.permisos.historial.enfermedad.vistapermisosenfermedadeditar', compact('temaPredeterminado'));
    }

    public function tablaHistorialPermisoEnfermedad()
    {
        $arrayPermisos = PermisoEnfermedad::orderBy('fecha', 'asc')->get()
            ->map(function ($item) {

                $item->fecha = date('d-m-Y', strtotime($item->fecha));

                $infoEmpleado = Empleado::find($item->id_empleado);
                $nombreEmpleado = $infoEmpleado->nombre;

                $item->nombreEmpleado = $nombreEmpleado;

                return $item;
            });

        return view('backend.permisos.historial.enfermedad.tablapermisosenfermedadeditar', compact('arrayPermisos'));
    }

    public function informacionHistorialPermisoEnfermedad(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = PermisoEnfermedad::where('id', $request->id)->first();

        // Agregar nombre del empleado
        $empleado = Empleado::find($info->id_empleado);
        $info->nombre_empleado = $empleado->nombre ?? 'Sin nombre';

        $arrayEmpleados = Empleado::orderBy('nombre', 'asc')->get();

        return ['success' => 1, 'info' => $info, 'arrayEmpleados' => $arrayEmpleados];
    }


    public function actualizarHistorialPermisoEnfermedad(Request $request)
    {
        $regla = array(
            'id'          => 'required',
            'empleado_id' => 'required',
            'condicion'   => 'required',
            'fechaEntrego'=> 'required',
        );

        // fecha_fraccionado

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return response()->json(['success' => 0, 'message' => 'Datos inválidos']);
        }

        try {

            $permiso = PermisoEnfermedad::find($request->id);

            if (!$permiso) {
                return response()->json(['success' => 0, 'message' => 'Registro no encontrado']);
            }

            $empleado = Empleado::find($request->empleado_id);

            if (!$empleado) {
                return response()->json(['success' => 0, 'message' => 'Empleado no encontrado']);
            }

            $unidad = Unidad::find($empleado->id_unidad);
            $cargo  = Cargo::find($empleado->id_cargo);

            $permiso->update([
                'id_empleado' => $request->empleado_id,
                'unidad'      => $unidad->nombre ?? null,
                'cargo'       => $cargo->nombre  ?? null,
                'fecha'       => $request->fechaEntrego,
                'condicion'   => $request->condicion,
                'fecha_fraccionado' => $request->fecha_fraccionado,
                'fecha_inicio'=> $request->fecha_inicio,
                'fecha_fin'   => $request->fecha_fin,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin'    => $request->hora_fin,
                'razon'       => $request->razon,

                'unidad_atencion'   => $request->unidadAtencion,
                'especialidad'      => $request->especialidad,
                'condicion_medica'  => $request->condicionMedica,
            ]);

            return response()->json(['success' => 1]);

        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }


    public function borrarHistorialPermisoEnfermedad(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        PermisoEnfermedad::where('id', $request->id)->delete();


        return ['success' => 1];
    }






}
