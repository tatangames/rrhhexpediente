@extends('adminlte::page')

@section('title', 'Generar Permiso')

@section('content_header')
    <h1>Generar Permiso - Personal</h1>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content_top_nav_right')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" title="Tema">
            <i id="theme-icon" class="fas fa-sun"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right p-0" style="min-width: 180px">
            <a class="dropdown-item d-flex align-items-center" href="#" data-theme="dark">
                <i class="far fa-moon mr-2"></i> Dark
            </a>
            <a class="dropdown-item d-flex align-items-center" href="#" data-theme="light">
                <i class="far fa-sun mr-2"></i> Light
            </a>
        </div>
    </li>

    <li class="nav-item dropdown">
        <a href="#" class="nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-cogs"></i>
            <span class="d-none d-md-inline">{{ Auth::guard('admin')->user()->nombre }}</span>
        </a>

        <div class="dropdown-menu dropdown-menu-right">
            <a href="{{ route('admin.perfil') }}" class="dropdown-item">
                <i class="fas fa-user mr-2"></i> Editar Perfil
            </a>

            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </li>
@endsection

@section('content')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />

    <style>
        #lista-empleados .item-compacto {
            padding: 6px 10px !important;
            font-size: 14px;
            line-height: 1.2;
        }

        #modalLimitePermiso .modal-header {
            border-bottom: 3px solid #f39c12;
        }

        #modalLimitePermiso .alert-warning {
            border-left: 4px solid #f39c12;
        }

        #mensaje-limite {
            font-weight: 500;
        }

        #modalLimitePermiso .bg-light {
            border-left: 3px solid #17a2b8;
        }
    </style>



    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <!-- COLUMNA IZQUIERDA: DATOS DEL PERMISO -->
                <div class="col-md-6">

                    <!-- Card para Otros -->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Datos del Permiso</h3>
                        </div>
                        <div class="card-body">

                            <!-- Fecha del Permiso -->
                            <div class="form-group col-md-4">
                                <label>Fecha: <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha-entrego">
                            </div>
                            <br>

                            <!-- Goce de Sueldo -->
                            <div class="form-group">
                                <label>Goce de Sueldo: <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px;">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input custom-control-input-success"
                                               type="radio"
                                               id="radio-con-goce"
                                               name="goce-sueldo"
                                               value="1"
                                               checked>
                                        <label for="radio-con-goce" class="custom-control-label">
                                            <i class="fas fa-check-circle text-success"></i>
                                            <strong>Con goce de sueldo</strong>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input custom-control-input-danger"
                                               type="radio"
                                               id="radio-sin-goce"
                                               name="goce-sueldo"
                                               value="0">
                                        <label for="radio-sin-goce" class="custom-control-label">
                                            <i class="fas fa-times-circle text-danger"></i>
                                            <strong>Sin goce de sueldo</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>


                            <hr>


                            <!-- Condición: Fraccionado o Completo -->
                            <div class="form-group">
                                <label>Condición del Permiso: <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px;">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="radio-fraccionado-4" name="condicion-otros" value="fraccionado" checked>
                                        <label for="radio-fraccionado-4" class="custom-control-label">
                                            <strong>Fraccionado</strong> (Por horas)
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="radio-completo-4" name="condicion-otros" value="completo">
                                        <label for="radio-completo-4" class="custom-control-label">
                                            <strong>Completo</strong> (Día(s) completo(s))
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Sección FRACCIONADO -->
                            <!-- Sección FRACCIONADO -->
                            <div id="seccion-fraccionado-4">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Fecha del Permiso: <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="fecha-solicitud-permiso">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Hora de Inicio: <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" id="hora-inicio-4">
                                            <small class="form-text text-muted">Desde qué hora necesita el permiso</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Hora de Fin: <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" id="hora-fin-4">
                                            <small class="form-text text-muted">Hasta qué hora necesita el permiso</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Duración del Permiso:</label>
                                            <input type="text" class="form-control" id="horas-permiso-4" readonly>
                                            <!-- ✅ CAMPO OCULTO PARA GUARDAR LOS MINUTOS EXACTOS -->
                                            <input type="hidden" id="minutos-permiso-4">
                                            <small class="form-text text-muted">Se calcula automáticamente</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección COMPLETO -->
                            <div id="seccion-completo-4" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha de Inicio: <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="fecha-inicio-comp-4">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha de Fin: <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="fecha-fin-comp-4">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Días Solicitados:</label>
                                            <input type="number" class="form-control" id="dias-solicitados-4" readonly>
                                            <small class="form-text text-muted">Se calcula automáticamente</small>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <hr>

                            <!-- Razón -->
                            <div class="form-group">
                                <label>Razón del Permiso:</label>
                                <textarea class="form-control" rows="3" maxlength="800" id="razon-permiso-4" placeholder="Describa brevemente el motivo del permiso"></textarea>
                            </div>


                            <!-- Botón Guardar -->
                            <div class="form-group mt-4">
                                <button type="button" class="btn btn-primary btn-block" id="btn-guardar">
                                    <i class="fas fa-save"></i> Guardar Permiso
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- COLUMNA DERECHA: DATOS DEL EMPLEADO -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Datos del Empleado</h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group" style="position:relative;">
                                <label>Buscar empleado por nombre: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="input-buscar-empleado"
                                       placeholder="Escriba el nombre del empleado">
                                <input type="hidden" id="empleado-id">
                                <div id="lista-empleados"
                                     class="list-group mt-1"
                                     style="display:none; position:absolute; z-index:1000; max-height:300px; overflow-y:auto; width:100%;">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Unidad</label>
                                <input type="text" class="form-control" id="input-unidad" readonly>
                            </div>

                            <div class="form-group">
                                <label>Cargo</label>
                                <input type="text" class="form-control" id="input-cargo" readonly>
                            </div>

                            <div class="form-group mt-4" id="bloque-btn-info" style="display:none;">
                                <button type="button" class="btn btn-info btn-block" id="btn-informacion">
                                    <i class="fas fa-info-circle"></i> Información
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <div class="modal fade" id="modalInfoPermiso" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h4 class="modal-title text-white">
                        <i class="fas fa-info-circle"></i> Información de Permisos del Año <span id="info-anio"></span>
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <!-- Resumen de Permisos -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-check-circle"></i> Con Goce de Sueldo
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="description-block">
                                                <h5 class="description-header text-success" id="info-con-goce-usado">0</h5>
                                                <span class="description-text">Usados</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="description-block">
                                                <h5 class="description-header text-primary" id="info-con-goce-limite">5</h5>
                                                <span class="description-text">Límite</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="description-block">
                                                <h5 class="description-header text-info" id="info-con-goce-disponible">5</h5>
                                                <span class="description-text">Disponibles</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress mt-3" style="height: 25px;">
                                        <div class="progress-bar bg-success" role="progressbar" id="progress-con-goce"
                                             style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                            <span id="progress-con-goce-text">0%</span>
                                        </div>
                                    </div>
                                    <p class="text-center mt-2 mb-0">
                                        <small class="text-muted">Total de solicitudes: <strong id="info-con-goce-cantidad">0</strong></small>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-danger">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-times-circle"></i> Sin Goce de Sueldo
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="description-block">
                                                <h5 class="description-header text-danger" id="info-sin-goce-usado">0</h5>
                                                <span class="description-text">Usados</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="description-block">
                                                <h5 class="description-header text-primary" id="info-sin-goce-limite">60</h5>
                                                <span class="description-text">Límite</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="description-block">
                                                <h5 class="description-header text-info" id="info-sin-goce-disponible">60</h5>
                                                <span class="description-text">Disponibles</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress mt-3" style="height: 25px;">
                                        <div class="progress-bar bg-danger" role="progressbar" id="progress-sin-goce"
                                             style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                            <span id="progress-sin-goce-text">0%</span>
                                        </div>
                                    </div>
                                    <p class="text-center mt-2 mb-0">
                                        <small class="text-muted">Total de solicitudes: <strong id="info-sin-goce-cantidad">0</strong></small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Permisos -->
                    <div class="card">
                        <div class="card-header bg-secondary">
                            <h3 class="card-title">
                                <i class="fas fa-list"></i> Historial Detallado (<span id="info-total">0</span> permisos)
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <ul id="info-fechas" class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                                <!-- Se llenará con JavaScript -->
                            </ul>
                        </div>
                    </div>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal de Advertencia de Límite -->
    <div class="modal fade" id="modalLimitePermiso" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h4 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Límite de Permisos Excedido
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2 text-center d-flex align-items-center justify-content-center">
                            <i class="fas fa-calendar-times fa-4x text-warning"></i>
                        </div>
                        <div class="col-md-10">
                            <h5 class="text-warning mb-3">
                                <strong>No se puede procesar la solicitud</strong>
                            </h5>

                            <div class="alert alert-warning mb-3">
                                <h6 class="mb-2"><strong>Resumen del Año <span id="modal-anio"></span>:</strong></h6>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="mb-1"><i class="fas fa-info-circle"></i> Tipo: <strong id="modal-tipo-goce"></strong></p>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="info-box mb-0 bg-light">
                                                <div class="info-box-content p-2">
                                                    <span class="info-box-text text-muted small">Límite Total</span>
                                                    <span class="info-box-number text-primary" id="modal-limite">
                                                    <strong>-</strong>
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box mb-0 bg-light">
                                                <div class="info-box-content p-2">
                                                    <span class="info-box-text text-muted small">Ya Usados</span>
                                                    <span class="info-box-number text-info" id="modal-usados">
                                                    <strong>-</strong>
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box mb-0 bg-warning">
                                                <div class="info-box-content p-2">
                                                    <span class="info-box-text text-dark small">Solicitando</span>
                                                    <span class="info-box-number text-dark" id="modal-solicitando">
                                                    <strong>-</strong>
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box mb-0 bg-light">
                                                <div class="info-box-content p-2">
                                                    <span class="info-box-text text-muted small">Disponibles</span>
                                                    <span class="info-box-number text-success" id="modal-disponibles">
                                                    <strong>-</strong>
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-danger mt-3 mb-0">
                                <i class="fas fa-times-circle"></i>
                                <strong>No se puede aprobar:</strong> La cantidad solicitada excede el límite disponible.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')

    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>


    <script>

        $(function () {

            // ===============================
            // BUSCAR EMPLEADO
            // ===============================
            $('#input-buscar-empleado').on('keyup', function () {
                let texto = $(this).val();

                if (texto.length < 2) {
                    $('#lista-empleados').hide().html('');
                    $('#empleado-id').val('');
                    $('#input-unidad').val('');
                    $('#input-cargo').val('');
                    $('#bloque-btn-info').hide();
                    return;
                }

                axios.get(urlAdmin + '/admin/empleados/buscar', {
                    params: { q: texto }
                }).then(resp => {
                    let html = '';
                    resp.data.forEach(e => {
                        html += `
                    <button type="button"
                        class="list-group-item list-group-item-action empleado-item item-compacto"
                        data-id="${e.id}"
                        data-unidad="${e.unidad}"
                        data-cargo="${e.cargo}"
                        data-nombre="${e.nombre}">
                        ${e.nombre}
                    </button>
                `;
                    });

                    $('#lista-empleados').html(html).show();
                }).catch(err => {
                    console.error('Error al buscar empleados:', err);
                });
            });

            // ===============================
            // SELECCIONAR EMPLEADO
            // ===============================
            $(document).on('click', '.empleado-item', function () {
                $('#empleado-id').val($(this).data('id'));
                $('#input-buscar-empleado').val($(this).data('nombre'));
                $('#input-unidad').val($(this).data('unidad'));
                $('#input-cargo').val($(this).data('cargo'));
                $('#lista-empleados').hide();

                // Mostrar botón de información
                $('#bloque-btn-info').fadeIn(200);
            });

            // ===============================
            // OCULTAR LISTA SI CLIC AFUERA
            // ===============================
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#input-buscar-empleado, #lista-empleados').length) {
                    $('#lista-empleados').hide();
                }
            });

            // ===============================
            // BOTÓN INFORMACIÓN
            // ===============================
            $(document).on('click', '#btn-informacion', function() {
                let empleadoId = $('#empleado-id').val();

                if (!empleadoId) {
                    toastr.error('No hay empleado seleccionado');
                    return;
                }

                // 📅 Obtener la fecha seleccionada (si existe)
                let fechaSeleccionada = $('#fecha-entrego').val();

                if(!fechaSeleccionada){
                    toastr.error("Seleccionar fecha")
                    return
                }

                openLoading();

                // Petición API para obtener información del empleado
                axios.post(urlAdmin + '/admin/empleados/infopermiso/personal', {
                    empleado_id: empleadoId,
                    fecha: fechaSeleccionada || null
                })
                    .then(resp => {

                        if (resp.data.success) {

                            // Año y total
                            $('#info-anio').text(resp.data.anio);
                            $('#info-total').text(resp.data.total);

                            // CON GOCE DE SUELDO
                            $('#info-con-goce-usado').text(formatearTiempo(resp.data.con_goce.usado_minutos));
                            $('#info-con-goce-limite').text(formatearTiempo(resp.data.con_goce.limite_minutos));
                            $('#info-con-goce-disponible').text(formatearTiempo(resp.data.con_goce.disponible_minutos));
                            $('#info-con-goce-cantidad').text(resp.data.con_goce.cantidad);

                            let porcentajeConGoce = (resp.data.con_goce.usado_minutos / resp.data.con_goce.limite_minutos) * 100;
                            $('#progress-con-goce').css('width', porcentajeConGoce + '%');
                            $('#progress-con-goce').attr('aria-valuenow', porcentajeConGoce);
                            $('#progress-con-goce-text').text(Math.round(porcentajeConGoce) + '%');

                            // SIN GOCE DE SUELDO
                            $('#info-sin-goce-usado').text(formatearTiempo(resp.data.sin_goce.usado_minutos));
                            $('#info-sin-goce-limite').text(formatearTiempo(resp.data.sin_goce.limite_minutos));
                            $('#info-sin-goce-disponible').text(formatearTiempo(resp.data.sin_goce.disponible_minutos));
                            $('#info-sin-goce-cantidad').text(resp.data.sin_goce.cantidad);

                            let porcentajeSinGoce = (resp.data.sin_goce.usado_minutos / resp.data.sin_goce.limite_minutos) * 100;
                            $('#progress-sin-goce').css('width', porcentajeSinGoce + '%');
                            $('#progress-sin-goce').attr('aria-valuenow', porcentajeSinGoce);
                            $('#progress-sin-goce-text').text(Math.round(porcentajeSinGoce) + '%');

                            // LISTA DE PERMISOS
                            let lista = '';

                            if (resp.data.permisos.length === 0) {
                                lista = `
                        <li class="list-group-item text-center text-muted">
                            <i class="fas fa-inbox"></i> No hay permisos registrados para el año ${resp.data.anio}
                        </li>
                    `;
                            } else {
                                resp.data.permisos.forEach(function(item) {

                                    let badgeClass = item.goce === 'Con goce' ? 'badge-success' : 'badge-danger';
                                    let tipoClass = item.tipo === 'Completo' ? 'badge-primary' : 'badge-info';

                                    lista += `
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-2">
                                        <strong><i class="fas fa-calendar"></i> ${item.fecha}</strong>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="badge ${badgeClass}">${item.goce}</span>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="badge ${tipoClass}">${item.tipo}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">${item.detalle}</small>
                                    </div>
                                </div>
                                ${item.razon ? `
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <small><strong>Razón:</strong> ${item.razon}</small>
                                        </div>
                                    </div>
                                ` : ''}
                            </li>
                        `;
                                });
                            }

                            $('#info-fechas').html(lista);

                            $('#modalInfoPermiso').modal('show');
                        }

                    })
                    .catch(err => {
                        toastr.error('Error al obtener la información del empleado');
                    })
                    .finally(() => {
                        closeLoading();
                    });
            });

            // ===============================
            // FUNCIÓN PARA FORMATEAR TIEMPO
            // ===============================
            function formatearTiempo(minutos) {
                if (minutos < 60) {
                    return minutos + ' min';
                }

                const dias = Math.floor(minutos / (8 * 60));
                const minutosRestantes = minutos % (8 * 60);
                const horas = Math.floor(minutosRestantes / 60);
                const mins = minutosRestantes % 60;

                let resultado = '';
                if (dias > 0) resultado += dias + (dias === 1 ? ' día' : ' días');
                if (horas > 0) {
                    if (resultado) resultado += ', ';
                    resultado += horas + (horas === 1 ? ' hora' : ' horas');
                }
                if (mins > 0) {
                    if (resultado) resultado += ', ';
                    resultado += mins + ' min';
                }

                return resultado || '0 min';
            }

            // ===============================
            // BOTÓN GUARDAR
            // ===============================
            $(document).on('click', '#btn-guardar', function () {

                let empleadoId = $('#empleado-id').val();
                let condicionTexto = $('input[name="condicion-otros"]:checked').val();
                let condicion = (condicionTexto === 'fraccionado') ? 1 : 0;
                let razon = $('#razon-permiso-4').val().trim();
                let fechaEntrego = $('#fecha-entrego').val();
                let gocesSueldo = parseInt($('input[name="goce-sueldo"]:checked').val());

                // ===============================
                // VALIDACIONES GENERALES
                // ===============================

                if (!fechaEntrego) {
                    toastr.error('Debe ingresar la fecha');
                    return;
                }

                if (!empleadoId) {
                    toastr.error('Debe seleccionar un empleado');
                    return;
                }

                if (razon.length > 800) {
                    toastr.error('La razón del permiso no puede exceder los 800 caracteres');
                    return;
                }

                // ===============================
                // CREAR OBJETO BASE PRIMERO
                // ===============================

                let datosPermiso = {
                    empleado_id: empleadoId,
                    condicion: condicion,
                    razon: razon || null,
                    fechaEntrego: fechaEntrego,
                    goce_sueldo: gocesSueldo
                };

                // ===============================
                // VALIDAR SEGÚN CONDICIÓN
                // ===============================

                if (condicion === 1) { // fraccionado

                    let fecha_fraccionado = $('#fecha-solicitud-permiso').val();
                    let horaInicio = $('#hora-inicio-4').val();
                    let horaFin = $('#hora-fin-4').val();
                    let duracionMinutos = $('#minutos-permiso-4').val();

                    if (!fecha_fraccionado || !horaInicio || !horaFin) {
                        toastr.error('Complete todos los campos del permiso fraccionado');
                        return;
                    }

                    // ✅ Validar que la duración no esté vacía (indicaría 0 minutos)
                    if (!duracionMinutos || duracionMinutos == 0) {
                        toastr.error('El permiso debe ser de al menos 1 minuto');
                        return;
                    }

                    datosPermiso.fecha_fraccionado = fecha_fraccionado;
                    datosPermiso.hora_inicio = horaInicio;
                    datosPermiso.hora_fin = horaFin;
                    datosPermiso.duracion_minutos = parseInt(duracionMinutos);

                } else {

                    let fechaInicio = $('#fecha-inicio-comp-4').val();
                    let fechaFin = $('#fecha-fin-comp-4').val();
                    let dias = $('#dias-solicitados-4').val();

                    if (!fechaInicio || !fechaFin) {
                        toastr.error('Complete todos los campos del permiso completo');
                        return;
                    }

                    if (new Date(fechaFin) < new Date(fechaInicio)) {
                        toastr.error('La fecha fin no puede ser menor que la fecha inicio');
                        return;
                    }

                    datosPermiso.fecha_inicio = fechaInicio;
                    datosPermiso.fecha_fin = fechaFin;
                    datosPermiso.dias_solicitados = dias;
                }

                // ===============================
                // ENVIAR
                // ===============================

                openLoading();

                axios.post(urlAdmin + '/admin/guardar/permiso/personal', datosPermiso)
                    .then(resp => {
                        if (resp.data.success === 1) {
                            toastr.success('Permiso guardado exitosamente');
                            limpiarFormulario();
                        } else {
                            // Verificar si es error de límite excedido
                            if (resp.data.tipo === 'limite_excedido' && resp.data.data) {
                                mostrarModalLimite(resp.data.data);
                            } else {
                                toastr.error(resp.data.message || 'Error al guardar el permiso');
                            }
                        }
                    })
                    .catch(err => {
                        let mensajeError = 'Error al guardar el permiso';

                        if (err.response && err.response.data) {
                            if (err.response.data.tipo === 'limite_excedido' && err.response.data.data) {
                                mostrarModalLimite(err.response.data.data);
                            } else if (err.response.data.message) {
                                toastr.error(err.response.data.message);
                            } else {
                                toastr.error(mensajeError);
                            }
                        } else {
                            toastr.error(mensajeError);
                        }
                    })
                    .finally(() => {
                        closeLoading();
                    });
            });

            // ===============================
            // FUNCIÓN PARA MOSTRAR MODAL DE LÍMITE
            // ===============================
            function mostrarModalLimite(data) {
                $('#modal-anio').text(data.anio);
                $('#modal-tipo-goce').text(data.tipo_goce);
                $('#modal-limite').html(`<strong>${formatearTiempo(data.limite_minutos)}</strong>`);
                $('#modal-usados').html(`<strong>${formatearTiempo(data.usados_minutos)}</strong>`);
                $('#modal-solicitando').html(`<strong>${formatearTiempo(data.solicitando_minutos)}</strong>`);
                $('#modal-disponibles').html(`<strong>${formatearTiempo(data.disponibles_minutos)}</strong>`);

                $('#modalLimitePermiso').modal('show');
            }

            // ===============================
            // CAMBIAR ENTRE FRACCIONADO Y COMPLETO
            // ===============================
            $(document).on('change', 'input[name="condicion-otros"]', function() {
                if ($(this).val() === 'fraccionado') {
                    $('#seccion-fraccionado-4').slideDown(200);
                    $('#seccion-completo-4').slideUp(200);
                    // Limpiar campos de completo
                    $('#fecha-inicio-comp-4, #fecha-fin-comp-4, #dias-solicitados-4').val('');
                } else {
                    $('#seccion-completo-4').slideDown(200);
                    $('#seccion-fraccionado-4').slideUp(200);
                    // Limpiar campos de fraccionado
                    $('#fecha-solicitud-permiso, #hora-inicio-4, #hora-fin-4, #horas-permiso-4, #minutos-permiso-4').val('');
                }
            });

            // ===============================
            // CALCULAR DURACIÓN (FRACCIONADO) - AHORA EN MINUTOS
            // ===============================
            $(document).on('change', '#hora-inicio-4, #hora-fin-4', function() {

                let horaInicio = $('#hora-inicio-4').val();
                let horaFin = $('#hora-fin-4').val();

                if (horaInicio && horaFin) {

                    let [horaIni, minIni] = horaInicio.split(':');
                    let [horaFinH, minFin] = horaFin.split(':');

                    let inicio = new Date();
                    inicio.setHours(parseInt(horaIni), parseInt(minIni), 0, 0);

                    let fin = new Date();
                    fin.setHours(parseInt(horaFinH), parseInt(minFin), 0, 0);

                    let diferenciaMinutos = (fin - inicio) / (1000 * 60);

                    if (diferenciaMinutos > 0) {

                        // Guardar minutos totales en campo oculto
                        $('#minutos-permiso-4').val(diferenciaMinutos);

                        // Mostrar formato legible
                        let horas = Math.floor(diferenciaMinutos / 60);
                        let minutos = diferenciaMinutos % 60;

                        let textoMostrar = '';
                        if (horas > 0) {
                            textoMostrar += horas + (horas === 1 ? ' hora' : ' horas');
                        }
                        if (minutos > 0) {
                            if (textoMostrar) textoMostrar += ' y ';
                            textoMostrar += minutos + ' minutos';
                        }

                        $('#horas-permiso-4').val(textoMostrar);

                    } else {
                        $('#horas-permiso-4').val('');
                        $('#minutos-permiso-4').val('');
                        toastr.error('La hora de fin debe ser mayor a la hora de inicio');
                    }
                }
            });

            // ===============================
            // CALCULAR DÍAS Y FECHA REINGRESO (COMPLETO)
            // ===============================
            $(document).on('change', '#fecha-inicio-comp-4, #fecha-fin-comp-4', function() {
                let fechaInicio = $('#fecha-inicio-comp-4').val();
                let fechaFin = $('#fecha-fin-comp-4').val();

                if (fechaInicio && fechaFin) {
                    let inicio = new Date(fechaInicio);
                    let fin = new Date(fechaFin);

                    // Calcular días
                    let diferencia = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24)) + 1;
                    $('#dias-solicitados-4').val(diferencia);

                    // Calcular fecha de reingreso (día siguiente al fin)
                    let reingreso = new Date(fin);
                    reingreso.setDate(reingreso.getDate() + 1);
                }
            });
        });

        function limpiarFormulario() {

            // Limpiar empleado
            $('#input-buscar-empleado').val('');
            $('#empleado-id').val('');
            $('#input-unidad').val('');
            $('#input-cargo').val('');
            $('#bloque-btn-info').hide();

            $('#radio-con-goce').prop('checked', true);

            // Limpiar razón
            $('#razon-permiso-4').val('');

            // Resetear a fraccionado
            $('#radio-fraccionado-4').prop('checked', true);
            $('#seccion-fraccionado-4').show();
            $('#seccion-completo-4').hide();

            // Limpiar campos fraccionado
            $('#fecha-solicitud-permiso, #hora-inicio-4, #hora-fin-4, #horas-permiso-4, #minutos-permiso-4').val('');

            // Limpiar campos completo
            $('#fecha-inicio-comp-4, #fecha-fin-comp-4, #dias-solicitados-4').val('');
        }

    </script>


    <script>
        (function () {
            // ===== Config inicial =====
            const SERVER_DEFAULT = {{ $temaPredeterminado }}; // 0 = light, 1 = dark
            const iconEl = document.getElementById('theme-icon');

            // CSRF para axios
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

            // ===== Funciones =====
            function applyTheme(mode) {
                const dark = mode === 'dark';

                // AdminLTE v3
                document.body.classList.toggle('dark-mode', dark);

                // AdminLTE v4
                document.documentElement.setAttribute('data-bs-theme', dark ? 'dark' : 'light');

                // Icono
                if (iconEl) {
                    iconEl.classList.remove('fa-sun', 'fa-moon');
                    iconEl.classList.add(dark ? 'fa-moon' : 'fa-sun');
                }
            }

            function themeToInt(mode) {
                return mode === 'dark' ? 1 : 0;
            }

            function intToTheme(v) {
                return v === 1 ? 'dark' : 'light';
            }

            // ===== Aplicar tema inicial desde servidor =====
            applyTheme(intToTheme(SERVER_DEFAULT));

            // ===== Manejo de clicks y POST a backend =====
            let saving = false;

            document.addEventListener('click', async (e) => {
                const a = e.target.closest('.dropdown-item[data-theme]');
                if (!a) return;
                e.preventDefault();
                if (saving) return;

                const selectedMode = a.dataset.theme; // 'dark' | 'light'
                const newValue = themeToInt(selectedMode);

                // Modo optimista: aplicar de una vez
                const previousMode = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
                applyTheme(selectedMode);

                try {
                    saving = true;
                    await axios.post(urlAdmin + '/admin/actualizar/tema', {tema: newValue});
                    // Si querés, mostrar un toast:
                    if (window.toastr) toastr.success('Tema actualizado');
                } catch (err) {
                    // Revertir si falló
                    applyTheme(previousMode);
                    if (window.toastr) {
                        toastr.error('No se pudo actualizar el tema');
                    } else {
                        alert('No se pudo actualizar el tema');
                    }
                } finally {
                    saving = false;
                }
            });
        })();
    </script>


@endsection
