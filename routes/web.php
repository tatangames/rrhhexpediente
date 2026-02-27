<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sistema\LoginController;
use App\Http\Controllers\Sistema\ControlController;
use App\Http\Controllers\Sistema\RolesController;
use App\Http\Controllers\Sistema\PerfilController;
use App\Http\Controllers\Sistema\PermisoController;
use App\Http\Controllers\Sistema\FichaController;
use App\Http\Controllers\Sistema\ConfiguracionController;
use App\Http\Controllers\Sistema\RRHHController;

use App\Http\Controllers\Permiso\ConfigPermisoController;
use App\Http\Controllers\Permiso\ReportesPermisoController;
use App\Http\Controllers\Permiso\HistorialPermisoController;

use App\Http\Controllers\Evaluacion\EvaluacionController;
use App\Http\Controllers\Evaluacion\JefeEvaluacionController;

use App\Http\Controllers\Permiso\ReportesExcelPermisoController;


Route::get('/', [LoginController::class,'vistaLoginForm'])->name('login.admin');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');

Route::get('/registro', [LoginController::class, 'vistaRegistroForm'])->name('login.registro.admin');
Route::post('/registro/empleado', [LoginController::class, 'registroEmpleado']);


// --- CONTROL WEB ---
Route::get('/panel', [ControlController::class,'indexRedireccionamiento'])->name('admin.panel');

// --- ROLES ---
Route::get('/admin/roles/index', [RolesController::class,'index'])->name('admin.roles.index');
Route::get('/admin/roles/tabla', [RolesController::class,'tablaRoles']);
Route::get('/admin/roles/lista/permisos/{id}', [RolesController::class,'vistaPermisos']);
Route::get('/admin/roles/permisos/tabla/{id}', [RolesController::class,'tablaRolesPermisos']);
Route::post('/admin/roles/permiso/borrar', [RolesController::class, 'borrarPermiso']);
Route::post('/admin/roles/permiso/agregar', [RolesController::class, 'agregarPermiso']);
Route::get('/admin/roles/permisos/lista', [RolesController::class,'listaTodosPermisos']);
Route::get('/admin/roles/permisos-todos/tabla', [RolesController::class,'tablaTodosPermisos']);
Route::post('/admin/roles/borrar-global', [RolesController::class, 'borrarRolGlobal']);

// --- PERMISOS ---
Route::get('/admin/permisos/index', [PermisoController::class,'index'])->name('admin.permisos.index');
Route::get('/admin/permisos/tabla', [PermisoController::class,'tablaUsuarios']);
Route::post('/admin/permisos/nuevo-usuario', [PermisoController::class, 'nuevoUsuario']);
Route::post('/admin/permisos/info-usuario', [PermisoController::class, 'infoUsuario']);
Route::post('/admin/permisos/editar-usuario', [PermisoController::class, 'editarUsuario']);
Route::post('/admin/permisos/nuevo-rol', [PermisoController::class, 'nuevoRol']);
Route::post('/admin/permisos/extra-nuevo', [PermisoController::class, 'nuevoPermisoExtra']);
Route::post('/admin/permisos/extra-borrar', [PermisoController::class, 'borrarPermisoGlobal']);

// actualizar empleado por RRHH
Route::post('/admin/editar/empleado', [PermisoController::class, 'editarUsuarioPorRRHH']);



// --- PERFIL ---
Route::get('/admin/editar-perfil/index', [PerfilController::class,'indexEditarPerfil'])->name('admin.perfil');
Route::post('/admin/editar-perfil/actualizar', [PerfilController::class, 'editarUsuario']);

Route::get('sin-permisos', [ControlController::class,'indexSinPermiso'])->name('no.permisos.index');

// actualizar Tema
Route::post('/admin/actualizar/tema', [ControlController::class, 'actualizarTema'])->name('admin.tema.update');

// === CARGO EMPLEADO ===
Route::get('/admin/cargo/index', [ConfiguracionController::class,'vistaCargo'])->name('admin.cargo.index');
Route::get('/admin/cargo/tabla', [ConfiguracionController::class,'tablaCargo']);
Route::post('/admin/cargo/nuevo', [ConfiguracionController::class,'nuevoCargo']);
Route::post('/admin/cargo/informacion', [ConfiguracionController::class,'infoCargo']);
Route::post('/admin/cargo/editar', [ConfiguracionController::class,'actualizarCargo']);

