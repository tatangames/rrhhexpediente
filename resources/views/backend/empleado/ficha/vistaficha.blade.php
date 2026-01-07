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
                            <input type="text" maxlength="100" id="nombre" class="form-control" value="{{ $nombre }}">
                            <small id="error-nombre" class="text-danger d-none">
                                Este campo es obligatorio
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>DUI / NIT</label>
                            <input type="text" maxlength="50" id="dui" class="form-control">
                            <small id="error-dui" class="text-danger d-none">
                                Este campo es obligatorio
                            </small>
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Distrito</label>
                            <select width="100%"  class="form-control" id="select-distrito">
                                <option value="0">Seleccionar Opción</option>
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
                                <option value="0">Seleccionar Opción</option>
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
                                <option value="0">Seleccionar Opción</option>
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
                            <input type="date" class="form-control" id="fecha-ingreso">
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
                                id="salario"
                                min="0.00"
                                max="50000"
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
                            <input type="date" class="form-control" id="fecha-nacimiento">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Lugar de Nacimiento</label>
                            <input type="text" maxlength="100" class="form-control" id="lugar-nacimiento">
                        </div>
                    </div>

                </div>



                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nivel Académico</label>
                            <select width="100%"  class="form-control" id="select-nivel" name="nivel_academico">
                                <option value="0">Seleccionar Opción</option>
                                @foreach($arrayNiveles as $sel)
                                    <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 d-none" id="nivel-7">
                        <div class="form-group">
                            <label>Especifique nivel académico</label>
                            <input type="text" maxlength="100" class="form-control" name="nivel_otro" id="otro-nivel">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profesión</label>
                            <input type="text" maxlength="100" id="profesion" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Dirección actual</label>
                    <input type="text" class="form-control" maxlength="100" id="direccion-actual">
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Estado civil</label>
                            <select class="form-control" name="estado_civil" id="select-civil">
                                <option value="0">Seleccione</option>
                                <option value="1">Soltero</option>
                                <option value="2">Casado</option>
                                <option value="3">Divorciado</option>
                                <option value="4">Viudo</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Celular</label>
                            <input type="text" maxlength="20" id="celular" class="form-control" name="celular">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>En caso de emergencia llamar a</label>
                            <input type="text" class="form-control" id="emergencia-llamar" maxlength="50" name="contacto_emergencia">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Celular emergencia</label>
                            <input type="text" class="form-control" id="celular-emergencia" maxlength="20" name="celular_emergencia">
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-footer">
                <div class="text-right">
                    <button type="button" onclick="guardarFicha()" class="btn btn-primary mr-2">
                        <i class="fas fa-save"></i> Guardar
                    </button>

                    <button type="button" class="btn btn-success">
                        <i class="fas fa-print"></i> Imprimir PDF
                    </button>
                </div>
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

        document.getElementById('select-nivel').addEventListener('change', function () {

            const contenedor = document.getElementById('nivel-7');

            if (!contenedor) return;

            contenedor.classList.toggle('d-none', this.value !== '7');

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


    <script>

        function mostrarError(id, mensaje) {
            const el = document.getElementById(id);

            if (!el) {
                console.warn(`Elemento no encontrado: ${id}`);
                return;
            }

            el.innerText = mensaje;
            el.classList.remove('d-none');
        }

        function ocultarError(id) {
            const el = document.getElementById(id);

            if (!el) return; // ⬅️ evita el error

            el.innerText = '';
            el.classList.add('d-none');
        }

        function guardarFicha(){

            limpiarErrores();

            var nombre = document.getElementById('nombre').value;
            var dui = document.getElementById('dui').value;

            if (nombre.trim() === '') {
                mostrarError('error-nombre', 'El nombre es obligatorio');
                valido = false;
            } else {
                ocultarError('error-nombre');
            }

            if (dui.trim() === '') {
                mostrarError('error-dui', 'El dui es obligatorio');
                valido = false;
            } else {
                ocultarError('error-dui');
            }
        }

        function limpiarErrores() {
            ocultarError('error-nombre');
            ocultarError('error-usuario');
            ocultarError('error-password');
        }

    </script>




@endsection
