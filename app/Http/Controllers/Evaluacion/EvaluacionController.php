<?php

namespace App\Http\Controllers\Evaluacion;

use App\Http\Controllers\Controller;
use App\Models\EvaluacionCargo;
use App\Models\EvaluacionDependencias;
use App\Models\EvaluacionDetalle;
use App\Models\EvaluacionTipo;
use App\Models\EvaluacionTitulo;
use App\Models\EvaluacionUnidad;
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


    public function indexRegistrarTituloEvaluacion()
    {
        $arrayTipos = EvaluacionTipo::orderBy('nombre', 'ASC')->get();

        return view('backend.evaluacion.registro.vistaregistrotitulo', compact('arrayTipos'));
    }


    public function nuevaTituloEvaluacion(Request $request)
    {
        $regla = array(
            'id_evaluacion_tipo' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            // Posición máxima dentro del mismo tipo
            $ultimaPosicion = EvaluacionTitulo::where('id_evaluacion_tipo', $request->id_evaluacion_tipo)
                ->max('posicion');

            $nuevaPosicion = $ultimaPosicion ? $ultimaPosicion + 1 : 1;

            $dato = new EvaluacionTitulo();
            $dato->id_evaluacion_tipo = $request->id_evaluacion_tipo;
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


    public function indexListadoTitulo()
    {
        $arrayTipos = EvaluacionTipo::orderBy('nombre', 'ASC')->get();

        return view('backend.evaluacion.historial.vistahistorial', compact('arrayTipos'));
    }

    public function tablaListadoTitulo($idtipo)
    {
        $arrayTitulos = EvaluacionTitulo::where('id_evaluacion_tipo', $idtipo)
            ->orderBy('posicion', 'ASC')
            ->get();

        return view('backend.evaluacion.historial.tablahistorial', compact('arrayTitulos'));
    }

    public function informacionListadoTitulo(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = EvaluacionTitulo::where('id', $request->id)->first();

        if (!$info) {
            return ['success' => 0];
        }

        return ['success' => 1, 'info' => $info];
    }

    public function editarListadoTitulo(Request $request)
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

        EvaluacionTitulo::where('id', $request->id)->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'estado' => $request->estado,
        ]);

        return ['success' => 1];
    }

    public function borrarListadoTitulo(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $info = EvaluacionTitulo::where('id', $request->id)->first();

            if (!$info) {
                DB::rollback();
                return ['success' => 0];
            }

            $idTipo = $info->id_evaluacion_tipo;

            // YA BORRA EN CASCADA
            $info->delete();

            // Reordenar posiciones del mismo tipo para que no queden huecos
            $arrayRestante = EvaluacionTitulo::where('id_evaluacion_tipo', $idTipo)
                ->orderBy('posicion', 'ASC')
                ->get();

            $posicion = 1;
            foreach ($arrayRestante as $item) {
                $item->posicion = $posicion;
                $item->save();
                $posicion++;
            }

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function posicionListadoTitulo(Request $request)
    {
        $regla = array(
            'orden' => 'required|array',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $posicion = 1;
            foreach ($request->orden as $id) {
                EvaluacionTitulo::where('id', $id)->update([
                    'posicion' => $posicion
                ]);
                $posicion++;
            }

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }









    //* *******************************************************************************************



    public function indexDetalleEvaluacion($id)
    {
        $infoTitulo = EvaluacionTitulo::findOrFail($id);

        return view('backend.evaluacion.historial.detalle.vistadetallehistorial', compact('infoTitulo'));
    }

    public function tablaDetalleEvaluacion($id)
    {
        $arrayDetalle = EvaluacionDetalle::where('id_evaluacion_titulo', $id)
            ->orderBy('posicion', 'ASC')
            ->get();

        return view('backend.evaluacion.historial.detalle.tabladetallehistorial', compact('arrayDetalle'));
    }

    public function nuevoDetalleEvaluacion(Request $request)
    {
        $regla = array(
            'id_evaluacion_titulo' => 'required',
            'puntos' => 'required|numeric|min:0',
        );

        // nombre

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            // Posición máxima dentro del mismo título
            $ultimaPosicion = EvaluacionDetalle::where('id_evaluacion_titulo', $request->id_evaluacion_titulo)
                ->max('posicion');

            $nuevaPosicion = $ultimaPosicion ? $ultimaPosicion + 1 : 1;

            $dato = new EvaluacionDetalle();
            $dato->id_evaluacion_titulo = $request->id_evaluacion_titulo;
            $dato->nombre = $request->nombre;
            $dato->puntos = $request->puntos;
            $dato->posicion = $nuevaPosicion;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function informacionDetalleEvaluacion(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = EvaluacionDetalle::where('id', $request->id)->first();

        if (!$info) {
            return ['success' => 0];
        }

        return ['success' => 1, 'info' => $info];
    }

    public function editarDetalleEvaluacion(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'puntos' => 'required|numeric|min:0',
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

    public function borrarDetalleEvaluacion(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $info = EvaluacionDetalle::where('id', $request->id)->first();

            if (!$info) {
                DB::rollback();
                return ['success' => 0];
            }

            $idTitulo = $info->id_evaluacion_titulo;

            $info->delete();

            // Reordenar posiciones del mismo título
            $arrayRestante = EvaluacionDetalle::where('id_evaluacion_titulo', $idTitulo)
                ->orderBy('posicion', 'ASC')
                ->get();

            $posicion = 1;
            foreach ($arrayRestante as $item) {
                $item->posicion = $posicion;
                $item->save();
                $posicion++;
            }

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function posicionDetalleEvaluacion(Request $request)
    {
        $regla = array(
            'orden' => 'required|array',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $posicion = 1;
            foreach ($request->orden as $id) {
                EvaluacionDetalle::where('id', $id)->update([
                    'posicion' => $posicion
                ]);
                $posicion++;
            }

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }












}
