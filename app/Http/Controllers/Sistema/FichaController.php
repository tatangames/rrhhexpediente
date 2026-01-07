<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Distrito;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FichaController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    private function getTemaPredeterminado(){
        return Auth::guard('admin')->user()->tema;
    }

    public function vistaFichaForm(){
        $temaPredeterminado = $this->getTemaPredeterminado();

        $nombre = Auth::guard('admin')->user()->nombre;

        $arrayDistritos = Distrito::orderBy('nombre', 'ASC')->get();
        $arrayCargos = Cargo::orderBy('nombre', 'ASC')->get();
        $arrayUnidades = Unidad::orderBy('nombre', 'ASC')->get();



        return view('backend.empleado.ficha.vistaficha', compact('temaPredeterminado', 'nombre',
        'arrayDistritos', 'arrayCargos', 'arrayUnidades'));
    }




}
