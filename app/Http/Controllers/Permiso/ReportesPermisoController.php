<?php

namespace App\Http\Controllers\Permiso;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Illuminate\Support\Facades\Auth;

class ReportesPermisoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    private function getTemaPredeterminado(){
        return Auth::guard('admin')->user()->tema;
    }


    public function indexReportesGeneral(){
        $temaPredeterminado =  $this->getTemaPredeterminado();

        $arrayEmpleados = Empleado::orderBy('nombre', 'ASC')->get();

        return view('backend.permisos.reportes.vistageneralreportes', compact('temaPredeterminado', 'arrayEmpleados'));
    }

}
