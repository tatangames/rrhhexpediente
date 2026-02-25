<?php

namespace App\Http\Controllers\Permiso;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Empleado;
use App\Models\PermisoCompensatorio;
use App\Models\PermisoConsultaMedica;
use App\Models\PermisoEnfermedad;
use App\Models\PermisoIncapacidad;
use App\Models\PermisoOtro;
use App\Models\Riesgo;
use App\Models\TipoIncapacidad;
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
                'fecha_fraccionado' => $request->fecha_fraccionado,
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




    // =================================================================
    // **************     HISTORIAL - CONSULTA MEDICA ****************
    // =================================================================


    public function indexHistorialPermisoConsultaMedica()
    {
        $temaPredeterminado =  $this->getTemaPredeterminado();

        return view('backend.permisos.historial.consultamedica.vistapermisosconsultamedicaeditar', compact('temaPredeterminado'));
    }

    public function tablaHistorialPermisoConsultaMedica()
    {
        $arrayPermisos = PermisoConsultaMedica::orderBy('fecha', 'asc')->get()
            ->map(function ($item) {

                $item->fecha = date('d-m-Y', strtotime($item->fecha));

                $infoEmpleado = Empleado::find($item->id_empleado);
                $nombreEmpleado = $infoEmpleado->nombre;

                $item->nombreEmpleado = $nombreEmpleado;

                return $item;
            });

        return view('backend.permisos.historial.consultamedica.tablapermisosconsultamedicaeditar', compact('arrayPermisos'));
    }

    public function informacionHistorialPermisoConsultaMedica(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = PermisoConsultaMedica::where('id', $request->id)->first();

        // Agregar nombre del empleado
        $empleado = Empleado::find($info->id_empleado);
        $info->nombre_empleado = $empleado->nombre ?? 'Sin nombre';

        $arrayEmpleados = Empleado::orderBy('nombre', 'asc')->get();

        return ['success' => 1, 'info' => $info, 'arrayEmpleados' => $arrayEmpleados];
    }


    public function actualizarHistorialPermisoConsultaMedica(Request $request)
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

            $permiso = PermisoConsultaMedica::find($request->id);

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


    public function borrarHistorialPermisoConsultaMedica(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        PermisoConsultaMedica::where('id', $request->id)->delete();

        return ['success' => 1];
    }





    // =================================================================
    // **************     HISTORIAL - COMPENSATORIO ****************
    // =================================================================


    public function indexHistorialPermisoCompensatorio()
    {
        $temaPredeterminado =  $this->getTemaPredeterminado();

        return view('backend.permisos.historial.compensatorio.vistapermisoscompensatorioeditar', compact('temaPredeterminado'));
    }

    public function tablaHistorialPermisoCompensatorio()
    {
        $arrayPermisos = PermisoCompensatorio::orderBy('fecha', 'asc')->get()
            ->map(function ($item) {


                $item->fecha = date('d-m-Y', strtotime($item->fecha));

                $infoEmpleado = Empleado::find($item->id_empleado);
                $nombreEmpleado = $infoEmpleado->nombre;

                $item->nombreEmpleado = $nombreEmpleado;

                return $item;
            });

        return view('backend.permisos.historial.compensatorio.tablapermisoscompensatorioeditar', compact('arrayPermisos'));
    }

    public function informacionHistorialPermisoCompensatorio(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = PermisoCompensatorio::where('id', $request->id)->first();

        // Agregar nombre del empleado
        $empleado = Empleado::find($info->id_empleado);
        $info->nombre_empleado = $empleado->nombre ?? 'Sin nombre';

        $arrayEmpleados = Empleado::orderBy('nombre', 'asc')->get();

        return ['success' => 1, 'info' => $info, 'arrayEmpleados' => $arrayEmpleados];
    }


    public function actualizarHistorialPermisoCompensatorio(Request $request)
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

            $permiso = PermisoCompensatorio::find($request->id);

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
            ]);

            return response()->json(['success' => 1]);

        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }


    public function borrarHistorialPermisoCompensatorio(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        PermisoCompensatorio::where('id', $request->id)->delete();


        return ['success' => 1];
    }






    // =================================================================
    // **************     HISTORIAL - INCAPACIDAD ****************
    // =================================================================


    public function indexHistorialPermisoIncapacidad()
    {
        $temaPredeterminado   = $this->getTemaPredeterminado();
        $arrayTipoIncapacidad = TipoIncapacidad::orderBy('nombre', 'asc')->get();
        $arrayRiesgo          = Riesgo::orderBy('nombre', 'asc')->get();

        return view('backend.permisos.historial.incapacidad.vistapermisosincapacidadeditar',
            compact('temaPredeterminado', 'arrayTipoIncapacidad', 'arrayRiesgo'));
    }

    public function tablaHistorialPermisoIncapacidad()
    {
        $arrayPermisos = PermisoIncapacidad::orderBy('fecha', 'asc')->get()
            ->map(function ($item) {

                $item->fecha = date('d-m-Y', strtotime($item->fecha));

                $infoEmpleado = Empleado::find($item->id_empleado);
                $nombreEmpleado = $infoEmpleado->nombre;

                $item->nombreEmpleado = $nombreEmpleado;

                return $item;
            });

        return view('backend.permisos.historial.incapacidad.tablapermisosincapacidadeditar', compact('arrayPermisos'));
    }

    public function informacionHistorialPermisoIncapacidad(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = PermisoIncapacidad::where('id', $request->id)->first();

        // Agregar nombre del empleado
        $empleado = Empleado::find($info->id_empleado);
        $info->nombre_empleado = $empleado->nombre ?? 'Sin nombre';

        $arrayEmpleados = Empleado::orderBy('nombre', 'asc')->get();

        return ['success' => 1, 'info' => $info, 'arrayEmpleados' => $arrayEmpleados];
    }


    // Actualizar con los campos correctos de incapacidad
    public function actualizarHistorialPermisoIncapacidad(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'id'          => 'required',
            'empleado_id' => 'required',
            'fecha'       => 'required',
        ]);

        if ($validar->fails()) {
            return response()->json(['success' => 0, 'message' => 'Datos inválidos']);
        }

        try {
            $permiso = PermisoIncapacidad::find($request->id);
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
                'id_empleado'                  => $request->empleado_id,
                'unidad'                       => $unidad->nombre,
                'cargo'                        => $cargo->nombre,
                'fecha'                        => $request->fecha,
                'id_tipo_incapacidad'          => $request->tipocondicion,
                'id_riesgo'                    => $request->riesgo,
                'fecha_inicio'                 => $request->fecha_inicio,
                'dias'                         => $request->dias,
                'fecha_fin'                    => $request->fecha_fin,
                'diagnostico'                  => $request->diagnostico,
                'numero'                       => $request->numero,
                'hospitalizacion'              => $request->hospitalizacion,
                'fecha_inicio_hospitalizacion' => $request->fecha_inicio_hospitalizacion,
                'fecha_fin_hospitalizacion'    => $request->fecha_fin_hospitalizacion,
            ]);

            return response()->json(['success' => 1]);

        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }


    public function borrarHistorialPermisoIncapacidad(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        PermisoIncapacidad::where('id', $request->id)->delete();


        return ['success' => 1];
    }










}
