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
use App\Models\PermisoPersonal;
use App\Models\Riesgo;
use App\Models\TipoIncapacidad;
use App\Models\Unidad;
use Carbon\Carbon;
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




// =================================================================
    // **************     HISTORIAL - PERSONAL ****************
    // =================================================================


    public function indexHistorialPermisoPersonal()
    {
        $temaPredeterminado =  $this->getTemaPredeterminado();

        return view('backend.permisos.historial.personal.vistapermisospersonaleditar', compact('temaPredeterminado'));
    }

    public function tablaHistorialPermisoPersonal()
    {
        $arrayPermisos = PermisoPersonal::orderBy('fecha', 'asc')->get()
            ->map(function ($item) {


                $item->fecha = date('d-m-Y', strtotime($item->fecha));

                $infoEmpleado = Empleado::find($item->id_empleado);
                $nombreEmpleado = $infoEmpleado->nombre;

                $item->nombreEmpleado = $nombreEmpleado;

                return $item;
            });

        return view('backend.permisos.historial.personal.tablapermisospersonaleditar', compact('arrayPermisos'));
    }

    public function informacionHistorialPermisoPersonal(Request $request)
    {
        $regla = ['id' => 'required'];

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return response()->json(['success' => 0]);
        }

        $info = PermisoPersonal::find($request->id);

        if (!$info) {
            return response()->json(['success' => 0, 'message' => 'Registro no encontrado']);
        }

        $empleado = Empleado::find($info->id_empleado);
        $info->nombre_empleado = $empleado->nombre ?? 'Sin nombre';

        // ✅ Ya no necesitas el mapeo, fecha_fraccionado existe en la tabla
        return response()->json(['success' => 1, 'info' => $info]);
    }


    public function actualizarHistorialPermisoPersonal(Request $request)
    {
        $regla = [
            'id'           => 'required',
            'empleado_id'  => 'required',
            'condicion'    => 'required',
            'fechaEntrego' => 'required',
            'goce_sueldo'  => 'required|boolean',
        ];

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return response()->json(['success' => 0, 'message' => 'Datos inválidos']);
        }

        try {

            $permiso = PermisoPersonal::find($request->id);

            if (!$permiso) {
                return response()->json(['success' => 0, 'message' => 'Registro no encontrado']);
            }

            $empleado = Empleado::find($request->empleado_id);

            if (!$empleado) {
                return response()->json(['success' => 0, 'message' => 'Empleado no encontrado']);
            }

            $unidad = Unidad::find($empleado->id_unidad);
            $cargo  = Cargo::find($empleado->id_cargo);

            $anio = Carbon::parse($request->fechaEntrego)->year;

            // ✅ Filtrar usando los campos correctos según condición
            $permisosDelAnio = PermisoPersonal::where('id_empleado', $request->empleado_id)
                ->where('goce', $request->goce_sueldo)
                ->where('id', '!=', $request->id)
                ->where(function ($q) use ($anio) {
                    $q->where(function ($q2) use ($anio) {
                        // Completo: año por fecha_inicio
                        $q2->where('condicion', 0)
                            ->whereYear('fecha_inicio', $anio);
                    })
                        ->orWhere(function ($q2) use ($anio) {
                            // Fraccionado: año por fecha_fraccionado
                            $q2->where('condicion', 1)
                                ->whereYear('fecha_fraccionado', $anio);
                        });
                })
                ->get();

            // ===============================
            // CALCULAR MINUTOS USADOS
            // ===============================
            $totalMinutosUsados = 0;

            foreach ($permisosDelAnio as $p) {
                if ($p->condicion == 0) {
                    $inicio = Carbon::parse($p->fecha_inicio);
                    $fin    = Carbon::parse($p->fecha_fin);
                    $dias   = $inicio->diffInDays($fin) + 1;
                    $totalMinutosUsados += $dias * 480;
                } else {
                    $horaInicio = Carbon::parse($p->hora_inicio);
                    $horaFin    = Carbon::parse($p->hora_fin);
                    $totalMinutosUsados += $horaInicio->diffInMinutes($horaFin);
                }
            }

            // ===============================
            // CALCULAR MINUTOS SOLICITADOS
            // ===============================
            $minutosSolicitados = 0;

            if ($request->condicion == 0) {
                $inicio = Carbon::parse($request->fecha_inicio);
                $fin    = Carbon::parse($request->fecha_fin);
                $dias   = $inicio->diffInDays($fin) + 1;
                $minutosSolicitados = $dias * 480;
            } else {
                $minutosSolicitados = (int) $request->duracion_minutos;
            }

            // ===============================
            // VALIDAR LÍMITE SEGÚN GOCE
            // ===============================
            if ($request->goce_sueldo == 1) {
                $limiteMinutos = 5 * 480;
                $tipoGoce      = 'Con goce de sueldo';
            } else {
                $limiteMinutos = 60 * 480;
                $tipoGoce      = 'Sin goce de sueldo';
            }

            if (($totalMinutosUsados + $minutosSolicitados) > $limiteMinutos) {
                $disponibles = $limiteMinutos - $totalMinutosUsados;
                return response()->json([
                    'success' => 0,
                    'tipo'    => 'limite_excedido',
                    'data'    => [
                        'anio'                => $anio,
                        'tipo_goce'           => $tipoGoce,
                        'limite_minutos'      => $limiteMinutos,
                        'usados_minutos'      => $totalMinutosUsados,
                        'solicitando_minutos' => $minutosSolicitados,
                        'disponibles_minutos' => $disponibles,
                    ],
                    'message' => "Límite de permisos {$tipoGoce} excedido.",
                ]);
            }

            // ===============================
            // TODO OK: Actualizar
            // ===============================
            $permiso->update([
                'id_empleado'       => $request->empleado_id,
                'unidad'            => $unidad->nombre ?? null,
                'cargo'             => $cargo->nombre  ?? null,
                'fecha'             => $request->fechaEntrego,
                'condicion'         => $request->condicion,
                'goce'              => $request->goce_sueldo,
                'razon'             => $request->razon ?? null,
                // ✅ Campos según condición, limpiar los que no aplican
                'fecha_fraccionado' => $request->condicion == 1 ? $request->fecha_fraccionado : null,
                'hora_inicio'       => $request->condicion == 1 ? $request->hora_inicio       : null,
                'hora_fin'          => $request->condicion == 1 ? $request->hora_fin          : null,
                'fecha_inicio'      => $request->condicion == 0 ? $request->fecha_inicio      : null,
                'fecha_fin'         => $request->condicion == 0 ? $request->fecha_fin         : null,
            ]);

            return response()->json(['success' => 1]);

        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }


    public function borrarHistorialPermisoPersonal(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        PermisoPersonal::where('id', $request->id)->delete();

        return ['success' => 1];
    }






}
