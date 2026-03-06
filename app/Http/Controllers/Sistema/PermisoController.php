<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Administrador;
use App\Models\FichaEmpleado;
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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    private function getTemaPredeterminado(){
        return Auth::guard('admin')->user()->tema;
    }


    public function index(){
        $roles = Role::all()->pluck('name', 'id');
        $temaPredeterminado =  $this->getTemaPredeterminado();

        return view('backend.admin.rolesypermisos.permisos', compact('roles', 'temaPredeterminado'));
    }

    public function tablaUsuarios(){
        $usuarios = Administrador::orderBy('id', 'ASC')->get();

        return view('backend.admin.rolesypermisos.tabla.tablapermisos', compact('usuarios'));
    }

    public function nuevoUsuario(Request $request){

        // Verificar usuario duplicado
        if (Administrador::where('usuario', $request->usuario)->exists()) {
            return ['success' => 1];
        }

        // Convertir ID de rol a nombre de rol
        $role = Role::where('id', $request->rol)
            ->where('guard_name', 'admin')
            ->first();

        if (!$role) {
            return ['success' => 4, 'msg' => 'El rol no existe para guard admin'];
        }

        // 1️⃣ Crear usuario SIN rol todavía
        $u = new Administrador();
        $u->nombre   = $request->nombre;
        $u->usuario  = $request->usuario;
        $u->password = bcrypt($request->password);
        $u->activo   = 1;
        $u->tema     = 0;

        if ($u->save()) {

            // 2️⃣ Asignar rol DESPUÉS de guardar
            $u->assignRole($role->name);

            return ['success' => 2];
        }

        return ['success' => 3];
    }

    public function infoUsuario(Request $request){
        if($info = Administrador::where('id', $request->id)->first()){

            $roles = Role::all()->pluck('name', 'id');

            $idrol = $info->roles->pluck('id');

            return ['success' => 1,
                'info' => $info,
                'roles' => $roles,
                'idrol' => $idrol];

        }else{
            return ['success' => 2];
        }
    }

    public function editarUsuario(Request $request){

        if(Administrador::where('id', $request->id)->first()){

            if(Administrador::where('usuario', $request->usuario)
                ->where('id', '!=', $request->id)->first()){
                return ['success' => 1];
            }

            $usuario = Administrador::find($request->id);
            $usuario->nombre = $request->nombre;
            $usuario->usuario = $request->usuario;

            if($request->password != null){
                $usuario->password = bcrypt($request->password);
            }

            try {
                $role = Role::findById((int) $request->rol, 'admin'); // <= guard explícito
                $usuario->syncRoles([$role]); // reemplaza roles anteriores
            } catch (RoleDoesNotExist $e) {
                return ['success' => 4, 'message' => 'El rol no existe para el guard admin'];
            }

            $usuario->save();

            return ['success' => 2];
        }else{
            return ['success' => 3];
        }
    }


    public function editarUsuarioPorRRHH(Request $request)
    {
        if(Administrador::where('id', $request->id)->first()){

            // usuario no este igual a otro
            if(Administrador::where('usuario', $request->usuario)
                ->where('id', '!=', $request->id)->first()){
                return ['success' => 1];
            }


            // NO USUARIO REPETIDO
            if(Administrador::where('dui', $request->dui)
                ->where('id', '!=', $request->id)->first()){
                return ['success' => 2];
            }


            $usuario = Administrador::find($request->id);
            $usuario->usuario = $request->usuario;
            $usuario->dui = $request->dui;
            if($request->password != null){
                $usuario->password = bcrypt($request->password);
            }
            $usuario->save();

            FichaEmpleado::where('id_administrador', $request->id)->update([
                'dui' => $request->dui,
            ]);


            return ['success' => 3];
        }else{
            return ['success' => 3];
        }
    }




    public function nuevoRol(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        // verificar si existe el rol
        if(Role::where('name', $request->nombre)->first()){
            return ['success' => 1];
        }

        Role::create(['name' => $request->nombre]);

        return ['success' => 2];
    }


    public function nuevoPermisoExtra(Request $request){

        // verificar si existe el permiso
        if(Permission::where('name', $request->nombre)->first()){
            return ['success' => 1];
        }

        Permission::create(['name' => $request->nombre, 'description' => $request->descripcion]);

        return ['success' => 2];
    }

    public function borrarPermisoGlobal(Request $request){

        // buscamos el permiso el cual queremos eliminar
        Permission::findById($request->idpermiso)->delete();

        return ['success' => 1];
    }



    // =============== GENERAR PERMISO - OTROS ==========================================================

    public function indexGenerarPermisoOtros()
    {
        $temaPredeterminado = $this->getTemaPredeterminado();

        return view('backend.permisos.generar.generarpermisootros', compact('temaPredeterminado'));
    }


    public function buscarPorNombre(Request $request)
    {
        $texto = $request->get('q');

        $empleados = PermisosEmpleados::where('nombre', 'LIKE', "%$texto%")
            ->with(['unidad', 'cargo'])
            ->limit(50)
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->id,
                    'nombre' => $e->nombre,
                    'unidad' => $e->unidad->nombre ?? '',
                    'cargo' => $e->cargo->nombre ?? '',
                ];
            });

        return response()->json($empleados);
    }


    public function informacionPermisoOtros(Request $request)
    {
        $empleadoId = $request->empleado_id;
        $anioActual = Carbon::parse($request->fecha)->year;

        $permisos = PermisoOtro::where('id_empleado', $empleadoId)
            ->whereYear('fecha', $anioActual)
            ->orderBy('fecha', 'desc')
            ->get();

        $data = $permisos->map(function ($item) {

            $info = [
                'fecha' => Carbon::parse($item->fecha)->format('d-m-Y'),
                'razon' => $item->razon,
                'condicion' => $item->condicion ?? 0
            ];

            if ($item->condicion == 0) {

                // Día completo
                $info['tipo'] = 'Día completo';
                $info['fecha_inicio'] = $item->fecha_inicio
                    ? Carbon::parse($item->fecha_inicio)->format('d-m-Y')
                    : null;

                $info['fecha_fin'] = $item->fecha_fin
                    ? Carbon::parse($item->fecha_fin)->format('d-m-Y')
                    : null;

            } else {

                // Fraccionado
                $info['tipo'] = 'Fraccionado';

                if ($item->hora_inicio && $item->hora_fin) {

                    $horaInicio = Carbon::parse($item->hora_inicio);
                    $horaFin = Carbon::parse($item->hora_fin);

                    $minutosTotales = $horaFin->diffInMinutes($horaInicio);

                    $horas = floor($minutosTotales / 60);
                    $minutos = $minutosTotales % 60;

                    $info['hora_inicio'] = $horaInicio->format('H:i');
                    $info['hora_fin'] = $horaFin->format('H:i');

                    $texto = '';

                    if ($horas > 0) {
                        $texto .= $horas . ' h ';
                    }

                    if ($minutos > 0) {
                        $texto .= $minutos . ' m';
                    }

                    $info['horas_texto'] = trim($texto);
                }
            }

            return $info;
        });

        return response()->json([
            'success' => true,
            'anio' => $anioActual,
            'total' => $permisos->count(),
            'permisos' => $data
        ]);
    }


    public function guardarPermisoOtros(Request $request)
    {
        $regla = array(
            'empleado_id'  => 'required',
            'condicion'    => 'required',
            'fechaEntrego' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);
        if ($validar->fails()) { return ['success' => 0]; }

        try {

            $empleado = PermisosEmpleados::find($request->empleado_id);

            if (!$empleado) {
                return response()->json(['success' => 0, 'message' => 'Empleado no encontrado']);
            }

            // ===============================
            // 📅 CONVERTIR FECHAS A Y-m-d (formato MySQL)
            // El input HTML envía Y-m-d, pero por si acaso se parsea con Carbon
            // ===============================
            $fechaEntrego     = \Carbon\Carbon::parse($request->fechaEntrego)->format('Y-m-d');

            $fechaFraccionado = $request->fecha_fraccionado
                ? \Carbon\Carbon::parse($request->fecha_fraccionado)->format('Y-m-d')
                : null;

            $fechaInicio      = $request->fecha_inicio
                ? \Carbon\Carbon::parse($request->fecha_inicio)->format('Y-m-d')
                : null;

            $fechaFin         = $request->fecha_fin
                ? \Carbon\Carbon::parse($request->fecha_fin)->format('Y-m-d')
                : null;

            // ===============================
            // 🔎 VERIFICAR DUPLICADOS
            // ===============================
            if (!$request->forzar_guardado) {

                $query = PermisoOtro::where('id_empleado', $request->empleado_id)
                    ->where('condicion', $request->condicion);

                if ($request->condicion == 1) {

                    // Fraccionado: misma fecha + solapamiento de horas
                    $query->where('fecha_fraccionado', $fechaFraccionado)
                        ->where(function ($q) use ($request) {
                            $q->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fin])
                                ->orWhereBetween('hora_fin', [$request->hora_inicio, $request->hora_fin])
                                ->orWhere(function ($q2) use ($request) {
                                    $q2->where('hora_inicio', '<=', $request->hora_inicio)
                                        ->where('hora_fin', '>=', $request->hora_fin);
                                });
                        });

                } else {

                    // Completo: solapamiento de fechas (Y-m-d sí ordena bien en SQL)
                    $query->where(function ($q) use ($fechaInicio, $fechaFin) {
                        $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                            ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                            ->orWhere(function ($q2) use ($fechaInicio, $fechaFin) {
                                $q2->where('fecha_inicio', '<=', $fechaInicio)
                                    ->where('fecha_fin', '>=', $fechaFin);
                            });
                    });
                }

                $duplicados = $query->get();

                if ($duplicados->count() > 0) {

                    $lista = $duplicados->map(function ($p) {
                        return [
                            'fecha'             => $p->fecha,
                            'condicion'         => $p->condicion == 1 ? 'Fraccionado' : 'Completo',
                            'fecha_fraccionado' => $p->fecha_fraccionado,
                            'fecha_inicio'      => $p->fecha_inicio,
                            'fecha_fin'         => $p->fecha_fin,
                            'hora_inicio'       => $p->hora_inicio,
                            'hora_fin'          => $p->hora_fin,
                            'razon'             => $p->razon ?? 'Sin descripción',
                        ];
                    });

                    return response()->json([
                        'success'    => 2,
                        'message'    => 'Se encontraron permisos similares',
                        'duplicados' => $lista,
                    ]);
                }
            }

            // ===============================
            // 💾 GUARDAR
            // ===============================
            $unidad = PermisosUnidades::find($empleado->id_unidad);
            $cargo  = PermisosCargos::find($empleado->id_cargo);

            PermisoOtro::create([
                'id_empleado'       => $request->empleado_id,
                'unidad'            => $unidad->nombre ?? null,
                'cargo'             => $cargo->nombre ?? null,
                'fecha'             => $fechaEntrego,       // Y-m-d ✅
                'condicion'         => $request->condicion,
                'fecha_fraccionado' => $fechaFraccionado,   // Y-m-d ✅
                'fecha_inicio'      => $fechaInicio,        // Y-m-d ✅
                'fecha_fin'         => $fechaFin,           // Y-m-d ✅
                'hora_inicio'       => $request->hora_inicio,
                'hora_fin'          => $request->hora_fin,
                'razon'             => $request->razon,
            ]);

            return response()->json(['success' => 1]);

        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }


    // =============== GENERAR PERMISO - INCAPACIDAD ==========================================================


    public function indexGenerarPermisoIncapacidad()
    {
        $temaPredeterminado = $this->getTemaPredeterminado();

        $arrayTipoIncapacidad = PermisosTipoIncapacidad::orderBy('nombre', 'asc')->get();
        $arrayRiesgo = PermisoRiesgo::orderBy('nombre', 'asc')->get();

        return view('backend.permisos.generar.generarpermisoincapacidad', compact('temaPredeterminado', 'arrayTipoIncapacidad', 'arrayRiesgo'));
    }



    public function guardarPermisoIncapacidad(Request $request)
    {
        try {

            $empleado = PermisosEmpleados::find($request->empleado_id);

            if (!$empleado) {
                return response()->json(['success' => 0, 'message' => 'Empleado no encontrado']);
            }

            // ===============================
            // 📅 CONVERTIR FECHAS A Y-m-d (formato MySQL)
            // ===============================
            $fecha      = Carbon::parse($request->fecha)->format('Y-m-d');
            $fechaInicio = Carbon::parse($request->fecha_inicio)->format('Y-m-d');
            $fechaFin    = Carbon::parse($request->fecha_fin)->format('Y-m-d');

            $fechaInicioHosp = $request->hospitalizacion && $request->fecha_inicio_hospitalizacion
                ? Carbon::parse($request->fecha_inicio_hospitalizacion)->format('Y-m-d')
                : null;

            $fechaFinHosp = $request->hospitalizacion && $request->fecha_fin_hospitalizacion
                ? Carbon::parse($request->fecha_fin_hospitalizacion)->format('Y-m-d')
                : null;

            // ===============================
            // 🔎 VERIFICAR DUPLICADOS
            // Regla: mismo empleado + solapamiento de fechas inicio/fin
            // ===============================
            if (!$request->forzar_guardado) {

                $duplicados = PermisoIncapacidad::where('id_empleado', $request->empleado_id)
                    ->where(function ($q) use ($fechaInicio, $fechaFin) {
                        $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                            ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                            ->orWhere(function ($q2) use ($fechaInicio, $fechaFin) {
                                $q2->where('fecha_inicio', '<=', $fechaInicio)
                                    ->where('fecha_fin', '>=', $fechaFin);
                            });
                    })
                    ->get();

                if ($duplicados->count() > 0) {

                    $lista = $duplicados->map(function ($p) {
                        return [
                            'fecha'       => $p->fecha,
                            'fecha_inicio' => $p->fecha_inicio,
                            'fecha_fin'    => $p->fecha_fin,
                            'dias'         => $p->dias,
                            'diagnostico'  => $p->diagnostico ?? 'Sin diagnóstico',
                        ];
                    });

                    return response()->json([
                        'success'    => 2,
                        'message'    => 'Se encontraron incapacidades similares',
                        'duplicados' => $lista,
                    ]);
                }
            }

            // ===============================
            // 💾 GUARDAR
            // ===============================
            $unidad = PermisosUnidades::find($empleado->id_unidad);
            $cargo  = PermisosCargos::find($empleado->id_cargo);

            $incapacidad = new PermisoIncapacidad();
            $incapacidad->id_empleado               = $request->empleado_id;
            $incapacidad->unidad                    = $unidad->nombre ?? null;
            $incapacidad->cargo                     = $cargo->nombre ?? null;
            $incapacidad->fecha                     = $fecha;           // Y-m-d ✅
            $incapacidad->id_tipo_incapacidad       = $request->tipo_incapacidad_id;
            $incapacidad->id_riesgo                 = $request->riesgo_id;
            $incapacidad->fecha_inicio              = $fechaInicio;     // Y-m-d ✅
            $incapacidad->dias                      = $request->dias;
            $incapacidad->fecha_fin                 = $fechaFin;        // Y-m-d ✅
            $incapacidad->diagnostico               = $request->diagnostico;
            $incapacidad->numero                    = $request->numero;
            $incapacidad->hospitalizacion           = $request->hospitalizacion;
            $incapacidad->fecha_inicio_hospitalizacion = $fechaInicioHosp; // Y-m-d ✅
            $incapacidad->fecha_fin_hospitalizacion    = $fechaFinHosp;    // Y-m-d ✅
            $incapacidad->save();

            return response()->json(['success' => 1, 'message' => 'Incapacidad guardada exitosamente']);

        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function informacionPermisoIncapacidad(Request $request)
    {
        $empleadoId = $request->empleado_id;
        $anioActual = Carbon::parse($request->fecha)->year;

        $permisos = PermisoIncapacidad::where('id_empleado', $empleadoId)
            ->whereYear('fecha_inicio', $anioActual)
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        $data = $permisos->map(function ($item) {
            return [
                'fecha_inicio' => Carbon::parse($item->fecha_inicio)->format('d-m-Y'),
                'fecha_fin' => Carbon::parse($item->fecha_fin)->format('d-m-Y'),
                'dias' => $item->dias,
                'diagnostico' => $item->diagnostico
            ];
        });

        return response()->json([
            'success' => true,
            'anio' => $anioActual,
            'total' => $permisos->count(),
            'incapacidades' => $data
        ]);
    }




    // =============== GENERAR PERMISO - INCAPACIDAD ==========================================================


    public function indexGenerarPermisoEnfermedad()
    {
        $temaPredeterminado = $this->getTemaPredeterminado();

        return view('backend.permisos.generar.generarpermisoenfermedad', compact('temaPredeterminado'));
    }


    public function informacionPermisoEnfermedad(Request $request)
    {
        $empleadoId = $request->empleado_id;
        $anioActual = Carbon::parse($request->fecha)->year;

        $permisos = PermisoEnfermedad::where('id_empleado', $empleadoId)
            ->whereYear('fecha', $anioActual)
            ->orderBy('fecha', 'desc')
            ->get();

        $data = $permisos->map(function ($item) {

            $info = [
                'fecha' => Carbon::parse($item->fecha)->format('d-m-Y'),
                'razon' => $item->razon,
                'condicion' => $item->condicion ?? 0, // 0 = Día completo, 1 = Fraccionado
            ];

            if ($item->condicion == 0) {

                // Día completo
                $info['tipo'] = 'Día completo';
                $info['fecha_inicio'] = $item->fecha_inicio
                    ? Carbon::parse($item->fecha_inicio)->format('d-m-Y')
                    : null;

                $info['fecha_fin'] = $item->fecha_fin
                    ? Carbon::parse($item->fecha_fin)->format('d-m-Y')
                    : null;

            } else {

                // Fraccionado
                $info['tipo'] = 'Fraccionado';

                if ($item->hora_inicio && $item->hora_fin) {

                    $horaInicio = Carbon::parse($item->hora_inicio);
                    $horaFin = Carbon::parse($item->hora_fin);

                    $minutosTotales = $horaFin->diffInMinutes($horaInicio);

                    $horas = floor($minutosTotales / 60);
                    $minutos = $minutosTotales % 60;

                    $info['hora_inicio'] = $horaInicio->format('H:i');
                    $info['hora_fin'] = $horaFin->format('H:i');

                    $info['horas_texto'] = $horas . 'h ' . $minutos . 'm';
                }
            }

            return $info;
        });

        return response()->json([
            'success' => true,
            'anio' => $anioActual,
            'total' => $permisos->count(),
            'permisos' => $data
        ]);
    }



    public function guardarPermisoEnfermedad(Request $request)
    {
        $regla = array(
            'empleado_id'  => 'required',
            'condicion'    => 'required',
            'fechaEntrego' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);
        if ($validar->fails()) { return ['success' => 0]; }

        try {

            $empleado = PermisosEmpleados::find($request->empleado_id);

            if (!$empleado) {
                return response()->json(['success' => 0, 'message' => 'Empleado no encontrado']);
            }

            // ===============================
            // 📅 CONVERTIR FECHAS A Y-m-d (formato MySQL)
            // ===============================
            $fechaEntrego     = Carbon::parse($request->fechaEntrego)->format('Y-m-d');

            $fechaFraccionado = $request->fecha_fraccionado
                ? Carbon::parse($request->fecha_fraccionado)->format('Y-m-d')
                : null;

            $fechaInicio      = $request->fecha_inicio
                ? Carbon::parse($request->fecha_inicio)->format('Y-m-d')
                : null;

            $fechaFin         = $request->fecha_fin
                ? Carbon::parse($request->fecha_fin)->format('Y-m-d')
                : null;

            // ===============================
            // 🔎 VERIFICAR DUPLICADOS
            // ===============================
            if (!$request->forzar_guardado) {

                $query = PermisoEnfermedad::where('id_empleado', $request->empleado_id)
                    ->where('condicion', $request->condicion);

                if ($request->condicion == 1) {

                    // Fraccionado: misma fecha + solapamiento de horas
                    $query->where('fecha_fraccionado', $fechaFraccionado)
                        ->where(function ($q) use ($request) {
                            $q->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fin])
                                ->orWhereBetween('hora_fin', [$request->hora_inicio, $request->hora_fin])
                                ->orWhere(function ($q2) use ($request) {
                                    $q2->where('hora_inicio', '<=', $request->hora_inicio)
                                        ->where('hora_fin', '>=', $request->hora_fin);
                                });
                        });

                } else {

                    // Completo: solapamiento de fechas (Y-m-d ordena bien en SQL)
                    $query->where(function ($q) use ($fechaInicio, $fechaFin) {
                        $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                            ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                            ->orWhere(function ($q2) use ($fechaInicio, $fechaFin) {
                                $q2->where('fecha_inicio', '<=', $fechaInicio)
                                    ->where('fecha_fin', '>=', $fechaFin);
                            });
                    });
                }

                $duplicados = $query->get();

                if ($duplicados->count() > 0) {

                    $lista = $duplicados->map(function ($p) {
                        return [
                            'fecha'             => $p->fecha,
                            'condicion'         => $p->condicion == 1 ? 'Fraccionado' : 'Completo',
                            'fecha_fraccionado' => $p->fecha_fraccionado,
                            'fecha_inicio'      => $p->fecha_inicio,
                            'fecha_fin'         => $p->fecha_fin,
                            'hora_inicio'       => $p->hora_inicio,
                            'hora_fin'          => $p->hora_fin,
                            'razon'             => $p->razon ?? 'Sin descripción',
                        ];
                    });

                    return response()->json([
                        'success'    => 2,
                        'message'    => 'Se encontraron permisos similares',
                        'duplicados' => $lista,
                    ]);
                }
            }

            // ===============================
            // 💾 GUARDAR
            // ===============================
            $unidad = PermisosUnidades::find($empleado->id_unidad);
            $cargo  = PermisosCargos::find($empleado->id_cargo);

            PermisoEnfermedad::create([
                'id_empleado'       => $request->empleado_id,
                'unidad'            => $unidad->nombre ?? null,
                'cargo'             => $cargo->nombre ?? null,
                'fecha'             => $fechaEntrego,       // Y-m-d ✅
                'condicion'         => $request->condicion,
                'fecha_fraccionado' => $fechaFraccionado,   // Y-m-d ✅
                'fecha_inicio'      => $fechaInicio,        // Y-m-d ✅
                'fecha_fin'         => $fechaFin,           // Y-m-d ✅
                'hora_inicio'       => $request->hora_inicio,
                'hora_fin'          => $request->hora_fin,
                'razon'             => $request->razon,
                'unidad_atencion'   => $request->unidadAtencion,
                'especialidad'      => $request->especialidad,
                'condicion_medica'  => $request->condicionMedica,
            ]);

            return response()->json(['success' => 1]);

        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }



    // =============== GENERAR PERMISO - CONSULTA MEDICA ==========================================================


    public function indexGenerarPermisoConsultaMedica()
    {
        $temaPredeterminado = $this->getTemaPredeterminado();

        return view('backend.permisos.generar.generarpermisoconsultamedica', compact('temaPredeterminado'));
    }


    public function informacionPermisoConsultaMedica(Request $request)
    {
        $empleadoId = $request->empleado_id;
        $anioActual = Carbon::parse($request->fecha)->year;

        $permisos = PermisoConsultaMedica::where('id_empleado', $empleadoId)
            ->whereYear('fecha', $anioActual)
            ->orderBy('fecha', 'desc')
            ->get();

        $data = $permisos->map(function ($item) {

            $info = [
                'fecha' => Carbon::parse($item->fecha)->format('d-m-Y'),
                'razon' => $item->razon,
                'condicion' => $item->condicion ?? 0
            ];

            if ($item->condicion == 0) {

                // Día completo
                $info['tipo'] = 'Día completo';
                $info['fecha_inicio'] = $item->fecha_inicio
                    ? Carbon::parse($item->fecha_inicio)->format('d-m-Y')
                    : null;

                $info['fecha_fin'] = $item->fecha_fin
                    ? Carbon::parse($item->fecha_fin)->format('d-m-Y')
                    : null;

            } else {

                // Fraccionado
                $info['tipo'] = 'Fraccionado';

                if ($item->hora_inicio && $item->hora_fin) {

                    $horaInicio = Carbon::parse($item->hora_inicio);
                    $horaFin = Carbon::parse($item->hora_fin);

                    $minutosTotales = $horaFin->diffInMinutes($horaInicio);

                    $horas = floor($minutosTotales / 60);
                    $minutos = $minutosTotales % 60;

                    $info['hora_inicio'] = $horaInicio->format('H:i');
                    $info['hora_fin'] = $horaFin->format('H:i');

                    $texto = '';

                    if ($horas > 0) {
                        $texto .= $horas . ' h ';
                    }

                    if ($minutos > 0) {
                        $texto .= $minutos . ' m';
                    }

                    $info['horas_texto'] = trim($texto);
                }
            }

            return $info;
        });

        return response()->json([
            'success' => true,
            'anio' => $anioActual,
            'total' => $permisos->count(),
            'permisos' => $data
        ]);
    }



    public function guardarPermisoConsultaMedica(Request $request)
    {
        $regla = array(
            'empleado_id'  => 'required',
            'condicion'    => 'required',
            'fechaEntrego' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);
        if ($validar->fails()) { return ['success' => 0]; }

        try {

            $empleado = PermisosEmpleados::find($request->empleado_id);

            if (!$empleado) {
                return response()->json(['success' => 0, 'message' => 'Empleado no encontrado']);
            }

            // ===============================
            // 📅 CONVERTIR FECHAS A Y-m-d (formato MySQL)
            // ===============================
            $fechaEntrego     = Carbon::parse($request->fechaEntrego)->format('Y-m-d');

            $fechaFraccionado = $request->fecha_fraccionado
                ? Carbon::parse($request->fecha_fraccionado)->format('Y-m-d')
                : null;

            $fechaInicio      = $request->fecha_inicio
                ? Carbon::parse($request->fecha_inicio)->format('Y-m-d')
                : null;

            $fechaFin         = $request->fecha_fin
                ? Carbon::parse($request->fecha_fin)->format('Y-m-d')
                : null;

            // ===============================
            // 🔎 VERIFICAR DUPLICADOS
            // ===============================
            if (!$request->forzar_guardado) {

                $query = PermisoConsultaMedica::where('id_empleado', $request->empleado_id)
                    ->where('condicion', $request->condicion);

                if ($request->condicion == 1) {

                    // Fraccionado: misma fecha + solapamiento de horas
                    $query->where('fecha_fraccionado', $fechaFraccionado)
                        ->where(function ($q) use ($request) {
                            $q->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fin])
                                ->orWhereBetween('hora_fin', [$request->hora_inicio, $request->hora_fin])
                                ->orWhere(function ($q2) use ($request) {
                                    $q2->where('hora_inicio', '<=', $request->hora_inicio)
                                        ->where('hora_fin', '>=', $request->hora_fin);
                                });
                        });

                } else {

                    // Completo: solapamiento de fechas (Y-m-d ordena bien en SQL)
                    $query->where(function ($q) use ($fechaInicio, $fechaFin) {
                        $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                            ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                            ->orWhere(function ($q2) use ($fechaInicio, $fechaFin) {
                                $q2->where('fecha_inicio', '<=', $fechaInicio)
                                    ->where('fecha_fin', '>=', $fechaFin);
                            });
                    });
                }

                $duplicados = $query->get();

                if ($duplicados->count() > 0) {

                    $lista = $duplicados->map(function ($p) {
                        return [
                            'fecha'             => $p->fecha,
                            'condicion'         => $p->condicion == 1 ? 'Fraccionado' : 'Completo',
                            'fecha_fraccionado' => $p->fecha_fraccionado,
                            'fecha_inicio'      => $p->fecha_inicio,
                            'fecha_fin'         => $p->fecha_fin,
                            'hora_inicio'       => $p->hora_inicio,
                            'hora_fin'          => $p->hora_fin,
                            'razon'             => $p->razon ?? 'Sin descripción',
                        ];
                    });

                    return response()->json([
                        'success'    => 2,
                        'message'    => 'Se encontraron permisos similares',
                        'duplicados' => $lista,
                    ]);
                }
            }

            // ===============================
            // 💾 GUARDAR
            // ===============================
            $unidad = PermisosUnidades::find($empleado->id_unidad);
            $cargo  = PermisosCargos::find($empleado->id_cargo);

            PermisoConsultaMedica::create([
                'id_empleado'       => $request->empleado_id,
                'unidad'            => $unidad->nombre ?? null,
                'cargo'             => $cargo->nombre ?? null,
                'fecha'             => $fechaEntrego,       // Y-m-d ✅
                'condicion'         => $request->condicion,
                'fecha_fraccionado' => $fechaFraccionado,   // Y-m-d ✅
                'fecha_inicio'      => $fechaInicio,        // Y-m-d ✅
                'fecha_fin'         => $fechaFin,           // Y-m-d ✅
                'hora_inicio'       => $request->hora_inicio,
                'hora_fin'          => $request->hora_fin,
                'razon'             => $request->razon,
                'unidad_atencion'   => $request->unidadAtencion,
                'especialidad'      => $request->especialidad,
                'condicion_medica'  => $request->condicionMedica,
            ]);

            return response()->json(['success' => 1]);

        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }







    // =============== GENERAR PERMISO - PERSONAL  ==========================================================



    public function indexGenerarPermisoPersonal()
    {
        $temaPredeterminado = $this->getTemaPredeterminado();

        return view('backend.permisos.generar.generarpermisopersonal', compact('temaPredeterminado'));
    }



    public function informacionPermisoPersonal(Request $request)
    {
        $empleadoId = $request->empleado_id;

        // 📅 Año según fecha enviada o año actual
        if ($request->has('fecha') && $request->fecha) {
            $anio = Carbon::parse($request->fecha)->year;
        } else {
            $anio = now()->year;
        }

        // Obtener permisos del año
        $permisos = PermisoPersonal::where('id_empleado', $empleadoId)
            ->whereYear('fecha', $anio)
            ->orderBy('fecha', 'desc')
            ->get();

        $permisosConGoce = $permisos->where('goce', 1);
        $permisosSinGoce = $permisos->where('goce', 0);

        // =====================================================
        // CALCULAR MINUTOS CON GOCE
        // =====================================================
        $totalMinutosConGoce = 0;

        foreach ($permisosConGoce as $permiso) {

            if ($permiso->condicion == 0) {
                // Día completo (8h = 480 min)
                $inicio = Carbon::parse($permiso->fecha_inicio);
                $fin = Carbon::parse($permiso->fecha_fin);
                $dias = $inicio->diffInDays($fin) + 1;
                $totalMinutosConGoce += $dias * 480;

            } else {
                // Fraccionado (evitar pérdida por segundos)
                $horaInicio = Carbon::parse($permiso->hora_inicio)->seconds(0);
                $horaFin = Carbon::parse($permiso->hora_fin)->seconds(0);

                $minutos = $horaInicio->diffInSeconds($horaFin) / 60;
                $totalMinutosConGoce += round($minutos);
            }
        }

        // =====================================================
        // CALCULAR MINUTOS SIN GOCE
        // =====================================================
        $totalMinutosSinGoce = 0;

        foreach ($permisosSinGoce as $permiso) {

            if ($permiso->condicion == 0) {
                $inicio = Carbon::parse($permiso->fecha_inicio);
                $fin = Carbon::parse($permiso->fecha_fin);
                $dias = $inicio->diffInDays($fin) + 1;
                $totalMinutosSinGoce += $dias * 480;

            } else {
                $horaInicio = Carbon::parse($permiso->hora_inicio)->seconds(0);
                $horaFin = Carbon::parse($permiso->hora_fin)->seconds(0);

                $minutos = $horaInicio->diffInSeconds($horaFin) / 60;
                $totalMinutosSinGoce += round($minutos);
            }
        }

        // =====================================================
        // LÍMITES
        // =====================================================
        $limiteConGoce = 5 * 480;   // 5 días
        $limiteSinGoce = 60 * 480;  // 60 días

        $disponibleConGoce = max($limiteConGoce - $totalMinutosConGoce, 0);
        $disponibleSinGoce = max($limiteSinGoce - $totalMinutosSinGoce, 0);

        // =====================================================
        // FUNCIÓN PARA FORMATEAR MINUTOS A DÍAS/HORAS/MIN
        // =====================================================
        $formatearTiempo = function ($minutos) {
            $dias = floor($minutos / 480);
            $resto = $minutos % 480;

            $horas = floor($resto / 60);
            $mins = $resto % 60;

            $texto = '';

            if ($dias > 0) {
                $texto .= $dias . ($dias == 1 ? ' día ' : ' días ');
            }

            if ($horas > 0) {
                $texto .= $horas . ($horas == 1 ? ' hora ' : ' horas ');
            }

            if ($mins > 0 || $texto == '') {
                $texto .= $mins . ($mins == 1 ? ' minuto' : ' minutos');
            }

            return trim($texto);
        };

        // =====================================================
        // DETALLE DE PERMISOS
        // =====================================================
        $data = $permisos->map(function ($item) {

            if ($item->condicion == 0) {
                $inicio = Carbon::parse($item->fecha_inicio);
                $fin = Carbon::parse($item->fecha_fin);
                $dias = $inicio->diffInDays($fin) + 1;

                $detalle = "Del " . $inicio->format('d/m/Y') .
                    " al " . $fin->format('d/m/Y') .
                    " ({$dias} " . ($dias == 1 ? 'día' : 'días') . ")";

            } else {
                $horaInicio = Carbon::parse($item->hora_inicio);
                $horaFin = Carbon::parse($item->hora_fin);

                $minutos = $horaInicio->diffInSeconds($horaFin) / 60;
                $horas = floor($minutos / 60);
                $mins = $minutos % 60;

                $detalle = "De " . $horaInicio->format('H:i') .
                    " a " . $horaFin->format('H:i') .
                    " ({$horas}h {$mins}m)";
            }

            return [
                'fecha' => Carbon::parse($item->fecha)->format('d/m/Y'),
                'tipo' => $item->condicion == 0 ? 'Completo' : 'Fraccionado',
                'goce' => $item->goce == 1 ? 'Con goce' : 'Sin goce',
                'detalle' => $detalle,
                'razon' => $item->razon
            ];
        });

        // =====================================================
        // RESPUESTA
        // =====================================================
        return response()->json([
            'success' => true,
            'anio' => $anio,
            'total' => $permisos->count(),
            'con_goce' => [
                'usado_minutos' => $totalMinutosConGoce,
                'limite_minutos' => $limiteConGoce,
                'disponible_minutos' => $disponibleConGoce,
                'cantidad' => $permisosConGoce->count(),
                'usado_texto' => $formatearTiempo($totalMinutosConGoce),
                'disponible_texto' => $formatearTiempo($disponibleConGoce)
            ],
            'sin_goce' => [
                'usado_minutos' => $totalMinutosSinGoce,
                'limite_minutos' => $limiteSinGoce,
                'disponible_minutos' => $disponibleSinGoce,
                'cantidad' => $permisosSinGoce->count(),
                'usado_texto' => $formatearTiempo($totalMinutosSinGoce),
                'disponible_texto' => $formatearTiempo($disponibleSinGoce)
            ],
            'permisos' => $data
        ]);
    }



    // MÉTODO: guardarPermisoPersonal
    public function guardarPermisoPersonal(Request $request)
    {
        $regla = array(
            'empleado_id'  => 'required',
            'condicion'    => 'required',
            'fechaEntrego' => 'required',
            'goce_sueldo'  => 'required|boolean',
        );

        $validar = Validator::make($request->all(), $regla);
        if ($validar->fails()) {
            return ['success' => 0, 'message' => 'Datos incompletos'];
        }

        try {

            $empleado = PermisosEmpleados::find($request->empleado_id);

            if (!$empleado) {
                return response()->json(['success' => 0, 'message' => 'Empleado no encontrado']);
            }

            // ===============================
            // 📅 CONVERTIR FECHAS A Y-m-d (formato MySQL)
            // ===============================
            $fechaEntrego     = \Carbon\Carbon::parse($request->fechaEntrego)->format('Y-m-d');

            $fechaFraccionado = $request->fecha_fraccionado
                ? \Carbon\Carbon::parse($request->fecha_fraccionado)->format('Y-m-d')
                : null;

            $fechaInicio      = $request->fecha_inicio
                ? \Carbon\Carbon::parse($request->fecha_inicio)->format('Y-m-d')
                : null;

            $fechaFin         = $request->fecha_fin
                ? \Carbon\Carbon::parse($request->fecha_fin)->format('Y-m-d')
                : null;

            // ===============================
            // 📅 Año para validación de límites
            // ===============================
            $anio = \Carbon\Carbon::parse($fechaEntrego)->year;

            // ===============================
            // 🔢 VALIDAR LÍMITE DE PERMISOS
            // ===============================
            $permisosDelAnio = PermisoPersonal::where('id_empleado', $request->empleado_id)
                ->whereYear('fecha', $anio)
                ->where('goce', $request->goce_sueldo)
                ->get();

            $totalMinutosUsados  = 0;
            $minutosSolicitados  = 0;

            foreach ($permisosDelAnio as $permiso) {
                if ($permiso->condicion == 0) {
                    $inicio = \Carbon\Carbon::parse($permiso->fecha_inicio);
                    $fin    = \Carbon\Carbon::parse($permiso->fecha_fin);
                    $totalMinutosUsados += ($inicio->diffInDays($fin) + 1) * 480;
                } else {
                    $horaInicio = \Carbon\Carbon::parse($permiso->hora_inicio);
                    $horaFin    = \Carbon\Carbon::parse($permiso->hora_fin);
                    $totalMinutosUsados += $horaFin->diffInMinutes($horaInicio);
                }
            }

            if ($request->condicion == 0) {
                $inicio = \Carbon\Carbon::parse($fechaInicio);
                $fin    = \Carbon\Carbon::parse($fechaFin);
                $minutosSolicitados = ($inicio->diffInDays($fin) + 1) * 480;
            } else {
                $minutosSolicitados = $request->duracion_minutos;
            }

            if ($request->goce_sueldo == 1) {
                $limiteMinutos = 5 * 480; // 5 días = 2400 minutos
                $tipoGoce      = 'Con goce de sueldo';
            } else {
                $limiteMinutos = 60 * 480; // 60 días = 28800 minutos
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
            // 🔎 VERIFICAR DUPLICADOS
            // Regla: mismo empleado + misma condicion + solapamiento (sin importar goce)
            // ===============================
            if (!$request->forzar_guardado) {

                $query = PermisoPersonal::where('id_empleado', $request->empleado_id)
                    ->where('condicion', $request->condicion);

                if ($request->condicion == 1) {

                    // Fraccionado: misma fecha + solapamiento de horas
                    $query->where('fecha_fraccionado', $fechaFraccionado)
                        ->where(function ($q) use ($request) {
                            $q->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fin])
                                ->orWhereBetween('hora_fin', [$request->hora_inicio, $request->hora_fin])
                                ->orWhere(function ($q2) use ($request) {
                                    $q2->where('hora_inicio', '<=', $request->hora_inicio)
                                        ->where('hora_fin', '>=', $request->hora_fin);
                                });
                        });

                } else {

                    // Completo: solapamiento de fechas
                    $query->where(function ($q) use ($fechaInicio, $fechaFin) {
                        $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                            ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                            ->orWhere(function ($q2) use ($fechaInicio, $fechaFin) {
                                $q2->where('fecha_inicio', '<=', $fechaInicio)
                                    ->where('fecha_fin', '>=', $fechaFin);
                            });
                    });
                }

                $duplicados = $query->get();

                if ($duplicados->count() > 0) {

                    $lista = $duplicados->map(function ($p) {
                        return [
                            'fecha'             => $p->fecha,
                            'condicion'         => $p->condicion == 1 ? 'Fraccionado' : 'Completo',
                            'goce'              => $p->goce == 1 ? 'Con goce' : 'Sin goce',
                            'fecha_fraccionado' => $p->fecha_fraccionado,
                            'fecha_inicio'      => $p->fecha_inicio,
                            'fecha_fin'         => $p->fecha_fin,
                            'hora_inicio'       => $p->hora_inicio,
                            'hora_fin'          => $p->hora_fin,
                            'razon'             => $p->razon ?? 'Sin descripción',
                        ];
                    });

                    return response()->json([
                        'success'    => 2,
                        'message'    => 'Se encontraron permisos similares',
                        'duplicados' => $lista,
                    ]);
                }
            }

            // ===============================
            // 💾 GUARDAR
            // ===============================
            $unidad = PermisosUnidades::find($empleado->id_unidad);
            $cargo  = PermisosCargos::find($empleado->id_cargo);

            PermisoPersonal::create([
                'id_empleado'       => $request->empleado_id,
                'unidad'            => $unidad->nombre ?? null,
                'cargo'             => $cargo->nombre ?? null,
                'fecha'             => $fechaEntrego,       // Y-m-d ✅
                'condicion'         => $request->condicion,
                'fecha_fraccionado' => $fechaFraccionado,   // Y-m-d ✅
                'goce'              => $request->goce_sueldo,
                'fecha_inicio'      => $fechaInicio,        // Y-m-d ✅
                'fecha_fin'         => $fechaFin,           // Y-m-d ✅
                'hora_inicio'       => $request->hora_inicio,
                'hora_fin'          => $request->hora_fin,
                'razon'             => $request->razon,
            ]);

            return response()->json(['success' => 1]);

        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }







    // =============== GENERAR PERMISO - COMPENSATORIO ==========================================================

    public function indexGenerarPermisoCompensatorio()
    {
        $temaPredeterminado = $this->getTemaPredeterminado();

        return view('backend.permisos.generar.generarpermisocompensatorio', compact('temaPredeterminado'));
    }


    public function informacionPermisoCompensatorio(Request $request)
    {
        $empleadoId = $request->empleado_id;
        $anioActual = Carbon::parse($request->fecha)->year;

        $permisos = PermisoCompensatorio::where('id_empleado', $empleadoId)
            ->whereYear('fecha', $anioActual)
            ->orderBy('fecha', 'desc')
            ->get();

        $data = $permisos->map(function ($item) {

            $info = [
                'fecha' => Carbon::parse($item->fecha)->format('d-m-Y'),
                'razon' => $item->razon,
                'condicion' => $item->condicion, // 0: Día completo, 1: Fraccionado
            ];

            if ($item->condicion == 0) {

                // Día completo
                $info['tipo'] = 'Día completo';
                $info['fecha_inicio'] = $item->fecha_inicio
                    ? Carbon::parse($item->fecha_inicio)->format('d-m-Y')
                    : null;

                $info['fecha_fin'] = $item->fecha_fin
                    ? Carbon::parse($item->fecha_fin)->format('d-m-Y')
                    : null;

            } else {

                // Fraccionado
                $info['tipo'] = 'Fraccionado';

                if ($item->hora_inicio && $item->hora_fin) {

                    $horaInicio = Carbon::parse($item->hora_inicio);
                    $horaFin = Carbon::parse($item->hora_fin);

                    $minutosTotales = $horaFin->diffInMinutes($horaInicio);

                    $horas = floor($minutosTotales / 60);
                    $minutos = $minutosTotales % 60;

                    $info['hora_inicio'] = $horaInicio->format('H:i');
                    $info['hora_fin'] = $horaFin->format('H:i');

                    $info['minutos_totales'] = $minutosTotales;
                    $info['horas_texto'] = $horas . 'h ' . $minutos . 'm';
                }
            }

            return $info;
        });

        return response()->json([
            'success' => true,
            'anio' => $anioActual,
            'total' => $permisos->count(),
            'permisos' => $data
        ]);
    }



    public function guardarPermisoCompensatorio(Request $request)
    {
        $regla = array(
            'empleado_id'  => 'required',
            'condicion'    => 'required',
            'fechaEntrego' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);
        if ($validar->fails()) { return ['success' => 0]; }

        try {

            $empleado = PermisosEmpleados::find($request->empleado_id);

            if (!$empleado) {
                return response()->json(['success' => 0, 'message' => 'Empleado no encontrado']);
            }

            // ===============================
            // 📅 CONVERTIR FECHAS A Y-m-d (formato MySQL)
            // ===============================
            $fechaEntrego     = Carbon::parse($request->fechaEntrego)->format('Y-m-d');

            $fechaFraccionado = $request->fecha_fraccionado
                ? Carbon::parse($request->fecha_fraccionado)->format('Y-m-d')
                : null;

            $fechaInicio      = $request->fecha_inicio
                ? Carbon::parse($request->fecha_inicio)->format('Y-m-d')
                : null;

            $fechaFin         = $request->fecha_fin
                ? Carbon::parse($request->fecha_fin)->format('Y-m-d')
                : null;

            // ===============================
            // 🔎 VERIFICAR DUPLICADOS
            // ===============================
            if (!$request->forzar_guardado) {

                $query = PermisoCompensatorio::where('id_empleado', $request->empleado_id)
                    ->where('condicion', $request->condicion);

                if ($request->condicion == 1) {

                    // Fraccionado: misma fecha + solapamiento de horas
                    $query->where('fecha_fraccionado', $fechaFraccionado)
                        ->where(function ($q) use ($request) {
                            $q->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fin])
                                ->orWhereBetween('hora_fin', [$request->hora_inicio, $request->hora_fin])
                                ->orWhere(function ($q2) use ($request) {
                                    $q2->where('hora_inicio', '<=', $request->hora_inicio)
                                        ->where('hora_fin', '>=', $request->hora_fin);
                                });
                        });

                } else {

                    // Completo: solapamiento de fechas (Y-m-d ordena bien en SQL)
                    $query->where(function ($q) use ($fechaInicio, $fechaFin) {
                        $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                            ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                            ->orWhere(function ($q2) use ($fechaInicio, $fechaFin) {
                                $q2->where('fecha_inicio', '<=', $fechaInicio)
                                    ->where('fecha_fin', '>=', $fechaFin);
                            });
                    });
                }

                $duplicados = $query->get();

                if ($duplicados->count() > 0) {

                    $lista = $duplicados->map(function ($p) {
                        return [
                            'fecha'             => $p->fecha,
                            'condicion'         => $p->condicion == 1 ? 'Fraccionado' : 'Completo',
                            'fecha_fraccionado' => $p->fecha_fraccionado,
                            'fecha_inicio'      => $p->fecha_inicio,
                            'fecha_fin'         => $p->fecha_fin,
                            'hora_inicio'       => $p->hora_inicio,
                            'hora_fin'          => $p->hora_fin,
                            'razon'             => $p->razon ?? 'Sin descripción',
                        ];
                    });

                    return response()->json([
                        'success'    => 2,
                        'message'    => 'Se encontraron permisos similares',
                        'duplicados' => $lista,
                    ]);
                }
            }

            // ===============================
            // 💾 GUARDAR
            // ===============================
            $unidad = PermisosUnidades::find($empleado->id_unidad);
            $cargo  = PermisosCargos::find($empleado->id_cargo);

            PermisoCompensatorio::create([
                'id_empleado'       => $request->empleado_id,
                'unidad'            => $unidad->nombre ?? null,
                'cargo'             => $cargo->nombre ?? null,
                'fecha'             => $fechaEntrego,       // Y-m-d ✅
                'condicion'         => $request->condicion,
                'fecha_fraccionado' => $fechaFraccionado,   // Y-m-d ✅
                'fecha_inicio'      => $fechaInicio,        // Y-m-d ✅
                'fecha_fin'         => $fechaFin,           // Y-m-d ✅
                'hora_inicio'       => $request->hora_inicio,
                'hora_fin'          => $request->hora_fin,
                'razon'             => $request->razon,
            ]);

            return response()->json(['success' => 1]);

        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }















}
