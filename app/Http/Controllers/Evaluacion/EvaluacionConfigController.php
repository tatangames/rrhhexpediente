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

class EvaluacionConfigController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // =====================================================================
    //  UNIDAD EVALUACION
    // =====================================================================

    public function indexUnidadEvaluacion()
    {
        return view('backend.evaluacion.configuracion.unidad.vistaunidadevaluacion');
    }

    public function tablaUnidadEvaluacion()
    {
        $arrayUnidades = EvaluacionUnidad::orderBy('nombre', 'ASC')->get();

        return view('backend.evaluacion.configuracion.unidad.tablaunidadevaluacion', compact('arrayUnidades'));
    }

    public function nuevoUnidadEvaluacion(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'nombre' => 'required|max:100',
        ]);

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
            Log::info('Error nuevoUnidadEvaluacion: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function informacionUnidadEvaluacion(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($info = EvaluacionUnidad::where('id', $request->id)->first()) {
            return ['success' => 1, 'info' => $info];
        }

        return ['success' => 2];
    }

    public function actualizarUnidadEvaluacion(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'id'     => 'required',
            'nombre' => 'required|max:100',
        ]);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {
            EvaluacionUnidad::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('Error actualizarUnidadEvaluacion: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function borrarUnidadEvaluacion(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {
            EvaluacionUnidad::where('id', $request->id)->delete();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('Error borrarUnidadEvaluacion: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // =====================================================================
    //  DEPENDENCIA JERARQUICA EVALUACION
    // =====================================================================

    public function indexDependenciaEvaluacion()
    {
        return view('backend.evaluacion.configuracion.dependencia.vistadependenciaevaluacion');
    }

    public function tablaDependenciaEvaluacion()
    {
        $arrayDependencias = EvaluacionDependencias::orderBy('nombre', 'ASC')->get();

        return view('backend.evaluacion.configuracion.dependencia.tabladependenciaevaluacion', compact('arrayDependencias'));
    }

    public function nuevoDependenciaEvaluacion(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'nombre' => 'required|max:100',
        ]);

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
            Log::info('Error nuevoDependenciaEvaluacion: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function informacionDependenciaEvaluacion(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($info = EvaluacionDependencias::where('id', $request->id)->first()) {
            return ['success' => 1, 'info' => $info];
        }

        return ['success' => 2];
    }

    public function actualizarDependenciaEvaluacion(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'id'     => 'required',
            'nombre' => 'required|max:100',
        ]);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {
            EvaluacionDependencias::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('Error actualizarDependenciaEvaluacion: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function borrarDependenciaEvaluacion(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {
            EvaluacionDependencias::where('id', $request->id)->delete();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('Error borrarDependenciaEvaluacion: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // =====================================================================
    //  CARGO EVALUACION
    // =====================================================================

    public function vistaCargoEvaluacion()
    {
        return view('backend.evaluacion.configuracion.cargo.vistacargoevaluacion');
    }

    public function tablaCargoEvaluacion()
    {
        $arrayCargos = EvaluacionCargo::orderBy('nombre', 'ASC')->get();

        return view('backend.evaluacion.configuracion.cargo.tablacargoevaluacion', compact('arrayCargos'));
    }

    public function nuevoCargoEvaluacion(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'nombre' => 'required|max:100',
        ]);

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
            Log::info('Error nuevoCargoEvaluacion: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function infoCargoEvaluacion(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($info = EvaluacionCargo::where('id', $request->id)->first()) {
            return ['success' => 1, 'info' => $info];
        }

        return ['success' => 2];
    }

    public function actualizarCargoEvaluacion(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'id'     => 'required',
            'nombre' => 'required|max:100',
        ]);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {
            EvaluacionCargo::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('Error actualizarCargoEvaluacion: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function borrarCargoEvaluacion(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {
            EvaluacionCargo::where('id', $request->id)->delete();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('Error borrarCargoEvaluacion: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }



}