// === UNIDAD ===
Route::get('/admin/unidad/index', [ConfiguracionController::class,'indexUnidad'])->name('admin.unidad.index');
Route::get('/admin/unidad/tabla', [ConfiguracionController::class,'tablaUnidad']);
Route::post('/admin/unidad/nuevo', [ConfiguracionController::class,'nuevoUnidad']);
Route::post('/admin/unidad/informacion', [ConfiguracionController::class,'informacionUnidad']);
Route::post('/admin/unidad/editar', [ConfiguracionController::class,'actualizarUnidad']);

// === NIVEL ACADÉMICO ===
Route::get('/admin/nivelacademico/index', [ConfiguracionController::class,'indexNivelAcademico'])->name('admin.nivelacademico.index');
Route::get('/admin/nivelacademico/tabla', [ConfiguracionController::class,'tablaNivelAcademico']);
Route::post('/admin/nivelacademico/nuevo', [ConfiguracionController::class,'nuevoNivelAcademico']);
Route::post('/admin/nivelacademico/informacion', [ConfiguracionController::class,'informacionNivelAcademico']);
Route::post('/admin/nivelacademico/editar', [ConfiguracionController::class,'actualizarNivelAcademico']);

// === LISTA DE EMPLEADOS ===
Route::get('/admin/empleados/listado/index', [RRHHController::class,'indexListadoEmpleados'])->name('admin.empleados.index');
Route::get('/admin/empleados/listado/tabla', [RRHHController::class,'tablaListadoEmpleados']);

// Vista para editar Ficha
Route::get('/admin/empleados/ficha/vista/{idadmin}', [RRHHController::class,'indexVistaFichaEditar']);
// descargar PDF
Route::get('/admin/empleados/reporte/pdf/{idadmin}', [RRHHController::class,'descargarPdfFicha']);
// vista de documentos
Route::get('/admin/empleados/documentos/listado/{idadmin}', [RRHHController::class,'indexListadoDocumentos']);
// descargar documento
Route::get('/admin/empleado/documento/download/{id}', [RRHHController::class, 'descargarDocumento'])
    ->name('admin.media.download');



// FICHA EMPLEADO
Route::get('/empleado/ficha/index', [FichaController::class,'vistaFichaForm'])->name('empleado.ficha.index');
Route::post('/empleado/ficha/actualizar', [FichaController::class,'actualizarFicha']);
Route::get('/empleado/reporte/pdf', [FichaController::class,'pdfFicha']);

// EXPEDIENTE EMPLEADO
Route::get('/empleado/expediente/index', [FichaController::class,'vistaExpedienteForm'])->name('empleado.expediente.index');
// subir documentos
Route::post('/empleado/media/upload', [FichaController::class, 'upload'])->name('empleado.media.upload');
// borrar documento
Route::post('/empleado/eliminar/media/{id}', [FichaController::class, 'destroy'])->name('empleado.media.destroy');
// descargar documento
Route::get('/empleado/media/download/{id}', [FichaController::class, 'download'])->name('empleado.media.download');



// ================================ PERMISOS DE EMPLEADOS ============================================


// TIPO DE PERMISO
Route::get('/admin/tipopermiso/index', [ConfigPermisoController::class,'indexTipoPermiso'])->name('permisos.tipopermiso.index');
Route::get('/admin/tipopermiso/tabla', [ConfigPermisoController::class,'tablaTipoPermiso']);
Route::post('/admin/tipopermiso/nuevo', [ConfigPermisoController::class,'nuevoTipoPermiso']);
Route::post('/admin/tipopermiso/informacion', [ConfigPermisoController::class,'informacionTipoPermiso']);
Route::post('/admin/tipopermiso/editar', [ConfigPermisoController::class,'actualizarTipoPermiso']);

// RIESGOS
Route::get('/admin/riesgos/index', [ConfigPermisoController::class,'indexRiesgos'])->name('permisos.riesgos.index');
Route::get('/admin/riesgos/tabla', [ConfigPermisoController::class,'tablaRiesgos']);
Route::post('/admin/riesgos/nuevo', [ConfigPermisoController::class,'nuevoRiesgos']);
Route::post('/admin/riesgos/informacion', [ConfigPermisoController::class,'informacionRiesgos']);
Route::post('/admin/riesgos/editar', [ConfigPermisoController::class,'actualizarRiesgos']);

// TIPO DE INCAPACIDAD
Route::get('/admin/tipoincapacidad/index', [ConfigPermisoController::class,'indexTipoIncapacidad'])->name('tipoincapacidad.index');
Route::get('/admin/tipoincapacidad/tabla', [ConfigPermisoController::class,'tablaTipoIncapacidad']);
Route::post('/admin/tipoincapacidad/nuevo', [ConfigPermisoController::class,'nuevoTipoIncapacidad']);
Route::post('/admin/tipoincapacidad/informacion', [ConfigPermisoController::class,'informacionTipoIncapacidad']);
Route::post('/admin/tipoincapacidad/editar', [ConfigPermisoController::class,'actualizarTipoIncapacidad']);

