<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Administrador;
use App\Models\Extras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PerfilController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    private function getTemaPredeterminado(){
        return Auth::guard('admin')->user()->tema;
    }

    public function indexEditarPerfil(){
        $usuario = auth()->user();
        $temaPredeterminado =  $this->getTemaPredeterminado();

        return view('backend.admin.perfil.vistaperfil', compact('usuario', 'temaPredeterminado'));
    }

    public function editarUsuario(Request $request){

        $regla = array(
            'password' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        $usuario = auth()->user();

        Administrador::where('id', $usuario->id)
            ->update(['password' => bcrypt($request->password)]);

        return ['success' => 1];
    }
}
