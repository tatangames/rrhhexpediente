@extends('adminlte::page')

@section('title', 'Historial Permisos - Compensatorio')

@section('content_header')
    <h1>Historial Permisos - Compensatorio</h1>
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
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
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
                    <li class="breadcrumb-item active">Listado de Compensatorio</li>
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
                        <div class="col-md-4">
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

                        {{-- Condición --}}
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="mb-1"><i class="fas fa-tag mr-1 text-muted"></i> Tipo</label>
                                <select class="form-control form-control-sm" id="filtro-condicion">
                                    <option value="">Todos</option>
                                    <option value="1">Fraccionado (horas/minutos)</option>
                                    <option value="0">Días completo(s)</option>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Permiso</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-editar">
                        <input type="hidden" id="id-editar">

                        <div class="row">
                            <!-- Fecha Entregó -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fecha: <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit-fechaEntrego">
                                </div>
                            </div>

                            <!-- Condición radios -->
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Condición: <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center" style="gap:20px; margin-top:5px;">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="edit-radio-fraccionado"
                                                   name="edit-condicion" value="1">
                                            <label for="edit-radio-fraccionado" class="custom-control-label">
                                                <strong>Fraccionado</strong> (Por horas/minutos)
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="edit-radio-completo"
                                                   name="edit-condicion" value="0">
                                            <label for="edit-radio-completo" class="custom-control-label">
                                                <strong>Completo</strong> (Día(s) completo(s))
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Sección FRACCIONADO -->
                        <div id="edit-seccion-fraccionado" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha del Permiso: <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="edit-fecha-solicitud">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Hora Inicio: <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="edit-hora-inicio">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Hora Fin: <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="edit-hora-fin">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Duración:</label>
                                        <input type="text" class="form-control" id="edit-duracion" readonly
                                               placeholder="Se calcula automático">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección COMPLETO -->
                        <div id="edit-seccion-completo" style="display:none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha Inicio: <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="edit-fecha-inicio">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha Fin: <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="edit-fecha-fin">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Días Solicitados:</label>
                                        <input type="number" class="form-control" id="edit-dias" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Empleado -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Empleado: <span class="text-danger">*</span></label>
                                    <input type="hidden" id="edit-empleado-id">

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
                                                <button type="button" class="btn btn-secondary" id="btn-cancelar-busqueda"
                                                        title="Cancelar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="edit-lista-empleados"
                                             class="list-group mt-1"
                                             style="display:none; position:absolute; z-index:9999; max-height:220px; overflow-y:auto; width:100%;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Unidad</label>
                                    <input type="text" class="form-control" id="edit-unidad" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cargo</label>
                                    <input type="text" class="form-control" id="edit-cargo" readonly>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Razón -->
                        <div class="form-group">
                            <label>Razón del Permiso:</label>
                            <textarea class="form-control" rows="3" maxlength="800" id="edit-razon"
                                      placeholder="Describa brevemente el motivo del permiso"></textarea>
                        </div>

                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success btn-sm" onclick="editar()">Actualizar</button>
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

        // ===================================================
        // HELPERS
        // ===================================================
        function formatearFecha(fecha) {
            if (!fecha) return '';
            const [y, m, d] = fecha.split('-');
            return `${d}-${m}-${y}`;
        }

        $(function () {
            const ruta = "{{ url('/admin/historial/compensatorio/tabla') }}";

            // ===================================================
            // DATATABLE
            // ===================================================
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
                        sEmptyTable:   "Ningún dato disponible en esta tabla",
                        sInfo:         "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        sInfoEmpty:    "Mostrando 0 a 0 de 0 registros",
                        sInfoFiltered: "(filtrado de _MAX_ registros)",
                        sSearch:       "Buscar:",
                        oPaginate: { sFirst: "Primero", sLast: "Último", sNext: "Siguiente", sPrevious: "Anterior" },
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

            // ===================================================
            // CARGAR TABLA
            // ===================================================
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

            // ===================================================
            // APLICAR FILTROS
            // ===================================================
            window.aplicarFiltros = function () {
                const params = {
                    empleado_id: $('#filtro-empleado-id').val() || '',
                    fecha_desde: $('#filtro-fecha-desde').val() || '',
                    fecha_hasta: $('#filtro-fecha-hasta').val() || '',
                    condicion:   $('#filtro-condicion').val()   || '',
                };
                mostrarBadgesFiltros(params);
                cargarTabla(params);
            };

            // ===================================================
            // LIMPIAR FILTROS
            // ===================================================
            window.limpiarFiltros = function () {
                $('#filtro-buscar-empleado').val('');
                $('#filtro-empleado-id').val('');
                $('#filtro-limpiar-empleado').hide();
                $('#filtro-fecha-desde').val('');
                $('#filtro-fecha-hasta').val('');
                $('#filtro-condicion').val('');
                $('#filtros-activos-row').hide();
                $('#filtros-activos-badges').html('');
                cargarTabla();
            };

            // ===================================================
            // BADGES FILTROS ACTIVOS
            // ===================================================
            function mostrarBadgesFiltros(params) {
                let badges    = '';
                let hayFiltro = false;

                if (params.empleado_id) {
                    const nombre = $('#filtro-buscar-empleado').val();
                    badges += `<span class="badge badge-primary mr-1"><i class="fas fa-user mr-1"></i>${nombre}</span>`;
                    hayFiltro = true;
                }
                if (params.fecha_desde) {
                    badges += `<span class="badge badge-info mr-1"><i class="fas fa-calendar mr-1"></i>Desde: ${formatearFecha(params.fecha_desde)}</span>`;
                    hayFiltro = true;
                }
                if (params.fecha_hasta) {
                    badges += `<span class="badge badge-info mr-1"><i class="fas fa-calendar-check mr-1"></i>Hasta: ${formatearFecha(params.fecha_hasta)}</span>`;
                    hayFiltro = true;
                }
                if (params.condicion !== '') {
                    const label = params.condicion == '1' ? 'Fraccionado' : 'Días completo(s)';
                    badges += `<span class="badge badge-warning mr-1"><i class="fas fa-tag mr-1"></i>${label}</span>`;
                    hayFiltro = true;
                }

                if (hayFiltro) {
                    $('#filtros-activos-badges').html(badges);
                    $('#filtros-activos-row').show();
                } else {
                    $('#filtros-activos-row').hide();
                    $('#filtros-activos-badges').html('');
                }
            }

            // ===================================================
            // AUTOCOMPLETE — FILTRO EMPLEADO
            // ===================================================
            let filtroTimeout = null;

            $(document).on('keyup', '#filtro-buscar-empleado', function () {
                const texto = $(this).val();

                if (texto.length === 0) {
                    $('#filtro-empleado-id').val('');
                    $('#filtro-limpiar-empleado').hide();
                    $('#filtro-lista-empleados').hide().html('');
                    return;
                }
                if (texto.length < 2) {
                    $('#filtro-lista-empleados').hide().html('');
                    return;
                }

                clearTimeout(filtroTimeout);
                filtroTimeout = setTimeout(function () {
                    axios.get(urlAdmin + '/admin/empleados/buscar', { params: { q: texto } })
                        .then(resp => {
                            let html = '';
                            if (resp.data.length === 0) {
                                html = `<div class="list-group-item text-muted small">
                                            <i class="fas fa-info-circle mr-1"></i>Sin resultados
                                        </div>`;
                            } else {
                                resp.data.forEach(e => {
                                    html += `
                                        <button type="button"
                                            class="list-group-item list-group-item-action py-1 filtro-empleado-item"
                                            data-id="${e.id}"
                                            data-nombre="${e.nombre}">
                                            <strong>${e.nombre}</strong>
                                            <small class="text-muted d-block">${e.cargo ?? ''} — ${e.unidad ?? ''}</small>
                                        </button>`;
                                });
                            }
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

            // Carga inicial sin filtros
            cargarTabla();

        }); // end document ready


        // ===================================================
        // ELIMINAR
        // ===================================================
        function informacionBorrar(id) {
            Swal.fire({
                title: 'Borrar Registro',
                text: '¿Está seguro que desea eliminar este permiso?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                allowOutsideClick: false,
                confirmButtonText: 'Sí, borrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) borrarRegistro(id);
            });
        }

        function borrarRegistro(id) {
            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(urlAdmin + '/admin/historial/compensatorio/borrar', formData)
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Borrado correctamente');
                        recargar();
                    } else {
                        toastr.error('Error al borrar');
                    }
                })
                .catch(() => {
                    toastr.error('Error al borrar');
                    closeLoading();
                });
        }


        // ===================================================
        // MODAL EDITAR — CAMBIAR CONDICIÓN
        // ===================================================
        $(document).on('change', 'input[name="edit-condicion"]', function () {
            toggleEditCondicion($(this).val());
        });

        function toggleEditCondicion(val) {
            if (val == '1') {
                $('#edit-seccion-fraccionado').slideDown(200);
                $('#edit-seccion-completo').slideUp(200);
                $('#edit-fecha-inicio, #edit-fecha-fin, #edit-dias').val('');
            } else {
                $('#edit-seccion-completo').slideDown(200);
                $('#edit-seccion-fraccionado').slideUp(200);
                $('#edit-fecha-solicitud, #edit-hora-inicio, #edit-hora-fin, #edit-duracion').val('');
            }
        }

        // ===================================================
        // CALCULAR DURACIÓN AUTOMÁTICA (fraccionado)
        // ===================================================
        $(document).on('change', '#edit-hora-inicio, #edit-hora-fin', function () {
            const horaInicio = $('#edit-hora-inicio').val();
            const horaFin    = $('#edit-hora-fin').val();

            if (horaInicio && horaFin) {
                let [hI, mI] = horaInicio.split(':');
                let [hF, mF] = horaFin.split(':');

                let ini = new Date(); ini.setHours(parseInt(hI), parseInt(mI), 0, 0);
                let fin = new Date(); fin.setHours(parseInt(hF), parseInt(mF), 0, 0);
                let diffMs = fin - ini;

                if (diffMs > 0) {
                    let totalMin = diffMs / (1000 * 60);
                    let horas    = Math.floor(totalMin / 60);
                    let minutos  = totalMin % 60;
                    let texto    = '';

                    if (horas > 0)   texto += horas + (horas === 1 ? ' hora' : ' horas');
                    if (minutos > 0) texto += (texto ? ' ' : '') + minutos + (minutos === 1 ? ' minuto' : ' minutos');

                    $('#edit-duracion').val(texto);
                } else {
                    $('#edit-duracion').val('');
                    toastr.error('La hora de fin debe ser mayor a la hora de inicio');
                }
            }
        });

        // ===================================================
        // CALCULAR DÍAS AUTOMÁTICOS (completo)
        // ===================================================
        $(document).on('change', '#edit-fecha-inicio, #edit-fecha-fin', function () {
            const fi = $('#edit-fecha-inicio').val();
            const ff = $('#edit-fecha-fin').val();

            if (fi && ff) {
                const diferencia = Math.ceil((new Date(ff) - new Date(fi)) / (1000 * 60 * 60 * 24)) + 1;
                $('#edit-dias').val(diferencia > 0 ? diferencia : '');
            }
        });

        // ===================================================
        // ABRIR MODAL: CARGAR DATOS
        // ===================================================
        function informacion(id) {
            openLoading();
            document.getElementById("formulario-editar").reset();
            $('#edit-seccion-fraccionado, #edit-seccion-completo').hide();
            $('#edit-lista-empleados').hide();

            axios.post(urlAdmin + '/admin/historial/compensatorio/informacion', { id: id })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        const info = response.data.info;

                        $('#id-editar').val(info.id);
                        $('#edit-fechaEntrego').val(info.fecha);
                        $('#edit-razon').val(info.razon);

                        $('#edit-empleado-id').val(info.id_empleado);
                        $('#edit-empleado-nombre').val(info.nombre_empleado ?? '');
                        $('#edit-unidad').val(info.unidad ?? '');
                        $('#edit-cargo').val(info.cargo ?? '');
                        $('#edit-empleado-actual').show();
                        $('#edit-bloque-buscar').hide();

                        $(`input[name="edit-condicion"][value="${info.condicion}"]`).prop('checked', true);
                        toggleEditCondicion(info.condicion);

                        if (info.condicion == 1) {
                            $('#edit-fecha-solicitud').val(info.fecha_fraccionado);
                            $('#edit-hora-inicio').val(info.hora_inicio);
                            $('#edit-hora-fin').val(info.hora_fin);
                            $('#edit-hora-fin').trigger('change');
                        } else {
                            $('#edit-fecha-inicio').val(info.fecha_inicio);
                            $('#edit-fecha-fin').val(info.fecha_fin);
                            $('#edit-fecha-fin').trigger('change');
                        }

                        $('#modalEditar').modal('show');

                    } else {
                        toastr.error('Información no encontrada');
                    }
                })
                .catch(() => {
                    closeLoading();
                    toastr.error('Error al obtener información');
                });
        }

        // ===================================================
        // GUARDAR EDICIÓN
        // ===================================================
        function editar() {
            const id           = $('#id-editar').val();
            const empleadoId   = $('#edit-empleado-id').val();
            const condicion    = $('input[name="edit-condicion"]:checked').val();
            const fechaEntrego = $('#edit-fechaEntrego').val();
            const razon        = $('#edit-razon').val().trim();

            if (!fechaEntrego)           { toastr.error('La fecha es requerida');      return; }
            if (!empleadoId)             { toastr.error('El empleado es requerido');   return; }
            if (condicion === undefined) { toastr.error('La condición es requerida');  return; }

            let extras = {};

            if (condicion == '1') {
                const fechaFraccionado = $('#edit-fecha-solicitud').val();
                const horaInicio       = $('#edit-hora-inicio').val();
                const horaFin          = $('#edit-hora-fin').val();
                const duracion         = $('#edit-duracion').val();

                if (!fechaFraccionado || !horaInicio || !horaFin) {
                    toastr.error('Complete todos los campos del permiso fraccionado');
                    return;
                }
                extras = { fecha_fraccionado: fechaFraccionado, hora_inicio: horaInicio, hora_fin: horaFin, duracion };

            } else {
                const fechaInicio = $('#edit-fecha-inicio').val();
                const fechaFin    = $('#edit-fecha-fin').val();
                const dias        = $('#edit-dias').val();

                if (!fechaInicio || !fechaFin) {
                    toastr.error('Complete todos los campos del permiso completo');
                    return;
                }
                if (new Date(fechaFin) < new Date(fechaInicio)) {
                    toastr.error('La fecha fin no puede ser menor que la fecha inicio');
                    return;
                }
                extras = { fecha_inicio: fechaInicio, fecha_fin: fechaFin, dias_solicitados: dias };
            }

            openLoading();

            const formData = new FormData();
            formData.append('id',           id);
            formData.append('empleado_id',  empleadoId);
            formData.append('condicion',    condicion);
            formData.append('fechaEntrego', fechaEntrego);
            formData.append('razon',        razon);
            Object.entries(extras).forEach(([k, v]) => formData.append(k, v ?? ''));

            axios.post(urlAdmin + '/admin/historial/compensatorio/actualizar', formData)
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
                    } else {
                        toastr.error(response.data.message ?? 'Error al actualizar');
                    }
                })
                .catch(() => {
                    closeLoading();
                    toastr.error('Error al actualizar');
                });
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

        let editTimeout = null;

        $(document).on('keyup', '#edit-buscar-empleado', function () {
            const texto = $(this).val();

            if (texto.length < 2) {
                $('#edit-lista-empleados').hide().html('');
                return;
            }

            clearTimeout(editTimeout);
            editTimeout = setTimeout(function () {
                axios.get(urlAdmin + '/admin/empleados/buscar', { params: { q: texto } })
                    .then(resp => {
                        let html = '';
                        resp.data.forEach(e => {
                            html += `
                                <button type="button"
                                    class="list-group-item list-group-item-action edit-empleado-item"
                                    data-id="${e.id}"
                                    data-unidad="${e.unidad}"
                                    data-cargo="${e.cargo}"
                                    data-nombre="${e.nombre}">
                                    <strong>${e.nombre}</strong>
                                    <small class="text-muted d-block">${e.cargo ?? ''} — ${e.unidad ?? ''}</small>
                                </button>`;
                        });
                        $('#edit-lista-empleados').html(html).show();
                    })
                    .catch(() => toastr.error('Error al buscar empleados'));
            }, 300);
        });

        $(document).on('click', '.edit-empleado-item', function () {
            $('#edit-empleado-id').val($(this).data('id'));
            $('#edit-empleado-nombre').val($(this).data('nombre'));
            $('#edit-unidad').val($(this).data('unidad'));
            $('#edit-cargo').val($(this).data('cargo'));
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