// EMPLEADOS
Route::get('/admin/empleados/index', [ConfigPermisoController::class,'indexEmpleados'])->name('permiso.empleados.index');
Route::get('/admin/empleados/tabla', [ConfigPermisoController::class,'tablaEmpleados']);
Route::post('/admin/empleados/nuevo', [ConfigPermisoController::class,'nuevoEmpleados']);
Route::post('/admin/empleados/informacion', [ConfigPermisoController::class,'informacionEmpleados']);
Route::post('/admin/empleados/editar', [ConfigPermisoController::class,'actualizarEmpleados']);

// BUSCAR EMPLEADO
Route::get('/admin/empleados/buscar', [PermisoController::class, 'buscarPorNombre']);


// GENERAR PERMISO - OTROS
Route::get('/admin/tipopermiso/otros/index', [PermisoController::class,'indexGenerarPermisoOtros'])->name('generar.tipopermiso.otros');
Route::post('/admin/empleados/infopermiso/otros', [PermisoController::class, 'informacionPermisoOtros']);
Route::post('/admin/guardar/permiso/otros', [PermisoController::class, 'guardarPermisoOtros']);

// GENERAR PERMISO - INCAPACIDAD
Route::get('/admin/tipopermiso/incapacidad/index', [PermisoController::class,'indexGenerarPermisoIncapacidad'])->name('generar.tipopermiso.incapacidad');
Route::post('/admin/guardar/permiso/incapacidad', [PermisoController::class, 'guardarPermisoIncapacidad']);
Route::post('/admin/empleados/infopermiso/incapacidad', [PermisoController::class, 'informacionPermisoIncapacidad']);

// GENERAR PERMISO - ENFERMEDAD
Route::get('/admin/tipopermiso/enfermedad/index', [PermisoController::class,'indexGenerarPermisoEnfermedad'])->name('generar.tipopermiso.enfermedad');
Route::post('/admin/guardar/permiso/enfermedad', [PermisoController::class, 'guardarPermisoEnfermedad']);
Route::post('/admin/empleados/infopermiso/enfermedad', [PermisoController::class, 'informacionPermisoEnfermedad']);

// GENERAR PERMISO - CONSULTA MEDICA
Route::get('/admin/tipopermiso/consultamedica/index', [PermisoController::class,'indexGenerarPermisoConsultaMedica'])->name('generar.tipopermiso.consultamedica');
Route::post('/admin/guardar/permiso/consultamedica', [PermisoController::class, 'guardarPermisoConsultaMedica']);
Route::post('/admin/empleados/infopermiso/consultamedica', [PermisoController::class, 'informacionPermisoConsultaMedica']);


// GENERAR PERMISO - PERSONAL
Route::get('/admin/tipopermiso/personal/index', [PermisoController::class,'indexGenerarPermisoPersonal'])->name('generar.tipopermiso.personal');
Route::post('/admin/empleados/infopermiso/personal', [PermisoController::class, 'informacionPermisoPersonal']);
Route::post('/admin/guardar/permiso/personal', [PermisoController::class, 'guardarPermisoPersonal']);


// GENERAR PERMISO - COMPENSATORIO
Route::get('/admin/tipopermiso/compensatorio/index', [PermisoController::class,'indexGenerarPermisoCompensatorio'])->name('generar.tipopermiso.compensatorio');
Route::post('/admin/empleados/infopermiso/compensatorio', [PermisoController::class, 'informacionPermisoCompensatorio']);
Route::post('/admin/guardar/permiso/compensatorio', [PermisoController::class, 'guardarPermisoCompensatorio']);




// HISTORIAL - OTROS
Route::get('/admin/historial/otros/index', [HistorialPermisoController::class,'indexHistorialPermisoOtros'])->name('historial.permisos.otros');
Route::get('/admin/historial/otros/tabla', [HistorialPermisoController::class,'tablaHistorialPermisoOtros']);
Route::post('/admin/historial/otros/informacion', [HistorialPermisoController::class,'informacionHistorialPermisoOtros']);
Route::post('/admin/historial/otros/actualizar', [HistorialPermisoController::class,'actualizarHistorialPermisoOtros']);
Route::post('/admin/historial/otros/borrar', [HistorialPermisoController::class,'borrarHistorialPermisoOtros']);

