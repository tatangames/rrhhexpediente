@extends('adminlte::page')

@section('title', 'Empleados')

@section('content_header')
    <h1>Empleados</h1>
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
                    Nuevo Empleado
                </button>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Empleado</li>
                    <li class="breadcrumb-item active">Listado de Empleados</li>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Empleado</h4>
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
                                        <label>Nombre: <span style="color: red">*</span></label>
                                        <input type="text" maxlength="100" class="form-control" id="nombre-nuevo"
                                               autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Unidad: <span style="color: red">*</span></label>
                                        <br>
                                        <select width="100%" class="form-control" id="select-unidad">
                                            @foreach($arrayUnidad as $item)
                                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Cargo: <span style="color: red">*</span></label>
                                        <br>
                                        <select width="100%" class="form-control" id="select-cargo">
                                            @foreach($arrayCargo as $item)
                                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                            @endforeach
                                        </select>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Empleado</h4>
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
                                        <label>Nombre: <span style="color: red">*</span></label>
                                        <input type="text" maxlength="100" class="form-control" id="nombre-editar"
                                               autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Unidad: <span style="color: red">*</span></label>
                                        <br>
                                        <select width="100%" class="form-control" id="select-unidad-editar">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Cargo: <span style="color: red">*</span></label>
                                        <br>
                                        <select width="100%" class="form-control" id="select-cargo-editar">
                                        </select>
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

    <!-- modal borrar -->
    <div class="modal fade" id="modalBorrar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Borrar Empleado</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id-borrar">

                    <p>
                        Se borrará a <strong id="nombre-borrar"></strong>
                        <span class="text-muted" id="detalle-borrar"></span>
                    </p>

                    <div id="bloque-sin-permisos" style="display:none">
                        <div class="alert alert-info mb-0">
                            Este empleado no tiene permisos registrados. Se puede borrar directamente.
                        </div>
                    </div>

                    <div id="bloque-con-permisos" style="display:none">
                        <p class="mb-1">Información relacionada que se trasladará:</p>
                        <table class="table table-sm table-bordered" style="max-width: 420px;">
                            <tbody id="tbody-resumen-borrar"></tbody>
                        </table>

                        <div class="form-group">
                            <label>Trasladar toda la información a: <span style="color: red">*</span></label>
                            <br>
                            <select class="form-control" id="select-destino-borrar" style="width:100%"></select>
                            <small class="text-muted">Puedes buscar por nombre, unidad o cargo.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="borrar()">
                        <i class="fas fa-trash"></i> Trasladar y borrar
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
            const ruta = "{{ url('/admin/empleados/tabla') }}";

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
            var ruta = "{{ url('/admin/empleados/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar() {
            document.getElementById("formulario-nuevo").reset();

            $('#modalAgregar').modal('show');
        }

        function nuevo() {
            var nombre = document.getElementById('nombre-nuevo').value;
            var unidad = document.getElementById('select-unidad').value;
            var cargo = document.getElementById('select-cargo').value;

            if (nombre === '') {
                toastr.error('Nombre es requerido');
                return;
            }
            if (unidad === '') {
                toastr.error('Unidad es requerido');
                return;
            }
            if (cargo === '') {
                toastr.error('Cargo es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('unidad', unidad);
            formData.append('cargo', cargo);

            axios.post(urlAdmin + '/admin/empleados/nuevo', formData, {})
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

            // Destruir Select2 antes de limpiar para evitar duplicados
            if ($('#select-unidad-editar').hasClass('select2-hidden-accessible')) {
                $('#select-unidad-editar').select2('destroy');
            }
            if ($('#select-cargo-editar').hasClass('select2-hidden-accessible')) {
                $('#select-cargo-editar').select2('destroy');
            }

            // Limpiar opciones
            $('#select-unidad-editar').empty();
            $('#select-cargo-editar').empty();

            axios.post(urlAdmin + '/admin/empleados/informacion', {
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);
                        $('#nombre-editar').val(response.data.info.nombre);

                        $.each(response.data.arrayUnidad, function( key, val ){
                            if(response.data.info.id_unidad == val.id){
                                $('#select-unidad-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-unidad-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });

                        $.each(response.data.arrayCargo, function( key, val ){
                            if(response.data.info.id_cargo == val.id){
                                $('#select-cargo-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-cargo-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });

                        // Inicializar Select2 con buscador en el modal editar
                        $('#select-unidad-editar').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#modalEditar'),
                            placeholder: 'Seleccione una unidad',
                            allowClear: true,
                            width: '100%'
                        });

                        $('#select-cargo-editar').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#modalEditar'),
                            placeholder: 'Seleccione un cargo',
                            allowClear: true,
                            width: '100%'
                        });

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
            var nombre = document.getElementById('nombre-editar').value;
            var unidad = $('#select-unidad-editar').val();
            var cargo = $('#select-cargo-editar').val();

            if (nombre === '') {
                toastr.error('Nombre es requerido');
                return;
            }
            if (!unidad) {
                toastr.error('Unidad es requerido');
                return;
            }
            if (!cargo) {
                toastr.error('Cargo es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('unidad', unidad);
            formData.append('cargo', cargo);

            axios.post(urlAdmin + '/admin/empleados/editar', formData, {})
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

        // ===================== BORRAR EMPLEADO =====================
        var totalPermisosBorrar = 0;

        function modalBorrar(id) {
            openLoading();

            if ($('#select-destino-borrar').hasClass('select2-hidden-accessible')) {
                $('#select-destino-borrar').select2('destroy');
            }
            $('#select-destino-borrar').empty();
            $('#tbody-resumen-borrar').empty();

            axios.post(urlAdmin + '/admin/empleados/resumen-borrar', {'id': id})
                .then((response) => {
                    closeLoading();

                    if (response.data.success !== 1) {
                        toastr.error('Información no encontrada');
                        return;
                    }

                    var emp = response.data.empleado;
                    totalPermisosBorrar = response.data.total;

                    $('#id-borrar').val(emp.id);
                    $('#nombre-borrar').text(emp.nombre);
                    $('#detalle-borrar').text('(' + (emp.unidad || 'Sin unidad') + ' - ' + (emp.cargo || 'Sin cargo') + ')');

                    if (totalPermisosBorrar === 0) {
                        $('#bloque-sin-permisos').show();
                        $('#bloque-con-permisos').hide();
                    } else {
                        $('#bloque-sin-permisos').hide();
                        $('#bloque-con-permisos').show();

                        $.each(response.data.resumen, function (i, fila) {
                            var tr = $('<tr>');
                            tr.append($('<td>').text(fila.tipo));
                            tr.append($('<td class="text-center">').text(fila.total));
                            $('#tbody-resumen-borrar').append(tr);
                        });
                        $('#tbody-resumen-borrar').append(
                            $('<tr class="font-weight-bold">')
                                .append($('<td>').text('TOTAL'))
                                .append($('<td class="text-center">').text(totalPermisosBorrar))
                        );

                        // opción vacía para el placeholder
                        $('#select-destino-borrar').append($('<option>').val('').text(''));
                        $.each(response.data.otros, function (i, o) {
                            var texto = o.nombre + ' — ' + (o.unidad || 'Sin unidad') + ' — ' + (o.cargo || 'Sin cargo');
                            $('#select-destino-borrar').append($('<option>').val(o.id).text(texto));
                        });

                        $('#select-destino-borrar').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#modalBorrar'),
                            placeholder: 'Busque y seleccione el empleado destino',
                            allowClear: true,
                            width: '100%'
                        });
                    }

                    $('#modalBorrar').modal('show');
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        function borrar() {
            var id = $('#id-borrar').val();
            var destino = $('#select-destino-borrar').val();

            if (totalPermisosBorrar > 0 && !destino) {
                toastr.error('Seleccione el empleado destino');
                return;
            }

            var texto = totalPermisosBorrar > 0
                ? 'Se trasladarán ' + totalPermisosBorrar + ' registro(s) al empleado seleccionado y se borrará este empleado.'
                : 'Se borrará este empleado.';

            Swal.fire({
                title: '¿Borrar empleado?',
                text: texto,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, borrar',
                cancelButtonText: 'Cancelar'
            }).then(function (result) {
                if (result.value) {
                    enviarBorrar(id, destino);
                }
            });
        }

        function enviarBorrar(id, destino) {
            openLoading();

            axios.post(urlAdmin + '/admin/empleados/borrar', {
                'id': id,
                'id_destino': destino || null
            })
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {
                        toastr.success('Empleado borrado correctamente');
                        $('#modalBorrar').modal('hide');
                        recargar();
                    } else if (response.data.success === 2) {
                        toastr.error('Seleccione un empleado destino válido');
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
