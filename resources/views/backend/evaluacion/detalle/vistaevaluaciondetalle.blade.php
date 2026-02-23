@extends('adminlte::page')

@section('title', 'Evaluación')

@section('content_header')
    <h1>Evaluación</h1>
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


    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <button type="button"
                        onclick="modalAgregar()"
                        class="btn btn-primary btn-sm">
                    <i class="fas fa-pencil-alt"></i>
                    Nuevo Título
                </button>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Evaluación</li>
                    <li class="breadcrumb-item active">Listado</li>
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
                    <h4 class="modal-title">Nuevo</h4>
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
                                        <label>Título</label>
                                        <input type="text" maxlength="2000" class="form-control" id="nombre-nuevo"
                                               autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Puntos</label>
                                        <input type="number" class="form-control" id="puntos-nuevo"
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar</h4>
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
                                        <label>Título</label>
                                        <input type="text" maxlength="2000" class="form-control" id="nombre-editar"
                                               autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Puntos</label>
                                        <input type="number" class="form-control" id="puntos-editar"
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
@stop



@section('js')
    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}" type="text/javascript"></script>

    <script>
        window.AppConfig = {
            themeDefault: {{ $temaPredeterminado }}, // 0 light | 1 dark
            updateThemeUrl: "{{ route('admin.tema.update') }}",
        };
    </script>

    <script src="{{ asset('js/theme.js') }}"></script>

    <script>
        $(function () {

            const evaluacionId = @json($id);
            const ruta = "{{ url('/admin/evaluacion-detalle/tabla') }}/"+ evaluacionId;

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
            const evaluacionId = @json($id);
            var ruta = "{{ url('/admin/evaluacion-detalle/tabla') }}/" + evaluacionId;
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar() {
            document.getElementById("formulario-nuevo").reset();

            $('#modalAgregar').modal('show');
        }

        function nuevo() {
            var nombre = document.getElementById('nombre-nuevo').value;
            var puntos = document.getElementById('puntos-nuevo').value;
            const evaluacionId = @json($id);

            if (nombre === '') {
                toastr.error('Título es requerido');
                return;
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(!puntos.match(reglaNumeroEntero)) {
                toastr.error('Puntos ser número Entero');
                return;
            }

            if(puntos <= 0){
                toastr.error('Puntos no debe tener negativos');
                return;
            }

            if(puntos > 300){
                toastr.error('Puntos máximo 300');
                return;
            }


            openLoading();
            var formData = new FormData();
            formData.append('id', evaluacionId);
            formData.append('nombre', nombre);
            formData.append('puntos', puntos);

            axios.post(urlAdmin + '/admin/evaluacion-detalle/nuevo', formData, {})
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

            axios.post(urlAdmin + '/admin/evaluacion-detalle/informacion', {
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);
                        $('#nombre-editar').val(response.data.info.nombre);
                        $('#puntos-editar').val(response.data.info.puntos);


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
            var puntos = document.getElementById('puntos-editar').value;


            if (nombre === '') {
                toastr.error('Título es requerido');
                return;
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(!puntos.match(reglaNumeroEntero)) {
                toastr.error('Puntos ser número Entero');
                return;
            }

            if(puntos <= 0){
                toastr.error('Puntos no debe tener negativos');
                return;
            }

            if(puntos > 300){
                toastr.error('Puntos máximo 300');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('puntos', puntos);

            axios.post(urlAdmin + '/admin/evaluacion-detalle/editar', formData, {})
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

        function infoBorrar(id){

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
                    borrarRegistro(id)
                }
            })
        }

        function borrarRegistro(id){
            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(urlAdmin + '/admin/evaluacion-detalle/borrar', formData)
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {
                        toastr.success('Borrado correctamente');
                        recargar();
                    } else {
                        toastr.error('Error al borrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al borrar');
                    closeLoading();
                });
        }






    </script>




@endsection
