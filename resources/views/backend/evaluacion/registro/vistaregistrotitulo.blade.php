@extends('adminlte::page')

@section('title', 'Registrar Título')

@section('content_header')
    <h1>Registrar Título de Evaluación</h1>
@stop

@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content_top_nav_right')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">

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
                    <li class="breadcrumb-item active">Registrar Título</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card card-blue">
                        <div class="card-header">
                            <h3 class="card-title">Nuevo Título</h3>
                        </div>

                        <form id="formulario-nuevo">
                            <div class="card-body">

                                <div class="form-group">
                                    <label>Tipo de Evaluación</label>
                                    <select class="form-control" id="select-tipo">
                                        <option value="">Seleccionar...</option>
                                        @foreach($arrayTipos as $tipo)
                                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Título</label>
                                    <input type="text" maxlength="2000" class="form-control" id="nombre-nuevo"
                                           autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" maxlength="2000" class="form-control" id="descripcion-nuevo"
                                           autocomplete="off">
                                </div>

                            </div>

                            <div class="card-footer text-right">
                                <button type="button" class="btn btn-success" onclick="nuevo()">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}" type="text/javascript"></script>

    <script>
        $(function () {
            $('#select-tipo').select2({
                theme: "bootstrap-5",
                language: {
                    noResults: function () {
                        return "Búsqueda no encontrada";
                    }
                }
            });
        });

        function nuevo() {
            var tipo = document.getElementById('select-tipo').value;
            var nombre = document.getElementById('nombre-nuevo').value;
            var descripcion = document.getElementById('descripcion-nuevo').value;

            if (tipo === '') {
                toastr.error('Tipo de evaluación es requerido');
                return;
            }

            if (nombre === '') {
                toastr.error('Título es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id_evaluacion_tipo', tipo);
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);

            axios.post(urlAdmin + '/admin/evaluacion/titulo/nuevo', formData, {})
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {
                        toastr.success('Registrado correctamente');

                        // Limpiar formulario para seguir registrando
                        document.getElementById('formulario-nuevo').reset();
                        $('#select-tipo').val('').trigger('change');
                        document.getElementById('nombre-nuevo').focus();
                    } else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }
    </script>
@endsection
