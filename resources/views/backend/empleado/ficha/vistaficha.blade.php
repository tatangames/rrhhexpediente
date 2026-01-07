@extends('adminlte::page')

@section('title', 'Ficha')

@section('content_header')
    <h1>Ficha</h1>
@stop


{{-- Activa plugins que necesitas --}}
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content_top_nav_right')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">

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

            <div class="dropdown-divider"></div>

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

    <!-- FORMULARIO FICHA -->

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Ficha del Empleado</h3>
        </div>

        <form method="POST" action="#">
            @csrf

            <div class="card-body">

                {{-- DATOS GENERALES --}}
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Nombre del empleado</label>
                            <input type="text" class="form-control" value="{{ $nombre }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>DUI / NIT</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Distrito</label>
                            <select width="100%"  class="form-control" id="select-distrito">
                                @foreach($arrayDistritos as $sel)
                                    <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Unidad</label>
                            <select width="100%"  class="form-control" id="select-unidad">
                                @foreach($arrayUnidades as $sel)
                                    <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Cargo</label>
                            <select width="100%" class="form-control" id="select-cargo">
                                @foreach($arrayCargos as $sel)
                                    <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Fecha de ingreso</label>
                            <input type="date" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Salario actual</label>
                            <input
                                type="text"
                                class="form-control"
                                inputmode="decimal"
                                placeholder="0.00"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                            >
                        </div>
                    </div>
                </div>

                <hr>

                {{-- INFORMACIÓN PARTICULAR --}}
                <h5 class="text-center font-weight-bold">INFORMACIÓN PARTICULAR</h5>

                <div class="row mt-3">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Fecha nacimiento</label>
                            <input type="date" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Lugar de Nacimiento</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="form-group">
                        <label>Nivel académico</label>
                        <select name="nivel_academico" id="nivel_academico" class="form-control">
                            <option value="0">Seleccione</option>
                            <option value="1">Parvularia</option>
                            <option value="2">Educación Básica</option>
                            <option value="3">Bachillerato</option>
                            <option value="4">Técnico</option>
                            <option value="6">Universitario</option>
                            <option value="7">Postgrado</option>
                            <option value="8">Otro</option>
                        </select>
                    </div>

                    <div class="form-group d-none" id="otro_nivel">
                        <label>Especifique nivel académico</label>
                        <input type="text" class="form-control" name="nivel_academico_otro">
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profesión</label>
                            <input type="text" class="form-control" name="profesion">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Dirección actual</label>
                    <input type="text" class="form-control" name="direccion_actual">
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Estado civil</label>
                            <select class="form-control" name="estado_civil">
                                <option value="">Seleccione</option>
                                <option>Soltero</option>
                                <option>Casado</option>
                                <option>Divorciado</option>
                                <option>Viudo</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Celular</label>
                            <input type="text" class="form-control" name="celular">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>En caso de emergencia llamar a</label>
                            <input type="text" class="form-control" name="contacto_emergencia">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Celular emergencia</label>
                            <input type="text" class="form-control" name="celular_emergencia">
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>






@stop



@section('js')
    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script>
        window.AppConfig = {
            themeDefault: {{ $temaPredeterminado }}, // 0 light | 1 dark
            updateThemeUrl: "{{ route('admin.tema.update') }}",
        };
    </script>

    <script src="{{ asset('js/theme.js') }}"></script>



    <script>

        document.getElementById('nivel_academico').addEventListener('change', function () {
            document.getElementById('otro_nivel')
                .classList.toggle('d-none', this.value !== 'Otro');
        });

        $('#select-unidad').select2({
            theme: "bootstrap-5",
            "language": {
                "noResults": function(){
                    return "Busqueda no encontrada";
                }
            },
        });

        $('#select-cargo').select2({
            theme: "bootstrap-5",
            "language": {
                "noResults": function(){
                    return "Busqueda no encontrada";
                }
            },
        });

    </script>






@endsection
