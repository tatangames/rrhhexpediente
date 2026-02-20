<?php

namespace App\Http\Controllers\Evaluacion;

use App\Http\Controllers\Controller;
use App\Models\Evaluacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EvaluacionController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    private function getTemaPredeterminado(){
        return Auth::guard('admin')->user()->tema;
    }


    public function indexEvaluacion()
    {
        $temaPredeterminado =  $this->getTemaPredeterminado();
        return view('backend.evaluacion.vistaevaluacion', compact('temaPredeterminado'));
    }

    public function tablaEvaluacion()
    {
        $arrayEvaluacion = Evaluacion::orderBy('posicion', 'ASC')->get();

        return view('backend.evaluacion.tablaevaluacion', compact('arrayEvaluacion'));
    }

    public function nuevaEvaluacion(Request $request)
    {
        $regla = array(
            'nombre' => 'required',
            'puntos' => 'required',
        );

        // descripcion

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {

            // Obtener la posición máxima actual
            $ultimaPosicion = Evaluacion::max('posicion');

            // Si no hay registros, será null, entonces empieza en 1
            $nuevaPosicion = $ultimaPosicion ? $ultimaPosicion + 1 : 1;

            $dato = new Evaluacion();
            $dato->nombre = $request->nombre;
            $dato->descripcion = $request->descripcion;
            $dato->puntos = $request->puntos;
            $dato->posicion = $nuevaPosicion;
            $dato->estado = true;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function informacionEvaluacion(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = Evaluacion::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function editarEvaluacion(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'puntos' => 'required',
            'estado' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Evaluacion::where('id', $request->id)->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'puntos' => $request->puntos,
            'estado' => $request->estado,
        ]);

        return ['success' => 1];
    }



    public function borrarEvaluacion(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        // YA BORRA EN CASCADA
        Evaluacion::where('id', $request->id)->delete();

        return ['success' => 1];
    }





}
