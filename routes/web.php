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

// GENERAR PERMISO
Route::get('/admin/empleddddados/index', [PermisoController::class,'indexGenerarPermiso'])->name('generar.permiso.index');
Route::get('/admin/empleados/buscar', [PermisoController::class, 'buscarPorNombre']);

