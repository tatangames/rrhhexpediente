@extends('adminlte::page')

@section('title', 'Unidades')

@section('content_header')
    <h1>Unidades</h1>
@stop
{{-- Activa plugins que necesitas --}}
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
            <div class="col-sm-6">
                <button type="button"
                        onclick="modalAgregar()"
                        class="btn btn-primary btn-sm">
                    <i class="fas fa-pencil-alt"></i>
                    Nueva Unidad
                </button>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Unidad</li>
                    <li class="breadcrumb-item active">Listado de Unidades</li>
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

    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nueva Unidad</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Unidad</label>
                                        <input type="text" maxlength="100" class="form-control" id="unidad-nuevo"
                                               autocomplete="off">
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button"
                            class="btn btn-success btn-sm" onclick="nuevo()">Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal editar -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Unidad</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-editar">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" id="id-editar">
                                    </div>

                                    <div class="form-group">
                                        <label>Unidad</label>
                                        <input type="text" maxlength="100" class="form-control" id="unidad-editar"
                                               autocomplete="off">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button"
                            class="btn btn-success btn-sm" onclick="editar()">Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal borrar (con traslado de empleados) -->
    <div class="modal fade" id="modalBorrar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Borrar Unidad</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id-borrar">

                    <p>Unidad a borrar: <strong id="nombre-borrar"></strong></p>

                    <div id="bloque-sin-empleados" class="alert alert-success" style="display: none;">
                        Esta unidad no tiene empleados asignados. Se puede borrar directamente.
                    </div>

                    <div id="bloque-con-empleados" style="display: none;">
                        <div class="alert alert-warning">
                            Estos empleados pertenecen a esta unidad. Indique a qué unidad se trasladará cada uno
                            antes de borrarla.
                        </div>

                        <div class="form-group">
                            <label>Mover todos a:</label>
                            <select id="todos-destino" class="form-control form-control-sm"></select>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th style="width: 40%">Empleado</th>
                                    <th style="width: 25%">Cargo</th>
                                    <th style="width: 35%">Nueva unidad</th>
                                </tr>
                                </thead>
                                <tbody id="tbody-borrar"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button"
                            class="btn btn-danger btn-sm" onclick="borrar()">Trasladar y borrar
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
    <script>
        $(function () {
            const ruta = "{{ url('/admin/permisos/unidad/tabla') }}";

            function initDataTable() {
                // Si ya hay instancia, destrúyela antes de re-crear
                if ($.fn.DataTable.isDataTable('#tabla')) {
                    $('#tabla').DataTable().destroy();
                }

                // Inicializa
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
                        sEmptyTable: "Ningún dato disponible en esta tabla",
                        sInfo: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        sInfoEmpty: "Mostrando 0 a 0 de 0 registros",
                        sInfoFiltered: "(filtrado de _MAX_ registros)",
                        sSearch: "Buscar:",
                        oPaginate: {sFirst: "Primero", sLast: "Último", sNext: "Siguiente", sPrevious: "Anterior"},
                        oAria: {sSortAscending: ": Orden ascendente", sSortDescending: ": Orden descendente"}
                    },
                    dom:
                        "<'row align-items-center'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-md-right'f>>" +
                        "tr" +
                        "<'row align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
                });

                // Estilitos
                $('#tabla_length select').addClass('form-control form-control-sm');
                $('#tabla_filter input').addClass('form-control form-control-sm').css('display', 'inline-block');
            }

            function cargarTabla() {
                $('#tablaDatatable').load(ruta, function () {
                    // AQUI debe existir exactamente un <table id="tabla"> en la parcial
                    initDataTable();
                });
            }

            // Primera carga
            cargarTabla();

            // Exponer recarga para tus flujos (crear/editar)
            window.recargar = function () {
                cargarTabla();
            };
        });
    </script>


    <script>

        function recargar() {
            var ruta = "{{ url('/admin/permisos/unidad/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar() {
            document.getElementById("formulario-nuevo").reset();

            $('#modalAgregar').modal('show');
        }

        function nuevo() {
            var nombre = document.getElementById('unidad-nuevo').value;

            if (nombre === '') {
                toastr.error('Nombre es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('nombre', nombre);

            axios.post(urlAdmin + '/admin/permisos/unidad/nuevo', formData, {})
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Registrado correctamente');
                        $('#modalAgregar').modal('hide');
                        recargar();
                    } else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        function informacion(id) {
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(urlAdmin + '/admin/permisos/unidad/informacion', {
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);
                        $('#unidad-editar').val(response.data.info.nombre);

                    } else {
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        function editar() {
            var id = document.getElementById('id-editar').value;
            var nombre = document.getElementById('unidad-editar').value;

            if (nombre === '') {
                toastr.error('Nombre es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);

            axios.post(urlAdmin + '/admin/permisos/unidad/editar', formData, {})
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
                    } else {
                        toastr.error('Error al actualizar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al actualizar');
                    closeLoading();
                });
        }

        // =================== BORRAR UNIDAD (CON TRASLADO) ===================

        // Unidades disponibles como destino (todas menos la que se borra)
        var unidadesDestino = [];

        function crearOpcionesSelect(select) {
            select.innerHTML = '';
            select.add(new Option('-- Seleccione --', ''));
            unidadesDestino.forEach(function (u) {
                select.add(new Option(u.nombre, u.id));
            });
        }

        function modalBorrar(id) {
            openLoading();

            axios.post(urlAdmin + '/admin/permisos/unidad/empleados', {
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if (response.data.success !== 1) {
                        toastr.error('Información no encontrada');
                        return;
                    }

                    var empleados = response.data.empleados;
                    unidadesDestino = response.data.unidades;

                    if (empleados.length > 0 && unidadesDestino.length === 0) {
                        toastr.error('No hay otra unidad a donde trasladar los empleados. Cree una primero.');
                        return;
                    }

                    $('#id-borrar').val(id);
                    $('#nombre-borrar').text(response.data.unidad.nombre);

                    var tbody = document.getElementById('tbody-borrar');
                    tbody.innerHTML = '';

                    if (empleados.length === 0) {
                        $('#bloque-con-empleados').hide();
                        $('#bloque-sin-empleados').show();
                    } else {
                        $('#bloque-sin-empleados').hide();
                        $('#bloque-con-empleados').show();

                        crearOpcionesSelect(document.getElementById('todos-destino'));

                        empleados.forEach(function (emp) {
                            var tr = document.createElement('tr');
                            tr.setAttribute('data-id', emp.id);

                            var tdNombre = document.createElement('td');
                            tdNombre.textContent = emp.nombre;

                            var tdCargo = document.createElement('td');
                            tdCargo.textContent = emp.cargo;

                            var tdSelect = document.createElement('td');
                            var select = document.createElement('select');
                            select.className = 'form-control form-control-sm select-destino';
                            crearOpcionesSelect(select);
                            tdSelect.appendChild(select);

                            tr.appendChild(tdNombre);
                            tr.appendChild(tdCargo);
                            tr.appendChild(tdSelect);
                            tbody.appendChild(tr);
                        });
                    }

                    $('#modalBorrar').modal('show');
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        // "Mover todos a": copia la unidad elegida a todos los empleados
        $(document).on('change', '#todos-destino', function () {
            var valor = this.value;
            if (valor === '') {
                return;
            }
            $('#tbody-borrar .select-destino').val(valor);
        });

        function borrar() {
            var id = document.getElementById('id-borrar').value;
            var nombre = document.getElementById('nombre-borrar').textContent;

            // Armar traslados { id_empleado: id_unidad_destino }
            var traslados = {};
            var faltante = false;

            $('#tbody-borrar tr').each(function () {
                var idEmpleado = this.getAttribute('data-id');
                var destino = $(this).find('.select-destino').val();

                if (!destino) {
                    faltante = true;
                    return false;
                }
                traslados[idEmpleado] = destino;
            });

            if (faltante) {
                toastr.error('Seleccione la unidad destino de cada empleado');
                return;
            }

            var total = Object.keys(traslados).length;
            var texto = total > 0
                ? 'Se trasladarán ' + total + ' empleado(s) y se borrará la unidad "' + nombre + '". Esta acción no se puede deshacer.'
                : 'Se borrará la unidad "' + nombre + '". Esta acción no se puede deshacer.';

            Swal.fire({
                title: '¿Borrar unidad?',
                text: texto,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Sí, borrar'
            }).then((result) => {
                if (result.value) {
                    enviarBorrar(id, traslados);
                }
            });
        }

        function enviarBorrar(id, traslados) {
            openLoading();

            axios.post(urlAdmin + '/admin/permisos/unidad/borrar', {
                'id': id,
                'traslados': traslados
            })
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {
                        toastr.success('Unidad borrada correctamente');
                        $('#modalBorrar').modal('hide');
                        recargar();
                    } else if (response.data.success === 2) {
                        // La lista de empleados cambió o hay un destino inválido: recargar el modal
                        toastr.error('La lista de empleados cambió, revise los traslados');
                        modalBorrar(id);
                    } else {
                        toastr.error('Error al borrar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al borrar');
                });
        }
    </script>


@endsection
