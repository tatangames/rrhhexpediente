<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Administrador;
use App\Models\Cargo;
use App\Models\Empleado;
use App\Models\FichaEmpleado;
use App\Models\PermisoIncapacidad;
use App\Models\PermisoOtro;
use App\Models\Riesgo;
use App\Models\TipoIncapacidad;
use App\Models\TipoPermiso;
use App\Models\Unidad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        $empleados = Empleado::where('nombre', 'LIKE', "%$texto%")
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
        $anioActual = now()->year;

        $permisos = PermisoOtro::where('id_empleado', $empleadoId)
            ->whereYear('fecha', $anioActual)
            ->orderBy('fecha', 'desc')
            ->get();

        $data = $permisos->map(function ($item) {
            return [
                'fecha' => Carbon::parse($item->fecha)->format('d-m-Y'),
                'razon' => $item->razon
            ];
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
            'empleado_id' => 'required',
            'condicion' => 'required',
            'fechaEntrego' => 'required',
        );

        //0: DIA COMPLETO
        // fecha_inicio, fecha_fin

        //1: FRACCIONADO
        // hora_inicio, hora_fin, duracion

        // razon, dias_solicitados,

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}


        try {

            $empleado = Empleado::find($request->empleado_id);

            if (!$empleado) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Empleado no encontrado'
                ]);
            }

            // 🔎 Buscar unidad y cargo
            $unidad = Unidad::find($empleado->id_unidad);
            $cargo = Cargo::find($empleado->id_cargo);

            $nombreUnidad = $unidad->nombre ?? null;
            $nombreCargo = $cargo->nombre ?? null;

            PermisoOtro::create([
                'id_empleado' => $request->empleado_id,
                'unidad' => $nombreUnidad,
                'cargo' => $nombreCargo,
                'fecha' => $request->fechaEntrego,
                'condicion' => $request->condicion,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'razon' => $request->razon,
            ]);

            return response()->json(['success' => 1]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => 0,
                'message' => $e->getMessage()
            ]);
        }
    }




    // =============== GENERAR PERMISO - INCAPACIDAD ==========================================================


    public function indexGenerarPermisoIncapacidad()
    {
        $temaPredeterminado = $this->getTemaPredeterminado();

        $arrayTipoIncapacidad = TipoIncapacidad::orderBy('nombre', 'asc')->get();
        $arrayRiesgo = Riesgo::orderBy('nombre', 'asc')->get();

        return view('backend.permisos.generar.generarpermisoincapacidad', compact('temaPredeterminado', 'arrayTipoIncapacidad', 'arrayRiesgo'));
    }



    public function guardarPermisoIncapacidad(Request $request)
    {
        try {

            $empleado = Empleado::find($request->empleado_id);

            if (!$empleado) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Empleado no encontrado'
                ]);
            }

            // 🔎 Buscar unidad y cargo
            $unidad = Unidad::find($empleado->id_unidad);
            $cargo = Cargo::find($empleado->id_cargo);

            $nombreUnidad = $unidad->nombre ?? null;
            $nombreCargo = $cargo->nombre ?? null;

            // Crear registro de incapacidad
            $incapacidad = new PermisoIncapacidad();
            $incapacidad->id_empleado = $request->empleado_id;
            $incapacidad->unidad = $nombreUnidad;
            $incapacidad->cargo = $nombreCargo;
            $incapacidad->fecha = $request->fecha;
            $incapacidad->id_tipo_incapacidad = $request->tipo_incapacidad_id;
            $incapacidad->id_riesgo = $request->riesgo_id;
            $incapacidad->fecha_inicio = $request->fecha_inicio;
            $incapacidad->dias = $request->dias;
            $incapacidad->fecha_fin = $request->fecha_fin;
            $incapacidad->diagnostico = $request->diagnostico;
            $incapacidad->numero = $request->numero;
            $incapacidad->hospitalizacion = $request->hospitalizacion;
            $incapacidad->fecha_inicio_hospitalizacion = $request->hospitalizacion ? $request->fecha_inicio_hospitalizacion : null;
            $incapacidad->fecha_fin_hospitalizacion = $request->hospitalizacion ? $request->fecha_fin_hospitalizacion : null;
            $incapacidad->save();

            return response()->json([
                'success' => 1,
                'message' => 'Incapacidad guardada exitosamente',
                'data' => $incapacidad
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => 0,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function informacionPermisoIncapacidad(Request $request)
    {
        $empleadoId = $request->empleado_id;
        $anioActual = now()->year;

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











}
