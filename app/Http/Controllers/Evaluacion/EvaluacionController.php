<?php

namespace App\Http\Controllers\Evaluacion;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Distrito;
use App\Models\Evaluacion;
use App\Models\EvaluacionCargo;
use App\Models\EvaluacionDependencias;
use App\Models\EvaluacionDetalle;
use App\Models\EvaluacionUnidad;
use App\Models\Unidad;
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
            'estado' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Evaluacion::where('id', $request->id)->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
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





    //*********************************************************************

    public function indexEvaluacionDetalle($id)
    {

        $temaPredeterminado =  $this->getTemaPredeterminado();

        return view('backend.evaluacion.detalle.vistaevaluaciondetalle', compact('id', 'temaPredeterminado'));
    }


    public function tablaEvaluacionDetalle($id)
    {
       $arrayDetalle = EvaluacionDetalle::where('evaluacion_id', $id)
           ->orderBy('posicion', 'ASC')
           ->get();

        return view('backend.evaluacion.detalle.tablaevaluaciondetalle', compact('arrayDetalle'));
    }

    public function nuevaEvaluacionDetalle(Request $request)
    {
        $regla = array(
            'id' => 'required',
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
            $ultimaPosicion = EvaluacionDetalle::where('evaluacion_id', $request->id)->max('posicion');

            // Si no hay registros, será null, entonces empieza en 1
            $nuevaPosicion = $ultimaPosicion ? $ultimaPosicion + 1 : 1;

            $dato = new EvaluacionDetalle();
            $dato->evaluacion_id = $request->id;
            $dato->nombre = $request->nombre;
            $dato->posicion = $nuevaPosicion;
            $dato->puntos = $request->puntos;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function informacionEvaluacionDetalle(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = EvaluacionDetalle::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }


    public function editarEvaluacionDetalle(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'puntos' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        EvaluacionDetalle::where('id', $request->id)->update([
            'nombre' => $request->nombre,
            'puntos' => $request->puntos,
        ]);

        return ['success' => 1];
    }



    public function borrarEvaluacionDetalle(Request $request)
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
















    // =================== CARGO - EVALUACION ==========================================


    public function vistaCargoEvaluacion()
    {
        $temaPredeterminado = $this->getTemaPredeterminado();
        return view('backend.evaluacion.configuracion.cargo.vistacargoevaluacion', compact('temaPredeterminado'));
    }

    public function tablaCargoEvaluacion()
    {
        $arrayCargos = EvaluacionCargo::orderBy('nombre', 'ASC')->get();

        return view('backend.evaluacion.configuracion.cargo.tablacargoevaluacion', compact('arrayCargos'));
    }

    public function nuevoCargoEvaluacion(Request $request)
    {
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new EvaluacionCargo();
            $dato->nombre = $request->nombre;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function infoCargoEvaluacion(Request $request){
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = EvaluacionCargo::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }


    public function actualizarCargoEvaluacion(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        EvaluacionCargo::where('id', $request->id)->update([
            'nombre' => $request->nombre,
        ]);

        return ['success' => 1];
    }


    public function borrarCargoEvaluacion(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        EvaluacionCargo::where('id', $request->id)->delete();

        return ['success' => 1];
    }




    // =================== UNIDAD - EVALUACION ==========================================


    public function indexUnidadEvaluacion()
    {
        $temaPredeterminado =  $this->getTemaPredeterminado();
        return view('backend.evaluacion.configuracion.unidad.vistaunidadevaluacion', compact('temaPredeterminado', ));
    }

    public function tablaUnidadEvaluacion()
    {
        $arrayUnidades = EvaluacionUnidad::orderBy('nombre', 'ASC')->get();
        return view('backend.evaluacion.configuracion.unidad.tablaunidadevaluacion', compact('arrayUnidades'));
    }


    public function nuevoUnidadEvaluacion(Request $request)
    {
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new EvaluacionUnidad();
            $dato->nombre = $request->nombre;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function informacionUnidadEvaluacion(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = EvaluacionUnidad::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarUnidadEvaluacion(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        EvaluacionUnidad::where('id', $request->id)->update([
            'nombre' => $request->nombre,
        ]);

        return ['success' => 1];
    }



    public function borrarUnidadEvaluacion(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        EvaluacionUnidad::where('id', $request->id)->delete();

        return ['success' => 1];
    }






// =================== DEPENDENCIA JERARQUICA - EVALUACION ==========================================


    public function indexDependenciaEvaluacion()
    {
        $temaPredeterminado =  $this->getTemaPredeterminado();
        return view('backend.evaluacion.configuracion.dependencia.vistadependenciaevaluacion', compact('temaPredeterminado' ));
    }

    public function tablaDependenciaEvaluacion()
    {
        $arrayDependecias = EvaluacionDependencias::orderBy('nombre', 'ASC')->get();
        return view('backend.evaluacion.configuracion.dependencia.tabladependenciaevaluacion', compact('arrayDependecias'));
    }


    public function nuevoDependenciaEvaluacion(Request $request)
    {
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new EvaluacionDependencias();
            $dato->nombre = $request->nombre;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function informacionDependenciaEvaluacion(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = EvaluacionDependencias::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarDependenciaEvaluacion(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        EvaluacionDependencias::where('id', $request->id)->update([
            'nombre' => $request->nombre,
        ]);

        return ['success' => 1];
    }



    public function borrarDependenciaEvaluacion(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        EvaluacionDependencias::where('id', $request->id)->delete();

        return ['success' => 1];
    }











}