// HISTORIAL - ENFERMEDAD
Route::get('/admin/historial/enfermedad/index', [HistorialPermisoController::class,'indexHistorialPermisoEnfermedad'])->name('historial.permisos.enfermedad');
Route::get('/admin/historial/enfermedad/tabla', [HistorialPermisoController::class,'tablaHistorialPermisoEnfermedad']);
Route::post('/admin/historial/enfermedad/informacion', [HistorialPermisoController::class,'informacionHistorialPermisoEnfermedad']);
Route::post('/admin/historial/enfermedad/actualizar', [HistorialPermisoController::class,'actualizarHistorialPermisoEnfermedad']);
Route::post('/admin/historial/enfermedad/borrar', [HistorialPermisoController::class,'borrarHistorialPermisoEnfermedad']);

// HISTORIAL - CONSULTA MEDICA
Route::get('/admin/historial/consultamedica/index', [HistorialPermisoController::class,'indexHistorialPermisoConsultaMedica'])->name('historial.permisos.consultamedica');
Route::get('/admin/historial/consultamedica/tabla', [HistorialPermisoController::class,'tablaHistorialPermisoConsultaMedica']);
Route::post('/admin/historial/consultamedica/informacion', [HistorialPermisoController::class,'informacionHistorialPermisoConsultaMedica']);
Route::post('/admin/historial/consultamedica/actualizar', [HistorialPermisoController::class,'actualizarHistorialPermisoConsultaMedica']);
Route::post('/admin/historial/consultamedica/borrar', [HistorialPermisoController::class,'borrarHistorialPermisoConsultaMedica']);

// HISTORIAL - COMPENSATORIO
Route::get('/admin/historial/compensatorio/index', [HistorialPermisoController::class,'indexHistorialPermisoCompensatorio'])->name('historial.permisos.compensatorio');
Route::get('/admin/historial/compensatorio/tabla', [HistorialPermisoController::class,'tablaHistorialPermisoCompensatorio']);
Route::post('/admin/historial/compensatorio/informacion', [HistorialPermisoController::class,'informacionHistorialPermisoCompensatorio']);
Route::post('/admin/historial/compensatorio/actualizar', [HistorialPermisoController::class,'actualizarHistorialPermisoCompensatorio']);
Route::post('/admin/historial/compensatorio/borrar', [HistorialPermisoController::class,'borrarHistorialPermisoCompensatorio']);

// HISTORIAL - INCAPACIDAD
Route::get('/admin/historial/incapacidad/index', [HistorialPermisoController::class,'indexHistorialPermisoIncapacidad'])->name('historial.permisos.incapacidad');
Route::get('/admin/historial/incapacidad/tabla', [HistorialPermisoController::class,'tablaHistorialPermisoIncapacidad']);
Route::post('/admin/historial/incapacidad/informacion', [HistorialPermisoController::class,'informacionHistorialPermisoIncapacidad']);
Route::post('/admin/historial/incapacidad/actualizar', [HistorialPermisoController::class,'actualizarHistorialPermisoIncapacidad']);
Route::post('/admin/historial/incapacidad/borrar', [HistorialPermisoController::class,'borrarHistorialPermisoIncapacidad']);

// HISTORIAL - PERSONAL
Route::get('/admin/historial/personal/index', [HistorialPermisoController::class,'indexHistorialPermisoPersonal'])->name('historial.permisos.personal');
Route::get('/admin/historial/personal/tabla', [HistorialPermisoController::class,'tablaHistorialPermisoPersonal']);
Route::post('/admin/historial/personal/informacion', [HistorialPermisoController::class,'informacionHistorialPermisoPersonal']);
Route::post('/admin/historial/personal/actualizar', [HistorialPermisoController::class,'actualizarHistorialPermisoPersonal']);
Route::post('/admin/historial/personal/borrar', [HistorialPermisoController::class,'borrarHistorialPermisoPersonal']);


// REPORTES - GENERAL
Route::get('/admin/reportes/general/index', [ReportesPermisoController::class,'indexReportesGeneral'])->name('reporte.general.index');

// Generar Permiso PDF y EXCEL
Route::post('/admin/reportes/permiso/pdf', [ReportesPermisoController::class, 'generarReportePermisoPDF'])->name('permiso.pdf.generar');
Route::post('/admin/reportes/permiso/excel', [ReportesExcelPermisoController::class, 'generarReportePermisoEXCEL'])->name('permiso.excel.generar');

























// *-***************************************************************************************
// ============================ MODULO EVALUACION ==========================================
// *-***************************************************************************************


