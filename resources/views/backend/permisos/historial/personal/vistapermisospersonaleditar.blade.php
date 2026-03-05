@extends('adminlte::page')

@section('title', 'Historial Permisos - Personal')

@section('content_header')
    <h1>Historial Permisos - Personal</h1>
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
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesion
                </button>
            </form>
        </div>
    </li>
@endsection

@section('content')

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6"></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Permisos</li>
                    <li class="breadcrumb-item active">Listado de Personal</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            {{-- ======================== PANEL DE FILTROS ======================== --}}
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter mr-1"></i> Filtros
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-end">

                        {{-- Empleado con autocomplete --}}
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="mb-1"><i class="fas fa-user mr-1 text-muted"></i> Empleado</label>
                                <div style="position: relative;">
                                    <input type="text"
                                           class="form-control form-control-sm"
                                           id="filtro-buscar-empleado"
                                           placeholder="Escriba un nombre..."
                                           autocomplete="off">
                                    <input type="hidden" id="filtro-empleado-id">

                                    <span id="filtro-limpiar-empleado"
                                          style="display:none; position:absolute; right:8px; top:50%;
                                                 transform:translateY(-50%); cursor:pointer; color:#999;">
                                        <i class="fas fa-times-circle"></i>
                                    </span>

                                    <div id="filtro-lista-empleados"
                                         class="list-group shadow"
                                         style="display:none; position:absolute; z-index:9999;
                                                max-height:200px; overflow-y:auto;
                                                width:100%; top:100%; left:0;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Fecha Desde --}}
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="mb-1"><i class="fas fa-calendar-alt mr-1 text-muted"></i> Fecha Desde</label>
                                <input type="date" class="form-control form-control-sm" id="filtro-fecha-desde">
                            </div>
                        </div>

                        {{-- Fecha Hasta --}}
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="mb-1"><i class="fas fa-calendar-check mr-1 text-muted"></i> Fecha Hasta</label>
                                <input type="date" class="form-control form-control-sm" id="filtro-fecha-hasta">
                            </div>
                        </div>

                        {{-- Tipo (condicion) --}}
                        <div class="col-md-1">
                            <div class="form-group mb-0">
                                <label class="mb-1"><i class="fas fa-tag mr-1 text-muted"></i> Tipo</label>
                                <select class="form-control form-control-sm" id="filtro-condicion">
                                    <option value="">Todos</option>
                                    <option value="1">Fraccionado</option>
                                    <option value="0">Dias</option>
                                </select>
                            </div>
                        </div>

                        {{-- Goce de sueldo --}}
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="mb-1"><i class="fas fa-dollar-sign mr-1 text-muted"></i> Goce de Sueldo</label>
                                <select class="form-control form-control-sm" id="filtro-goce">
                                    <option value="">Todos</option>
                                    <option value="1">Con goce</option>
                                    <option value="0">Sin goce</option>
                                </select>
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary btn-sm btn-block" onclick="aplicarFiltros()">
                                <i class="fas fa-search mr-1"></i> Filtrar
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-block mt-1" onclick="limpiarFiltros()">
                                <i class="fas fa-times mr-1"></i> Limpiar
                            </button>
                        </div>

                    </div>

                    {{-- Badges filtros activos --}}
                    <div class="row mt-2" id="filtros-activos-row" style="display:none !important;">
                        <div class="col-12">
                            <small class="text-muted">Filtros activos: </small>
                            <span id="filtros-activos-badges"></span>
                        </div>
                    </div>

                </div>
            </div>
            {{-- ======================== FIN PANEL DE FILTROS ======================== --}}

            <div class="card card-blue">
                <div class="card-header">
                    <h3 class="card-title">Listado</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="tablaDatatable"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>


    <!-- ======================== MODAL EDITAR ======================== -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-gray-dark text-dark">
                    <h4 class="modal-title">
                        <i class="fas fa-edit"></i> Editar
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="formulario-editar">
                        <input type="hidden" id="edit-permiso-id">

                        <div class="row">

                            <!-- COLUMNA IZQUIERDA: DATOS DEL PERMISO -->
                            <div class="col-md-6">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-edit"></i> Datos del Permiso
                                        </h3>
                                    </div>
                                    <div class="card-body">

                                        <div class="form-group col-md-5 px-0">
                                            <label>Fecha: <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="edit-fecha-entrego">
                                        </div>
                                        <br>

                                        <div class="form-group">
                                            <label>Goce de Sueldo: <span class="text-danger">*</span></label>
                                            <div class="d-flex align-items-center" style="gap: 20px;">
                                                <div class="custom-control custom-radio">
                                                    <input class="custom-control-input custom-control-input-success"
                                                           type="radio" id="edit-radio-con-goce"
                                                           name="edit-goce-sueldo" value="1">
                                                    <label for="edit-radio-con-goce" class="custom-control-label">
                                                        <i class="fas fa-check-circle text-success"></i>
                                                        <strong>Con goce de sueldo</strong>
                                                    </label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input class="custom-control-input custom-control-input-danger"
                                                           type="radio" id="edit-radio-sin-goce"
                                                           name="edit-goce-sueldo" value="0">
                                                    <label for="edit-radio-sin-goce" class="custom-control-label">
                                                        <i class="fas fa-times-circle text-danger"></i>
                                                        <strong>Sin goce de sueldo</strong>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="form-group">
                                            <label>Condicion del Permiso: <span class="text-danger">*</span></label>
                                            <div class="d-flex align-items-center" style="gap: 20px;">
                                                <div class="custom-control custom-radio">
                                                    <input class="custom-control-input" type="radio"
                                                           id="edit-radio-fraccionado" name="edit-condicion" value="1">
                                                    <label for="edit-radio-fraccionado" class="custom-control-label">
                                                        <strong>Fraccionado</strong> (Por horas/minutos)
                                                    </label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input class="custom-control-input" type="radio"
                                                           id="edit-radio-completo" name="edit-condicion" value="0">
                                                    <label for="edit-radio-completo" class="custom-control-label">
                                                        <strong>Completo</strong> (Dia(s) completo(s))
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        <!-- SECCION FRACCIONADO -->
                                        <div id="edit-seccion-fraccionado" style="display:none;">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Fecha del Permiso: <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" id="edit-fecha-fraccionado">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Hora de Inicio: <span class="text-danger">*</span></label>
                                                        <input type="time" class="form-control" id="edit-hora-inicio">
                                                        <small class="form-text text-muted">Desde que hora necesita el permiso</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Hora de Fin: <span class="text-danger">*</span></label>
                                                        <input type="time" class="form-control" id="edit-hora-fin">
                                                        <small class="form-text text-muted">Hasta que hora necesita el permiso</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Duracion del Permiso:</label>
                                                        <input type="text" class="form-control" id="edit-horas-permiso" readonly>
                                                        <input type="hidden" id="edit-minutos-permiso">
                                                        <small class="form-text text-muted">Se calcula automaticamente</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SECCION COMPLETO -->
                                        <div id="edit-seccion-completo" style="display:none;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Fecha de Inicio: <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" id="edit-fecha-inicio-comp">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Fecha de Fin: <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" id="edit-fecha-fin-comp">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Dias Solicitados:</label>
                                                        <input type="number" class="form-control" id="edit-dias-solicitados" readonly>
                                                        <small class="form-text text-muted">Se calcula automaticamente</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="form-group">
                                            <label>Razon del Permiso:</label>
                                            <textarea class="form-control" rows="3" maxlength="800"
                                                      id="edit-razon"
                                                      placeholder="Describa brevemente el motivo del permiso"></textarea>
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

                                            <div class="input-group" id="edit-empleado-actual">
                                                <input type="text" class="form-control" id="edit-empleado-nombre" readonly
                                                       placeholder="Cargando empleado...">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-warning" id="btn-cambiar-empleado"
                                                            title="Cambiar empleado">
                                                        <i class="fas fa-exchange-alt"></i> Cambiar
                                                    </button>
                                                </div>
                                            </div>

                                            <div id="edit-bloque-buscar" style="display:none; position:relative;">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="edit-buscar-empleado"
                                                           placeholder="Escriba el nombre del empleado...">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-secondary" id="btn-cancelar-busqueda">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div id="edit-lista-empleados"
                                                     class="list-group mt-1"
                                                     style="display:none; position:absolute; z-index:9999; max-height:220px; overflow-y:auto; width:100%;">
                                                </div>
                                            </div>

                                            <input type="hidden" id="edit-empleado-id">
                                        </div>

                                        <div class="form-group">
                                            <label>Unidad</label>
                                            <input type="text" class="form-control" id="edit-input-unidad" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label>Cargo</label>
                                            <input type="text" class="form-control" id="edit-input-cargo" readonly>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success btn-sm" onclick="editar()">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                </div>

            </div>
        </div>
    </div>


    <!-- Modal Limite Excedido -->
    <div class="modal fade" id="modalLimitePermiso" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h4 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Limite de Permisos Excedido
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
                            <h5 class="text-warning mb-3"><strong>No se puede procesar la solicitud</strong></h5>
                            <div class="alert alert-warning mb-3">
                                <h6 class="mb-2"><strong>Resumen del Año <span id="modal-anio"></span>:</strong></h6>
                                <p class="mb-1"><i class="fas fa-info-circle"></i> Tipo: <strong id="modal-tipo-goce"></strong></p>
                            </div>
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="info-box mb-0 bg-light">
                                                <div class="info-box-content p-2">
                                                    <span class="info-box-text text-muted small">Limite Total</span>
                                                    <span class="info-box-number text-primary" id="modal-limite"><strong>-</strong></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box mb-0 bg-light">
                                                <div class="info-box-content p-2">
                                                    <span class="info-box-text text-muted small">Ya Usados</span>
                                                    <span class="info-box-number text-info" id="modal-usados"><strong>-</strong></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box mb-0 bg-warning">
                                                <div class="info-box-content p-2">
                                                    <span class="info-box-text text-dark small">Solicitando</span>
                                                    <span class="info-box-number text-dark" id="modal-solicitando"><strong>-</strong></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box mb-0 bg-light">
                                                <div class="info-box-content p-2">
                                                    <span class="info-box-text text-muted small">Disponibles</span>
                                                    <span class="info-box-number text-success" id="modal-disponibles"><strong>-</strong></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-danger mt-3 mb-0">
                                <i class="fas fa-times-circle"></i>
                                <strong>No se puede aprobar:</strong> La cantidad solicitada excede el limite disponible.
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
    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}" type="text/javascript"></script>

    <script>

        function formatearFecha(fecha) {
            if (!fecha) return '';
            const [y, m, d] = fecha.split('-');
            return `${d}-${m}-${y}`;
        }

        $(function () {
            const ruta = "{{ url('/admin/historial/personal/tabla') }}";

            function initDataTable() {
                if ($.fn.DataTable.isDataTable('#tabla')) {
                    $('#tabla').DataTable().destroy();
                }
                $('#tabla').DataTable({
                    paging: true,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    responsive: true,
                    pagingType: "full_numbers",
                    lengthMenu: [[100, 150, -1], [100, 150, "Todo"]],
                    language: {
                        sProcessing:   "Procesando...",
                        sLengthMenu:   "Mostrar _MENU_ registros",
                        sZeroRecords:  "No se encontraron resultados",
                        sEmptyTable:   "Ningun dato disponible en esta tabla",
                        sInfo:         "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        sInfoEmpty:    "Mostrando 0 a 0 de 0 registros",
                        sInfoFiltered: "(filtrado de _MAX_ registros)",
                        sSearch:       "Buscar:",
                        oPaginate: { sFirst: "Primero", sLast: "Ultimo", sNext: "Siguiente", sPrevious: "Anterior" },
                        oAria: { sSortAscending: ": Orden ascendente", sSortDescending: ": Orden descendente" }
                    },
                    dom:
                        "<'row align-items-center'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-md-right'f>>" +
                        "tr" +
                        "<'row align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
                });
                $('#tabla_length select').addClass('form-control form-control-sm');
                $('#tabla_filter input').addClass('form-control form-control-sm').css('display', 'inline-block');
            }

            window.cargarTabla = function (params = {}) {
                openLoading();
                $.get(ruta, params, function (html) {
                    $('#tablaDatatable').html(html);
                    initDataTable();
                    closeLoading();
                });
            };

            window.recargar = function () {
                aplicarFiltros();
            };

            window.aplicarFiltros = function () {
                const params = {
                    empleado_id: $('#filtro-empleado-id').val() || '',
                    fecha_desde: $('#filtro-fecha-desde').val() || '',
                    fecha_hasta: $('#filtro-fecha-hasta').val() || '',
                    condicion:   $('#filtro-condicion').val()   || '',
                    goce:        $('#filtro-goce').val()        || '',
                };
                mostrarBadgesFiltros(params);
                cargarTabla(params);
            };

            window.limpiarFiltros = function () {
                $('#filtro-buscar-empleado').val('');
                $('#filtro-empleado-id').val('');
                $('#filtro-limpiar-empleado').hide();
                $('#filtro-fecha-desde').val('');
                $('#filtro-fecha-hasta').val('');
                $('#filtro-condicion').val('');
                $('#filtro-goce').val('');
                $('#filtros-activos-row').hide();
                $('#filtros-activos-badges').html('');
                cargarTabla();
            };

            function mostrarBadgesFiltros(params) {
                let badges = '', hay = false;

                if (params.empleado_id) {
                    badges += `<span class="badge badge-primary mr-1"><i class="fas fa-user mr-1"></i>${$('#filtro-buscar-empleado').val()}</span>`;
                    hay = true;
                }
                if (params.fecha_desde) {
                    badges += `<span class="badge badge-info mr-1"><i class="fas fa-calendar mr-1"></i>Desde: ${formatearFecha(params.fecha_desde)}</span>`;
                    hay = true;
                }
                if (params.fecha_hasta) {
                    badges += `<span class="badge badge-info mr-1"><i class="fas fa-calendar-check mr-1"></i>Hasta: ${formatearFecha(params.fecha_hasta)}</span>`;
                    hay = true;
                }
                if (params.condicion !== '') {
                    const label = params.condicion == '1' ? 'Fraccionado' : 'Dias completo(s)';
                    badges += `<span class="badge badge-warning mr-1"><i class="fas fa-tag mr-1"></i>${label}</span>`;
                    hay = true;
                }
                if (params.goce !== '') {
                    const label = params.goce == '1' ? 'Con goce' : 'Sin goce';
                    const color = params.goce == '1' ? 'badge-success' : 'badge-danger';
                    badges += `<span class="badge ${color} mr-1"><i class="fas fa-dollar-sign mr-1"></i>${label}</span>`;
                    hay = true;
                }

                if (hay) {
                    $('#filtros-activos-badges').html(badges);
                    $('#filtros-activos-row').show();
                } else {
                    $('#filtros-activos-row').hide();
                    $('#filtros-activos-badges').html('');
                }
            }

            // Autocomplete filtro empleado
            let filtroTimeout = null;

            $(document).on('keyup', '#filtro-buscar-empleado', function () {
                const texto = $(this).val();
                if (texto.length === 0) {
                    $('#filtro-empleado-id').val('');
                    $('#filtro-limpiar-empleado').hide();
                    $('#filtro-lista-empleados').hide().html('');
                    return;
                }
                if (texto.length < 2) { $('#filtro-lista-empleados').hide().html(''); return; }

                clearTimeout(filtroTimeout);
                filtroTimeout = setTimeout(function () {
                    axios.get(urlAdmin + '/admin/empleados/buscar', { params: { q: texto } })
                        .then(resp => {
                            let html = resp.data.length === 0
                                ? `<div class="list-group-item text-muted small"><i class="fas fa-info-circle mr-1"></i>Sin resultados</div>`
                                : resp.data.map(e => `
                                    <button type="button"
                                        class="list-group-item list-group-item-action py-1 filtro-empleado-item"
                                        data-id="${e.id}" data-nombre="${e.nombre}">
                                        <strong>${e.nombre}</strong>
                                        <small class="text-muted d-block">${e.cargo ?? ''} — ${e.unidad ?? ''}</small>
                                    </button>`).join('');
                            $('#filtro-lista-empleados').html(html).show();
                        })
                        .catch(() => toastr.error('Error al buscar empleados'));
                }, 300);
            });

            $(document).on('click', '.filtro-empleado-item', function () {
                $('#filtro-empleado-id').val($(this).data('id'));
                $('#filtro-buscar-empleado').val($(this).data('nombre'));
                $('#filtro-limpiar-empleado').show();
                $('#filtro-lista-empleados').hide();
            });

            $(document).on('click', '#filtro-limpiar-empleado', function () {
                $('#filtro-empleado-id').val('');
                $('#filtro-buscar-empleado').val('').focus();
                $(this).hide();
                $('#filtro-lista-empleados').hide().html('');
            });

            $(document).on('keypress', '#filtro-buscar-empleado', function (e) {
                if (e.which === 13) aplicarFiltros();
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('#filtro-buscar-empleado, #filtro-lista-empleados').length) {
                    $('#filtro-lista-empleados').hide();
                }
            });

            cargarTabla();
        });


        // ===================================================
        // ELIMINAR
        // ===================================================
        function informacionBorrar(id) {
            Swal.fire({
                title: 'Borrar Registro',
                text: '¿Esta seguro que desea eliminar este permiso?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                allowOutsideClick: false,
                confirmButtonText: 'Si, borrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) borrarRegistro(id);
            });
        }

        function borrarRegistro(id) {
            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            axios.post(urlAdmin + '/admin/historial/personal/borrar', formData)
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) { toastr.success('Borrado correctamente'); recargar(); }
                    else toastr.error('Error al borrar');
                })
                .catch(() => { toastr.error('Error al borrar'); closeLoading(); });
        }

        // ===================================================
        // ABRIR MODAL
        // ===================================================
        function informacion(id) {
            openLoading();
            document.getElementById("formulario-editar").reset();
            $('#edit-seccion-fraccionado, #edit-seccion-completo').hide();
            $('#edit-lista-empleados').hide();

            axios.post(urlAdmin + '/admin/historial/personal/informacion', { id: id })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        const info = response.data.info;

                        $('#edit-permiso-id').val(info.id);
                        $('#edit-fecha-entrego').val(info.fecha);
                        $('#edit-razon').val(info.razon ?? '');
                        $('input[name="edit-goce-sueldo"][value="' + info.goce + '"]').prop('checked', true);

                        $('#edit-empleado-id').val(info.id_empleado);
                        $('#edit-empleado-nombre').val(info.nombre_empleado ?? '');
                        $('#edit-input-unidad').val(info.unidad ?? '');
                        $('#edit-input-cargo').val(info.cargo ?? '');
                        $('#edit-empleado-actual').show();
                        $('#edit-bloque-buscar').hide();

                        $('input[name="edit-condicion"][value="' + info.condicion + '"]').prop('checked', true);
                        toggleEditCondicion(info.condicion);

                        if (info.condicion == 1) {
                            $('#edit-fecha-fraccionado').val(info.fecha_fraccionado ?? '');
                            $('#edit-hora-inicio').val(info.hora_inicio ? info.hora_inicio.substring(0, 5) : '');
                            $('#edit-hora-fin').val(info.hora_fin ? info.hora_fin.substring(0, 5) : '');
                            if (info.hora_inicio && info.hora_fin) $('#edit-hora-fin').trigger('change');
                        } else {
                            $('#edit-fecha-inicio-comp').val(info.fecha_inicio ?? '');
                            $('#edit-fecha-fin-comp').val(info.fecha_fin ?? '');
                            if (info.fecha_inicio && info.fecha_fin) $('#edit-fecha-fin-comp').trigger('change');
                        }

                        $('#modalEditar').modal('show');
                    } else {
                        toastr.error('Informacion no encontrada');
                    }
                })
                .catch(() => { closeLoading(); toastr.error('Error al obtener informacion'); });
        }

        $(document).on('change', 'input[name="edit-condicion"]', function () { toggleEditCondicion($(this).val()); });

        function toggleEditCondicion(val) {
            if (val == '1') {
                $('#edit-seccion-fraccionado').slideDown(200);
                $('#edit-seccion-completo').slideUp(200);
                $('#edit-fecha-inicio-comp, #edit-fecha-fin-comp, #edit-dias-solicitados').val('');
            } else {
                $('#edit-seccion-completo').slideDown(200);
                $('#edit-seccion-fraccionado').slideUp(200);
                $('#edit-fecha-fraccionado, #edit-hora-inicio, #edit-hora-fin, #edit-horas-permiso, #edit-minutos-permiso').val('');
            }
        }

        $(document).on('change', '#edit-hora-inicio, #edit-hora-fin', function () {
            let hi = $('#edit-hora-inicio').val(), hf = $('#edit-hora-fin').val();
            if (hi && hf) {
                let [hI, mI] = hi.split(':'), [hF, mF] = hf.split(':');
                let ini = new Date(); ini.setHours(+hI, +mI, 0, 0);
                let fin = new Date(); fin.setHours(+hF, +mF, 0, 0);
                let diff = (fin - ini) / (1000 * 60);
                if (diff > 0) {
                    $('#edit-minutos-permiso').val(diff);
                    let h = Math.floor(diff / 60), m = diff % 60, t = '';
                    if (h > 0) t += h + (h === 1 ? ' hora' : ' horas');
                    if (m > 0) { if (t) t += ' y '; t += m + ' minutos'; }
                    $('#edit-horas-permiso').val(t);
                } else {
                    $('#edit-horas-permiso, #edit-minutos-permiso').val('');
                    toastr.error('La hora de fin debe ser mayor a la hora de inicio');
                }
            }
        });

        $(document).on('change', '#edit-fecha-inicio-comp, #edit-fecha-fin-comp', function () {
            let fi = $('#edit-fecha-inicio-comp').val(), ff = $('#edit-fecha-fin-comp').val();
            if (fi && ff) {
                let d = Math.ceil((new Date(ff) - new Date(fi)) / (1000 * 60 * 60 * 24)) + 1;
                $('#edit-dias-solicitados').val(d > 0 ? d : '');
            }
        });

        // ===================================================
        // GUARDAR EDICIÓN
        // ===================================================
        function editar() {
            const id           = $('#edit-permiso-id').val();
            const empleadoId   = $('#edit-empleado-id').val();
            const condicion    = $('input[name="edit-condicion"]:checked').val();
            const fechaEntrego = $('#edit-fecha-entrego').val();
            const razon        = $('#edit-razon').val().trim();
            const goceSueldo   = $('input[name="edit-goce-sueldo"]:checked').val();

            if (!fechaEntrego)            { toastr.error('La fecha es requerida');      return; }
            if (!empleadoId)              { toastr.error('El empleado es requerido');   return; }
            if (condicion === undefined)  { toastr.error('La condicion es requerida');  return; }
            if (goceSueldo === undefined) { toastr.error('Seleccione goce de sueldo');  return; }

            const formData = new FormData();
            formData.append('id',           id);
            formData.append('empleado_id',  empleadoId);
            formData.append('condicion',    condicion);
            formData.append('fechaEntrego', fechaEntrego);
            formData.append('goce_sueldo',  goceSueldo);
            formData.append('razon',        razon);

            if (condicion == '1') {
                const fechaFrac = $('#edit-fecha-fraccionado').val();
                const horaIni   = $('#edit-hora-inicio').val();
                const horaFin   = $('#edit-hora-fin').val();
                const minutos   = $('#edit-minutos-permiso').val();
                if (!fechaFrac || !horaIni || !horaFin) { toastr.error('Complete todos los campos del permiso fraccionado'); return; }
                if (!minutos || minutos == 0) { toastr.error('El permiso debe ser de al menos 1 minuto'); return; }
                formData.append('fecha_fraccionado', fechaFrac);
                formData.append('hora_inicio',       horaIni);
                formData.append('hora_fin',          horaFin);
                formData.append('duracion_minutos',  parseInt(minutos));
            } else {
                const fechaIni = $('#edit-fecha-inicio-comp').val();
                const fechaFin = $('#edit-fecha-fin-comp').val();
                const dias     = $('#edit-dias-solicitados').val();
                if (!fechaIni || !fechaFin) { toastr.error('Complete todos los campos del permiso completo'); return; }
                if (new Date(fechaFin) < new Date(fechaIni)) { toastr.error('La fecha fin no puede ser menor que la fecha inicio'); return; }
                formData.append('fecha_inicio',     fechaIni);
                formData.append('fecha_fin',        fechaFin);
                formData.append('dias_solicitados', dias);
            }

            openLoading();

            axios.post(urlAdmin + '/admin/historial/personal/actualizar', formData)
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
                    } else {
                        if (response.data.tipo === 'limite_excedido' && response.data.data) mostrarModalLimite(response.data.data);
                        else toastr.error(response.data.message ?? 'Error al actualizar');
                    }
                })
                .catch((err) => {
                    closeLoading();
                    if (err.response?.data?.tipo === 'limite_excedido' && err.response.data.data) mostrarModalLimite(err.response.data.data);
                    else toastr.error(err.response?.data?.message || 'Error al actualizar');
                });
        }

        function mostrarModalLimite(data) {
            $('#modal-anio').text(data.anio);
            $('#modal-tipo-goce').text(data.tipo_goce);
            $('#modal-limite').html('<strong>' + formatearTiempo(data.limite_minutos) + '</strong>');
            $('#modal-usados').html('<strong>' + formatearTiempo(data.usados_minutos) + '</strong>');
            $('#modal-solicitando').html('<strong>' + formatearTiempo(data.solicitando_minutos) + '</strong>');
            $('#modal-disponibles').html('<strong>' + formatearTiempo(data.disponibles_minutos) + '</strong>');
            $('#modalLimitePermiso').modal('show');
        }

        function formatearTiempo(minutos) {
            if (minutos < 60) return minutos + ' min';
            const dias = Math.floor(minutos / 480), rest = minutos % 480;
            const h = Math.floor(rest / 60), m = rest % 60;
            let r = '';
            if (dias > 0) r += dias + (dias === 1 ? ' dia' : ' dias');
            if (h > 0)    { if (r) r += ', '; r += h + (h === 1 ? ' hora' : ' horas'); }
            if (m > 0)    { if (r) r += ', '; r += m + ' min'; }
            return r || '0 min';
        }

        // ===================================================
        // MODAL EDITAR — BUSCAR EMPLEADO
        // ===================================================
        $(document).on('click', '#btn-cambiar-empleado', function () {
            $('#edit-empleado-actual').hide();
            $('#edit-bloque-buscar').show();
            $('#edit-buscar-empleado').val('').focus();
            $('#edit-lista-empleados').hide().html('');
        });

        $(document).on('click', '#btn-cancelar-busqueda', function () {
            $('#edit-bloque-buscar').hide();
            $('#edit-empleado-actual').show();
            $('#edit-lista-empleados').hide().html('');
        });

        $(document).on('keyup', '#edit-buscar-empleado', function () {
            let texto = $(this).val();
            if (texto.length < 2) { $('#edit-lista-empleados').hide().html(''); return; }
            if ($(this).data('buscando')) return;
            const $input = $(this);
            $input.data('buscando', true);
            axios.get(urlAdmin + '/admin/empleados/buscar', { params: { q: texto } })
                .then(resp => {
                    let html = '';
                    resp.data.forEach(e => {
                        html += '<button type="button" class="list-group-item list-group-item-action edit-empleado-item"' +
                            ' data-id="' + e.id + '" data-unidad="' + e.unidad + '" data-cargo="' + e.cargo + '" data-nombre="' + e.nombre + '">' +
                            '<strong>' + e.nombre + '</strong>' +
                            '<small class="text-muted d-block">' + (e.cargo ?? '') + ' - ' + (e.unidad ?? '') + '</small></button>';
                    });
                    $('#edit-lista-empleados').html(html).show();
                })
                .catch(() => toastr.error('Error al buscar empleados'))
                .finally(() => $input.data('buscando', false));
        });

        $(document).on('click', '.edit-empleado-item', function () {
            $('#edit-empleado-id').val($(this).data('id'));
            $('#edit-empleado-nombre').val($(this).data('nombre'));
            $('#edit-input-unidad').val($(this).data('unidad'));
            $('#edit-input-cargo').val($(this).data('cargo'));
            $('#edit-lista-empleados').hide();
            $('#edit-bloque-buscar').hide();
            $('#edit-empleado-actual').show();
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('#edit-buscar-empleado, #edit-lista-empleados').length) {
                $('#edit-lista-empleados').hide();
            }
        });

    </script>

@endsection
