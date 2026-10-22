@extends('adminlte::page')

@section('title', 'Items')

@section('content_header')
    <h1>Items: {{ $infoTitulo->nombre }}</h1>
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
            <div class="col-sm-6">
                <button type="button"
                        onclick="modalAgregar()"
                        class="btn btn-primary btn-sm">
                    <i class="fas fa-pencil-alt"></i>
                    Nuevo Item
                </button>

                <a href="{{ route('evaluacion.titulo.listado.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Título</li>
                    <li class="breadcrumb-item active">Items</li>
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
                    <p class="text-muted mb-2">
                        <i class="fas fa-info-circle"></i> Arrastra las filas con el ícono <i class="fas fa-grip-vertical"></i> para cambiar la posición.
                    </p>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Item</h4>
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
                                        <label>Nombre</label>
                                        <input type="text" maxlength="2000" class="form-control" id="nombre-nuevo"
                                               autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Puntos</label>
                                        <input type="number" step="0.01" min="0" value="2.5" class="form-control" id="puntos-nuevo"
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
                    <h4 class="modal-title">Editar Item</h4>
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
                                        <label>Nombre</label>
                                        <input type="text" maxlength="2000" class="form-control" id="nombre-editar"
                                               autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Puntos</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="puntos-editar"
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
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <script>

        var idTitulo = {{ $infoTitulo->id }};
        var sortableInstancia = null;

        $(function () {
            cargarTabla();
        });

        function cargarTabla() {
            var ruta = "{{ url('/admin/evaluacion/titulo/detalle/tabla/' . $infoTitulo->id) }}";

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

            axios.post(urlAdmin + '/admin/evaluacion/titulo/detalle/posicion', {
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

        function modalAgregar() {
            document.getElementById("formulario-nuevo").reset();

            $('#modalAgregar').modal('show');
        }

        function nuevo() {
            var nombre = document.getElementById('nombre-nuevo').value;
            var puntos = document.getElementById('puntos-nuevo').value;

            if (puntos === '') {
                toastr.error('Puntos es requerido');
                return;
            }

            if (isNaN(puntos) || parseFloat(puntos) < 0) {
                toastr.error('Puntos debe ser un número válido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id_evaluacion_titulo', idTitulo);
            formData.append('nombre', nombre);
            formData.append('puntos', puntos);

            axios.post(urlAdmin + '/admin/evaluacion/titulo/detalle/nuevo', formData, {})
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

            axios.post(urlAdmin + '/admin/evaluacion/titulo/detalle/informacion', {
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

            if (puntos === '') {
                toastr.error('Puntos es requerido');
                return;
            }

            if (isNaN(puntos) || parseFloat(puntos) < 0) {
                toastr.error('Puntos debe ser un número válido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('puntos', puntos);

            axios.post(urlAdmin + '/admin/evaluacion/titulo/detalle/editar', formData, {})
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

            axios.post(urlAdmin + '/admin/evaluacion/titulo/detalle/borrar', formData)
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