// EVALUACION - REGISTRO
Route::get('/admin/evaluacion/index', [EvaluacionController::class,'indexEvaluacion'])->name('evaluacion.index');
Route::get('/admin/evaluacion/tabla', [EvaluacionController::class,'tablaEvaluacion']);
Route::post('/admin/evaluacion/nuevo', [EvaluacionController::class,'nuevaEvaluacion']);
Route::post('/admin/evaluacion/informacion', [EvaluacionController::class,'informacionEvaluacion']);
Route::post('/admin/evaluacion/editar', [EvaluacionController::class,'editarEvaluacion']);
Route::post('/admin/evaluacion/borrar', [EvaluacionController::class,'borrarEvaluacion']);


// EVALUACION - REGISTRO
Route::get('/admin/evaluacion-detalle/index/{id}', [EvaluacionController::class,'indexEvaluacionDetalle']);
Route::get('/admin/evaluacion-detalle/tabla/{id}', [EvaluacionController::class,'tablaEvaluacionDetalle']);
Route::post('/admin/evaluacion-detalle/nuevo', [EvaluacionController::class,'nuevaEvaluacionDetalle']);
Route::post('/admin/evaluacion-detalle/informacion', [EvaluacionController::class,'informacionEvaluacionDetalle']);
Route::post('/admin/evaluacion-detalle/editar', [EvaluacionController::class,'editarEvaluacionDetalle']);
Route::post('/admin/evaluacion-detalle/borrar', [EvaluacionController::class,'borrarEvaluacionDetalle']);

// === CARGO EMPLEADO - EVALUACION ===
Route::get('/admin/cargo-evaluacion/index', [EvaluacionController::class,'vistaCargoEvaluacion'])->name('admin.cargo.evaluacion.index');
Route::get('/admin/cargo-evaluacion/tabla', [EvaluacionController::class,'tablaCargoEvaluacion']);
Route::post('/admin/cargo-evaluacion/nuevo', [EvaluacionController::class,'nuevoCargoEvaluacion']);
Route::post('/admin/cargo-evaluacion/informacion', [EvaluacionController::class,'infoCargoEvaluacion']);
Route::post('/admin/cargo-evaluacion/editar', [EvaluacionController::class,'actualizarCargoEvaluacion']);
Route::post('/admin/cargo-evaluacion/borrar', [EvaluacionController::class,'borrarCargoEvaluacion']);


// === UNIDAD - EVALUACION ===
Route::get('/admin/unidad-evaluacion/index', [EvaluacionController::class,'indexUnidadEvaluacion'])->name('admin.unidad.evaluacion.index');
Route::get('/admin/unidad-evaluacion/tabla', [EvaluacionController::class,'tablaUnidadEvaluacion']);
Route::post('/admin/unidad-evaluacion/nuevo', [EvaluacionController::class,'nuevoUnidadEvaluacion']);
Route::post('/admin/unidad-evaluacion/informacion', [EvaluacionController::class,'informacionUnidadEvaluacion']);
Route::post('/admin/unidad-evaluacion/editar', [EvaluacionController::class,'actualizarUnidadEvaluacion']);
Route::post('/admin/unidad-evaluacion/borrar', [EvaluacionController::class,'borrarUnidadEvaluacion']);


// === DEPENDENCIA JERARQUICA- EVALUACION ===
Route::get('/admin/dependencia-evaluacion/index', [EvaluacionController::class,'indexDependenciaEvaluacion'])->name('admin.dependencia.evaluacion.index');
Route::get('/admin/dependencia-evaluacion/tabla', [EvaluacionController::class,'tablaDependenciaEvaluacion']);
Route::post('/admin/dependencia-evaluacion/nuevo', [EvaluacionController::class,'nuevoDependenciaEvaluacion']);
Route::post('/admin/dependencia-evaluacion/informacion', [EvaluacionController::class,'informacionDependenciaEvaluacion']);
Route::post('/admin/dependencia-evaluacion/editar', [EvaluacionController::class,'actualizarDependenciaEvaluacion']);
Route::post('/admin/dependencia-evaluacion/borrar', [EvaluacionController::class,'borrarDependenciaEvaluacion']);



// VISTA PUBLICA PARA LLENAR LA EVALUACION
Route::get('/evaluacion/empleado', [JefeEvaluacionController::class,'indexLlenarEvaluacion']);
// Guardar evaluación
Route::post('/evaluacion/generar/pdf', [JefeEvaluacionController::class, 'registrarEvaluacion'])->name('evaluacion.registrar');

