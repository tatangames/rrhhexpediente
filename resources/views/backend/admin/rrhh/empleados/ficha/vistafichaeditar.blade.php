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

    <style>

        .checkbox-grande .form-check-input {
            transform: scale(1.5); /* tamaño del check */
            cursor: pointer;
        }

        .checkbox-grande .form-check-label {
            font-size: 1.05rem;
            cursor: pointer;
        }

        .form-switch .form-check-input {
            transform: scale(1.5);
        }

    </style>

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
                            <input type="text" maxlength="100" id="nombre" class="form-control" value="{{ $arrayInfo['nombre'] }}">
                            <small id="error-nombre" class="text-danger d-none">
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>DUI</label>

                            <input class="form-control"
                                   id="dui"
                                   type="number"
                                   placeholder="Ingrese su DUI"
                                   maxlength="9"
                                   value="{{ $arrayInfo['dui'] }}"
                                   required
                                   oninput="this.value = this.value.slice(0,9)">

                            <small id="error-dui" class="text-danger d-none">
                            </small>
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Distrito</label>
                            <select class="form-control" id="select-distrito">
                                <option value="0">Seleccionar Opción</option>
                                @foreach($arrayDistritos as $sel)
                                    @if($arrayInfo['id_distrito'] == $sel->id)
                                        <option value="{{ $sel->id }}" selected>{{ $sel->nombre }}</option>
                                    @else
                                        <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <small id="error-distrito" class="text-danger d-none">

                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Unidad</label>
                            <select class="form-control" id="select-unidad">
                                <option value="0">Seleccionar Opción</option>
                                @foreach($arrayUnidades as $sel)
                                    @if($arrayInfo['id_unidad'] == $sel->id)
                                        <option value="{{ $sel->id }}" selected>{{ $sel->nombre }}</option>
                                    @else
                                        <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <small id="error-unidad" class="text-danger d-none">

                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Cargo</label>
                            <select class="form-control" id="select-cargo">
                                <option value="0">Seleccionar Opción</option>
                                @foreach($arrayCargos as $sel)
                                    @if($arrayInfo['id_cargo'] == $sel->id)
                                        <option value="{{ $sel->id }}" selected>{{ $sel->nombre }}</option>
                                    @else
                                        <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <small id="error-cargo" class="text-danger d-none">
                            </small>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Fecha de ingreso</label>
                            <input type="date" class="form-control" id="fecha-ingreso" value="{{ $arrayInfo['fecha_ingreso'] }}">
                            <small id="error-fechaingreso" class="text-danger d-none">
                            </small>
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
                                value="{{ $arrayInfo['salario_actual'] }}"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                            >
                            <small id="error-salario" class="text-danger d-none">
                            </small>
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
                            <input type="date" class="form-control" id="fecha-nacimiento" value="{{ $arrayInfo['fecha_nacimiento'] }}">
                            <small id="error-fechanacimiento" class="text-danger d-none">
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Lugar de Nacimiento</label>
                            <input type="text" maxlength="100" class="form-control" id="lugar-nacimiento" value="{{ $arrayInfo['lugar_nacimiento'] }}">
                            <small id="error-lugarnacimiento" class="text-danger d-none">
                            </small>
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
                                    @if($arrayInfo['id_nivelacademico'] == $sel->id)
                                        <option value="{{ $sel->id }}" selected>{{ $sel->nombre }}</option>
                                    @else
                                        <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <small id="error-nivelacademico" class="text-danger d-none">
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4 d-none" id="nivel-7">
                        <div class="form-group">
                            <label>Especifique nivel académico</label>
                            <input type="text" maxlength="100" class="form-control" name="nivel_otro" id="otro-nivel" value="{{ $arrayInfo['otro_nivelacademico'] }}">
                            <small id="error-otronivel" class="text-danger d-none">
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profesión</label>
                            <input type="text" maxlength="100" id="profesion" class="form-control" value="{{ $arrayInfo['profesion'] }}">
                            <small id="error-profesion" class="text-danger d-none">
                            </small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Dirección actual</label>
                    <input type="text" class="form-control" maxlength="100" id="direccion-actual" value="{{ $arrayInfo['direccion'] }}">
                    <small id="error-direccion" class="text-danger d-none">
                    </small>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Estado civil</label>
                            <select class="form-control" name="estado_civil" id="select-civil">
                                <option value="0">Seleccione</option>
                                <option value="1" {{ $arrayInfo['estado_civil'] == 1 ? 'selected' : '' }}>Soltero</option>
                                <option value="2" {{ $arrayInfo['estado_civil'] == 2 ? 'selected' : '' }}>Casado</option>
                                <option value="3" {{ $arrayInfo['estado_civil'] == 3 ? 'selected' : '' }}>Divorciado</option>
                                <option value="4" {{ $arrayInfo['estado_civil'] == 4 ? 'selected' : '' }}>Viudo</option>
                                <option value="5" {{ $arrayInfo['estado_civil'] == 5 ? 'selected' : '' }}>Acompañado</option>
                            </select>
                            <small id="error-civil" class="text-danger d-none">
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Celular</label>
                            <input type="text" maxlength="20" id="celular" class="form-control" name="celular" value="{{ $arrayInfo['celular'] }}">
                            <small id="error-celular" class="text-danger d-none">
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>En caso de emergencia llamar a</label>
                            <input type="text" class="form-control" id="emergencia-llamar" maxlength="50" name="contacto_emergencia" value="{{ $arrayInfo['caso_emergencia'] }}">
                            <small id="error-casoemergencia" class="text-danger d-none">
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Celular emergencia</label>
                            <input type="text" class="form-control" id="celular-emergencia" maxlength="20" name="celular_emergencia" value="{{ $arrayInfo['celular_emergencia'] }}">
                            <small id="error-celularemergencia" class="text-danger d-none">
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" id="check-padecimiento" {{ !empty($arrayInfo['tipo_padecimiento']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="check-padecimiento">
                                ¿Padece alguna enfermedad crónica o condición física?
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row mt-2 d-none" id="contenedor-padecimiento">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo de padecimiento</label>
                            <input type="text" class="form-control" id="tipo-padecimiento" name="tipo_padecimiento" maxlength="100" value="{{ $arrayInfo['tipo_padecimiento'] }}">
                            <small id="error-padecimiento" class="text-danger d-none"></small>
                        </div>
                    </div>
                </div>
            </div>


            <!-- DATOS DEL BENEFICIARIO -->



            <hr>
            <h5 class="text-center font-weight-bold mt-4">DATOS BENEFICIARIOS</h5>



            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="tabla-beneficiarios">
                    <thead class="thead-light text-center">
                    <tr>
                        <th style="width: 50px">N°</th>
                        <th>Nombre</th>
                        <th>Parentesco</th>
                        <th style="width: 80px">Edad</th>
                        <th style="width: 120px">Porcentaje (%)</th>
                        <th style="width: 60px">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($arrayBeneficiarios) && count($arrayBeneficiarios) > 0)
                        @foreach($arrayBeneficiarios as $index => $ben)
                            <tr>
                                <td class="text-center align-middle">{{ $index + 1 }}</td>

                                <td>
                                    <input
                                        type="text"
                                        class="form-control form-control-sm nombre"
                                        maxlength="50"
                                        value="{{ $ben->nombre }}"
                                    >
                                </td>

                                <td>
                                    <input
                                        type="text"
                                        class="form-control form-control-sm parentesco"
                                        maxlength="50"
                                        value="{{ $ben->parentesco }}"
                                    >
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        class="form-control form-control-sm edad"
                                        min="1"
                                        max="150"
                                        value="{{ $ben->edad }}"
                                    >
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        class="form-control form-control-sm porcentaje"
                                        min="1"
                                        max="100"
                                        value="{{ $ben->porcentaje }}"
                                    >
                                </td>

                                <td class="text-center">

                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>

            <small id="error-beneficiarios" style="font-size: 14px !important;" class="text-danger d-none"></small>





        </form>
    </div>
@stop



@section('js')
    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}" type="text/javascript"></script>
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
        document.addEventListener('DOMContentLoaded', function () {

            const check = document.getElementById('check-padecimiento');
            const contenedor = document.getElementById('contenedor-padecimiento');
            const input = document.getElementById('tipo-padecimiento');

            if (input.value.trim() !== '') {
                check.checked = true;
                contenedor.classList.remove('d-none');
            } else {
                check.checked = false;
                contenedor.classList.add('d-none');
            }
        });
    </script>

    <script>

        document.getElementById('check-padecimiento').addEventListener('change', function () {
            const contenedor = document.getElementById('contenedor-padecimiento');
            const input = document.getElementById('tipo-padecimiento');

            if (this.checked) {
                contenedor.classList.remove('d-none');
            } else {
                contenedor.classList.add('d-none');
                input.value = ''; // limpia el campo si se desmarca
            }
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
