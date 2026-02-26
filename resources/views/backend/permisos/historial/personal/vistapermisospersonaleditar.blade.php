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
            <div class="card card-blue">
                <div class="card-header">
                    <h3 class="card-title">Listado</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="tablaDatatable">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- modal editar -->
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

                                        <!-- Fecha -->
                                        <div class="form-group col-md-5 px-0">
                                            <label>Fecha: <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="edit-fecha-entrego">
                                        </div>
                                        <br>

                                        <!-- Goce de Sueldo -->
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

                                        <!-- Condicion -->
                                        <div class="form-group">
                                            <label>Condicion del Permiso: <span class="text-danger">*</span></label>
                                            <div class="d-flex align-items-center" style="gap: 20px;">
                                                <div class="custom-control custom-radio">
                                                    <input class="custom-control-input" type="radio"
                                                           id="edit-radio-fraccionado" name="edit-condicion" value="1">
                                                    <label for="edit-radio-fraccionado" class="custom-control-label">
                                                        <strong>Fraccionado</strong> (Por horas)
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

                                        <!-- Razon -->
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

                                            <!-- Empleado actual (readonly) -->
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

                                            <!-- Buscador (oculto por defecto) -->
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
                        sProcessing: "Procesando...",
                        sLengthMenu: "Mostrar _MENU_ registros",
                        sZeroRecords: "No se encontraron resultados",
                        sEmptyTable: "Ningun dato disponible en esta tabla",
                        sInfo: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        sInfoEmpty: "Mostrando 0 a 0 de 0 registros",
                        sInfoFiltered: "(filtrado de _MAX_ registros)",
                        sSearch: "Buscar:",
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

            function cargarTabla() {
                $('#tablaDatatable').load(ruta, function () {
                    initDataTable();
                });
            }

            // Primera carga
            cargarTabla();

            // Exponer para recargar desde otras funciones
            window.recargar = function () {
                cargarTabla();
            };
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/historial/personal/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function informacionBorrar(id) {
            Swal.fire({
                title: 'Borrar Registro',
                text: '',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                allowOutsideClick: false,
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarRegistro(id);
                }
            });
        }

        function borrarRegistro(id) {
            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(urlAdmin + '/admin/borrar/historial/permiso/personal', formData)
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
                        $('#edit-permiso-id-titulo').text('#' + info.id);
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
                            if (info.hora_inicio && info.hora_fin) {
                                $('#edit-hora-fin').trigger('change');
                            }
                        } else {
                            $('#edit-fecha-inicio-comp').val(info.fecha_inicio ?? '');
                            $('#edit-fecha-fin-comp').val(info.fecha_fin ?? '');
                            if (info.fecha_inicio && info.fecha_fin) {
                                $('#edit-fecha-fin-comp').trigger('change');
                            }
                        }

                        $('#modalEditar').modal('show');

                    } else {
                        toastr.error('Informacion no encontrada');
                    }
                })
                .catch(() => {
                    closeLoading();
                    toastr.error('Error al obtener informacion');
                });
        }

        $(document).on('change', 'input[name="edit-condicion"]', function () {
            toggleEditCondicion($(this).val());
        });

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
            let horaInicio = $('#edit-hora-inicio').val();
            let horaFin    = $('#edit-hora-fin').val();

            if (horaInicio && horaFin) {
                let [hI, mI] = horaInicio.split(':');
                let [hF, mF] = horaFin.split(':');

                let ini = new Date(); ini.setHours(parseInt(hI), parseInt(mI), 0, 0);
                let fin = new Date(); fin.setHours(parseInt(hF), parseInt(mF), 0, 0);

                let diff = (fin - ini) / (1000 * 60);

                if (diff > 0) {
                    $('#edit-minutos-permiso').val(diff);
                    let h = Math.floor(diff / 60), m = diff % 60;
                    let t = '';
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
            let fi = $('#edit-fecha-inicio-comp').val();
            let ff = $('#edit-fecha-fin-comp').val();
            if (fi && ff) {
                let diferencia = Math.ceil((new Date(ff) - new Date(fi)) / (1000 * 60 * 60 * 24)) + 1;
                $('#edit-dias-solicitados').val(diferencia > 0 ? diferencia : '');
            }
        });

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
            if (razon.length > 800)       { toastr.error('La razon no puede exceder los 800 caracteres'); return; }

            const formData = new FormData();
            formData.append('id',           id);
            formData.append('empleado_id',  empleadoId);
            formData.append('condicion',    condicion);
            formData.append('fechaEntrego', fechaEntrego);
            formData.append('goce_sueldo',  goceSueldo);
            formData.append('razon',        razon);

            if (condicion == '1') {
                const fechaFrac  = $('#edit-fecha-fraccionado').val();
                const horaInicio = $('#edit-hora-inicio').val();
                const horaFin    = $('#edit-hora-fin').val();
                const minutos    = $('#edit-minutos-permiso').val();

                if (!fechaFrac || !horaInicio || !horaFin) {
                    toastr.error('Complete todos los campos del permiso fraccionado'); return;
                }
                if (!minutos || minutos == 0) {
                    toastr.error('El permiso debe ser de al menos 1 minuto'); return;
                }

                formData.append('fecha_fraccionado', fechaFrac);
                formData.append('hora_inicio',       horaInicio);
                formData.append('hora_fin',          horaFin);
                formData.append('duracion_minutos',  parseInt(minutos));

            } else {
                const fechaInicio = $('#edit-fecha-inicio-comp').val();
                const fechaFin    = $('#edit-fecha-fin-comp').val();
                const dias        = $('#edit-dias-solicitados').val();

                if (!fechaInicio || !fechaFin) {
                    toastr.error('Complete todos los campos del permiso completo'); return;
                }
                if (new Date(fechaFin) < new Date(fechaInicio)) {
                    toastr.error('La fecha fin no puede ser menor que la fecha inicio'); return;
                }

                formData.append('fecha_inicio',     fechaInicio);
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
                        if (response.data.tipo === 'limite_excedido' && response.data.data) {
                            mostrarModalLimite(response.data.data);
                        } else {
                            toastr.error(response.data.message ?? 'Error al actualizar');
                        }
                    }
                })
                .catch((err) => {
                    closeLoading();
                    if (err.response && err.response.data) {
                        if (err.response.data.tipo === 'limite_excedido' && err.response.data.data) {
                            mostrarModalLimite(err.response.data.data);
                        } else {
                            toastr.error(err.response.data.message || 'Error al actualizar');
                        }
                    } else {
                        toastr.error('Error al actualizar');
                    }
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
            const dias  = Math.floor(minutos / 480);
            const rest  = minutos % 480;
            const horas = Math.floor(rest / 60);
            const mins  = rest % 60;
            let r = '';
            if (dias  > 0) r += dias  + (dias  === 1 ? ' dia'  : ' dias');
            if (horas > 0) { if (r) r += ', '; r += horas + (horas === 1 ? ' hora'  : ' horas'); }
            if (mins  > 0) { if (r) r += ', '; r += mins  + ' min'; }
            return r || '0 min';
        }

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

            if (texto.length < 2) {
                $('#edit-lista-empleados').hide().html('');
                return;
            }

            if ($(this).data('buscando')) return;
            const $input = $(this);
            $input.data('buscando', true);

            axios.get(urlAdmin + '/admin/empleados/buscar', { params: { q: texto } })
                .then(resp => {
                    let html = '';
                    resp.data.forEach(e => {
                        html += '<button type="button"' +
                            ' class="list-group-item list-group-item-action edit-empleado-item"' +
                            ' data-id="' + e.id + '"' +
                            ' data-unidad="' + e.unidad + '"' +
                            ' data-cargo="' + e.cargo + '"' +
                            ' data-nombre="' + e.nombre + '">' +
                            '<strong>' + e.nombre + '</strong>' +
                            '<small class="text-muted d-block">' + (e.cargo ?? '') + ' - ' + (e.unidad ?? '') + '</small>' +
                            '</button>';
                    });
                    $('#edit-lista-empleados').html(html).show();
                })
                .catch(() => {
                    toastr.error('Error al buscar empleados');
                })
                .finally(() => {
                    $input.data('buscando', false);
                });
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

    <script>
        (function () {
            const SERVER_DEFAULT = {{ $temaPredeterminado }};
            const iconEl = document.getElementById('theme-icon');

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

            function applyTheme(mode) {
                const dark = mode === 'dark';
                document.body.classList.toggle('dark-mode', dark);
                document.documentElement.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
                if (iconEl) {
                    iconEl.classList.remove('fa-sun', 'fa-moon');
                    iconEl.classList.add(dark ? 'fa-moon' : 'fa-sun');
                }
            }

            function intToTheme(v) { return v === 1 ? 'dark' : 'light'; }
            function themeToInt(m) { return m === 'dark' ? 1 : 0; }

            applyTheme(intToTheme(SERVER_DEFAULT));

            let saving = false;
            document.addEventListener('click', async (e) => {
                const a = e.target.closest('.dropdown-item[data-theme]');
                if (!a) return;
                e.preventDefault();
                if (saving) return;

                const selectedMode  = a.dataset.theme;
                const previousMode  = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
                applyTheme(selectedMode);

                try {
                    saving = true;
                    await axios.post(urlAdmin + '/admin/actualizar/tema', { tema: themeToInt(selectedMode) });
                    if (window.toastr) toastr.success('Tema actualizado');
                } catch (err) {
                    applyTheme(previousMode);
                    if (window.toastr) toastr.error('No se pudo actualizar el tema');
                } finally {
                    saving = false;
                }
            });
        })();
    </script>

@endsection
