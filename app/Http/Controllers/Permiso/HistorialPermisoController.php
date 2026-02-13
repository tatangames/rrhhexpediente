<?php

namespace App\Http\Controllers\Permiso;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\PermisoOtro;
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


    public function indexHistorialPermisoOtros()
    {
        $temaPredeterminado =  $this->getTemaPredeterminado();

        return view('backend.permisos.historial.otros.vistapermisosotroseditar', compact('temaPredeterminado'));
    }



    public function tablaHistorialPermisoOtros()
    {
        $arrayPermisos = PermisoOtro::orderBy('fecha', 'asc')->get()
            ->map(function ($item) {

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

        $arrayEmpleados = Empleado::orderBy('nombre', 'asc')->get();

        return ['success' => 1, 'info' => $info, 'arrayEmpleados' => $arrayEmpleados];
    }


    public function actualizarHistorialPermisoOtros(Request $request){


    }




}
