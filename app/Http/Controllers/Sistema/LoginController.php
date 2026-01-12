<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Administrador;
use App\Models\FichaEmpleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function __construct(){
        $this->middleware('guest', ['except' => ['logout']]);
    }

    public function vistaLoginForm(){
        return view('frontend.login.vistalogin');
    }

    public function login(Request $request){

        $rules = array(
            'usuario' => 'required',
            'password' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        // si ya habia iniciado sesion, redireccionar
        if (Auth::check()) {
            return ['success'=> 1, 'ruta'=> route('admin.panel')];
        }

        $credenciales = [
            'usuario'    => $request->input('usuario'),
            'password' => $request->input('password'),
        ];

        if (Auth::guard('admin')->attempt($credenciales, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return response()->json([
                'success' => 1,
                'ruta' => route('admin.panel'),
            ]);
        }

        return ['success' => 2];
    }

    public function logout(Request $request){
        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login.admin');
    }


    public function vistaRegistroForm()
    {
        return view('frontend.login.vistaregistro');
    }


    public function registroEmpleado(Request $request)
    {
        $regla = [
            'nombre'   => 'required',
            'dui'   => 'required',
            'usuario'  => 'required',
            'password' => 'required',
        ];

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0]; // campos vacíos
        }

        if (Administrador::where('usuario', $request->usuario)->exists()) {
            return ['success' => 1]; // usuario ya existe
        }


        if (Administrador::where('dui', $request->dui)->exists()) {
            return ['success' => 2]; // dui ya existe
        }

        DB::beginTransaction();

        try {

            $admin = Administrador::create([
                'nombre'   => $request->nombre,
                'usuario'  => $request->usuario,
                'password' => Hash::make($request->password),
                'activo'   => true,
                'dui'      => $request->dui,
                'tema'     => 0,
            ]);

            $admin->assignRole('empleado');


            // CREAR FICHA / TODOS LOS DEMAS CAMPOS SON NULL

            $ficha = new FichaEmpleado();
            $ficha->id_administrador = $admin->id;
            $ficha->nombre = $request->nombre;
            $ficha->dui = $request->dui;
            $ficha->save();


            // 🔥 INICIAR SESIÓN AUTOMÁTICAMENTE
            Auth::guard('admin')->login($admin);
            $request->session()->regenerate();

            DB::commit();

            return [
                'success' => 3,
                'ruta' => route('admin.panel')
            ];

        } catch (\Throwable $e) {
            DB::rollback();
            Log::error('Registro empleado: '.$e->getMessage());

            return ['success' => 99];
        }
    }




}
