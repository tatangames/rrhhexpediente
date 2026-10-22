@extends('adminlte::page')

@section('title', 'Listado de Títulos')

@section('content_header')
    <h1>Títulos de Evaluación</h1>
@stop

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
                    <li class="breadcrumb-item">Evaluación</li>
                    <li class="breadcrumb-item active">Listado de Títulos</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card card-blue">
                <div class="card-header">
                    <h3 class="card-title">Filtros</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipo de Evaluación</label>
                                <select class="form-control" id="select-tipo">
                                    <option value="">Seleccionar...</option>
                                    @foreach($arrayTipos as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-blue">
                <div class="card-header">
                    <h3 class="card-title">Listado</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">
                        <i class="fas fa-info-circle"></i> Arrastra las filas con el ícono <i class="fas fa-grip-vertical"></i> para cambiar la posición.
                    </p>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="tablaDatatable">
                                <p class="text-center text-muted my-3">Selecciona un tipo de evaluación para ver sus títulos.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- modal editar -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Título</h4>
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
                                        <label>Descripción</label>
                                        <input type="text" maxlength="2000" class="form-control" id="descripcion-editar"
                                               autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>VISIBLE</label><br>
                                        <label class="switch" style="margin-top:10px">
                                            <input type="checkbox" id="check-visible">
                                            <div class="slider round">
                                                <span class="on">SI</span>
                                                <span class="off">NO</span>
                                            </div>
                                        </label>
                                    </div>

                                </div>
                            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <script>
        var sortableInstancia = null;

        $(function () {
            $('#select-tipo').select2({
                theme: "bootstrap-5",
                language: {
                    noResults: function () {
                        return "Búsqueda no encontrada";
                    }
                }
            });

            $('#select-tipo').on('change', function () {
                cargarTabla();
            });
        });

        function cargarTabla() {
            var idTipo = $('#select-tipo').val();

            if (idTipo === '' || idTipo === null) {
                $('#tablaDatatable').html('<p class="text-center text-muted my-3">Selecciona un tipo de evaluación para ver sus títulos.</p>');
                return;
            }

            var ruta = "{{ url('/admin/evaluacion/titulo/listado/tabla') }}/" + idTipo;

            $('#tablaDatatable').load(ruta, function () {
                iniciarSortable();
            });
        }

        function recargar() {
            cargarTabla();
        }

        function iniciarSortable() {
            var tbody = document.getElementById('tbody-posicion');

            if (!tbody) {
                return;
            }

            if (sortableInstancia) {
                sortableInstancia.destroy();
            }

            sortableInstancia = new Sortable(tbody, {
                handle: '.handle',
                animation: 150,
                onEnd: function () {
                    guardarOrden();
                }
            });
        }

        function guardarOrden() {
            var filas = document.querySelectorAll('#tbody-posicion tr');
            var orden = [];

            filas.forEach(function (fila, index) {
                orden.push(fila.getAttribute('data-id'));
                // Actualiza el número visible de inmediato
                fila.querySelector('.col-posicion').textContent = index + 1;
            });

            openLoading();

            axios.post(urlAdmin + '/admin/evaluacion/titulo/listado/posicion', {
                'orden': orden
            })
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {
                        toastr.success('Posición actualizada');
                    } else {
                        toastr.error('Error al actualizar posición');
                        recargar();
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al actualizar posición');
                    recargar();
                });
        }

        function informacion(id) {
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(urlAdmin + '/admin/evaluacion/titulo/listado/informacion', {
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);
                        $('#nombre-editar').val(response.data.info.nombre);
                        $('#descripcion-editar').val(response.data.info.descripcion);

                        if (response.data.info.estado === 0 || response.data.info.estado === false) {
                            $("#check-visible").prop("checked", false);
                        } else {
                            $("#check-visible").prop("checked", true);
                        }
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
            var descripcion = document.getElementById('descripcion-editar').value;

            var checkbox = document.getElementById('check-visible');
            var valorCheckbox = checkbox.checked ? 1 : 0;

            if (nombre === '') {
                toastr.error('Título es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('estado', valorCheckbox);

            axios.post(urlAdmin + '/admin/evaluacion/titulo/listado/editar', formData, {})
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

        function infoBorrar(id) {
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

        function borrarRegistro(id) {
            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(urlAdmin + '/admin/evaluacion/titulo/listado/borrar', formData)
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

        function infoItems(id) {
            window.location.href = "{{ url('/admin/evaluacion/titulo/detalle/index') }}/" + id;
        }

    </script>
@endsection
