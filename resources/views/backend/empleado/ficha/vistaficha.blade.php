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

            <div class="mb-2 text-left" style="margin: 10px">
                <button type="button" class="btn btn-sm btn-primary" onclick="agregarFilaBeneficiario()">
                    <i class="fas fa-plus"></i> Agregar beneficiario
                </button>
                <p style="color: red">La suma TOTAL del PORCENTAJE (%) debe ser 100</p>
            </div>

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
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-danger"
                                        onclick="eliminarFilaBeneficiario(this)"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>

            <small id="error-beneficiarios" style="font-size: 14px !important;" class="text-danger d-none"></small>



            <div class="card-footer">
                <div class="text-right">
                    <button type="button" onclick="guardarFicha()" class="btn btn-primary mr-2">
                        <i class="fas fa-save"></i> Guardar
                    </button>

                    <button type="button" onclick="imprimir()" class="btn btn-success">
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

        document.addEventListener('input', function (e) {

            // EDAD
            if (e.target.classList.contains('edad')) {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');

                if (e.target.value !== '') {
                    let val = parseInt(e.target.value);
                    if (val < 1) e.target.value = 1;
                    if (val > 150) e.target.value = 150;
                }
            }

            // PORCENTAJE
            if (e.target.classList.contains('porcentaje')) {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');

                if (e.target.value !== '') {
                    let val = parseInt(e.target.value);
                    if (val < 1) e.target.value = 1;
                    if (val > 100) e.target.value = 100;
                }
            }

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

            var selectDistrito = document.getElementById('select-distrito').value;
            var selectUnidad = document.getElementById('select-unidad').value;
            var selectCargo = document.getElementById('select-cargo').value;

            var fechaIngreso = document.getElementById('fecha-ingreso').value;
            var salario = document.getElementById('salario').value;

            var fechaNacimiento = document.getElementById('fecha-nacimiento').value;
            var lugarNacimiento = document.getElementById('lugar-nacimiento').value;

            var selectAcademico = document.getElementById('select-nivel').value;
            var otroNivel = document.getElementById('otro-nivel').value;
            var profesion = document.getElementById('profesion').value;

            var direccionActual = document.getElementById('direccion-actual').value;

            var selectCivil = document.getElementById('select-civil').value;
            var celular = document.getElementById('celular').value;

            var casoEmergencia = document.getElementById('emergencia-llamar').value;
            var contactoEmergencia = document.getElementById('celular-emergencia').value;

            var checkPadecimiento = document.getElementById('check-padecimiento').checked;
            var tipoPadecimiento = document.getElementById('tipo-padecimiento').value;


            let valido = true;

            // Textos
            if (!validarRequerido(nombre, 'error-nombre', 'El nombre es obligatorio')) valido = false;
            if (!validarRequerido(dui, 'error-dui', 'El DUI es obligatorio')) valido = false;
            if (!validarRequerido(fechaIngreso, 'error-fechaingreso', 'Fecha de Ingreso es obligatorio')) valido = false;
            if (!validarSalario(salario, 'error-salario')) {
                valido = false;
            }
            if (!validarRequerido(fechaNacimiento, 'error-fechanacimiento', 'Fecha de Nacimiento es obligatorio')) valido = false;
            if (!validarRequerido(lugarNacimiento, 'error-lugarnacimiento', 'Lugar de Nacimiento es obligatorio')) valido = false;
            if (!validarRequerido(profesion, 'error-profesion', 'Profesión es obligatorio')) valido = false;
            if (!validarRequerido(direccionActual, 'error-direccion', 'Dirección es obligatorio')) valido = false;
            if (!validarRequerido(celular, 'error-celular', 'Celular es obligatorio')) valido = false;
            if (!validarRequerido(casoEmergencia, 'error-casoemergencia', 'Campo es obligatorio')) valido = false;
            if (!validarRequerido(contactoEmergencia, 'error-celularemergencia', 'Campo es obligatorio')) valido = false;

            // Selects
            if (!validarSelect(selectDistrito, 'error-distrito', 'El Distrito es obligatorio')) valido = false;
            if (!validarSelect(selectUnidad, 'error-unidad', 'La Unidad es obligatoria')) valido = false;
            if (!validarSelect(selectCargo, 'error-cargo', 'El Cargo es obligatorio')) valido = false;
            if (!validarSelect(selectAcademico, 'error-nivelacademico', 'El Nivel Académico es obligatorio')) valido = false;
            if (!validarSelect(selectCivil, 'error-civil', 'El Estado Civil es obligatorio')) valido = false;

            // Condicional (nivel académico = 7)
            if (!validarRequeridoCondicional(
                selectAcademico === '7',
                otroNivel,
                'error-otronivel',
                'Especificar Nivel Académico es obligatorio'
            )) valido = false;

            if (!validarRequeridoCondicional(
                checkPadecimiento,
                tipoPadecimiento,
                'error-padecimiento',
                'Debe especificar el tipo de padecimiento'
            )) valido = false;

            if (!/^\d{9}$/.test(dui)) {
                mostrarError('error-dui', 'El DUI debe contener exactamente 9 dígitos');
                valido = false;
            } else {
                ocultarError('error-dui');
            }

            if (!validarBeneficiarios()) {
                toastr.error("Faltan Campos por Completar")
                return;
            }

            if(!valido){
                toastr.error("Faltan Campos por Completar")
                return
            }




            openLoading();

            let formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('dui', dui);

            formData.append('selectDistrito', selectDistrito);
            formData.append('selectUnidad', selectUnidad);
            formData.append('selectCargo', selectCargo);

            formData.append('fechaIngreso', fechaIngreso);
            formData.append('salario', salario);

            formData.append('fechaNacimiento', fechaNacimiento);
            formData.append('lugarNacimiento', lugarNacimiento);

            formData.append('selectAcademico', selectAcademico);
            formData.append('otroNivel', otroNivel);
            formData.append('profesion', profesion);
            formData.append('direccionActual', direccionActual);


            formData.append('selectCivil', selectCivil);
            formData.append('celular', celular);

            formData.append('casoEmergencia', casoEmergencia);
            formData.append('contactoEmergencia', contactoEmergencia);

            formData.append('tipoPadecimiento', tipoPadecimiento);

            let beneficiarios = [];

            document.querySelectorAll('#tabla-beneficiarios tbody tr').forEach(tr => {
                beneficiarios.push({
                    nombre: tr.querySelector('.nombre').value,
                    parentesco: tr.querySelector('.parentesco').value,
                    edad: tr.querySelector('.edad').value,
                    porcentaje: tr.querySelector('.porcentaje').value
                });
            });

            formData.append('beneficiarios', JSON.stringify(beneficiarios));



            axios.post(urlAdmin+'/empleado/ficha/actualizar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        actualizado()
                    }else{
                        toastr.error('Error al actualizar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al actualizar');
                    closeLoading();
                });
        }

        function validarSalario(valor, idError, maximo = 20000) {
            if (valor.trim() === '') {
                mostrarError(idError, 'Salario es obligatorio');
                return false;
            }

            const salario = parseFloat(valor);

            if (isNaN(salario) || salario <= 0) {
                mostrarError(idError, 'Salario inválido');
                return false;
            }

            if (salario > maximo) {
                mostrarError(idError, `El salario no puede ser mayor a ${maximo.toLocaleString()}`);
                return false;
            }

            ocultarError(idError);
            return true;
        }

        function validarRequerido(valor, idError, mensaje) {
            if (valor.trim() === '') {
                mostrarError(idError, mensaje);
                return false;
            } else {
                ocultarError(idError);
                return true;
            }
        }

        function validarSelect(valor, idError, mensaje) {
            if (valor === '0') {
                mostrarError(idError, mensaje);
                return false;
            }
            ocultarError(idError);
            return true;
        }

        function validarRequeridoCondicional(condicion, valor, idError, mensaje) {
            if (condicion) {
                if (valor.trim() === '') {
                    mostrarError(idError, mensaje);
                    return false;
                }
                ocultarError(idError);
            } else {
                ocultarError(idError);
            }
            return true;
        }


        function limpiarErrores() {
            ocultarError('error-nombre');
            ocultarError('error-usuario');
            ocultarError('error-password');
        }









        function agregarFilaBeneficiario() {
            const tbody = document.querySelector('#tabla-beneficiarios tbody');
            const index = tbody.children.length + 1;

            const tr = document.createElement('tr');

            tr.innerHTML = `
                <td class="text-center align-middle">${index}</td>
                <td>
                    <input type="text" class="form-control form-control-sm nombre" maxlength="50">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm parentesco" maxlength="50">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm edad" min="1" max="150">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm porcentaje" min="1" max="100">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarFilaBeneficiario(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
        }


        function eliminarFilaBeneficiario(btn) {
            btn.closest('tr').remove();
            renumerarBeneficiarios();
        }

        function renumerarBeneficiarios() {
            document.querySelectorAll('#tabla-beneficiarios tbody tr').forEach((tr, i) => {
                tr.children[0].innerText = i + 1;
            });
        }



        function recalcularPorcentaje() {
            const filas = document.querySelectorAll('#tabla-beneficiarios tbody tr');
            const total = filas.length;

            if (total === 0) return;

            const porcentajeBase = Math.floor(100 / total);
            let restante = 100;

            filas.forEach((tr, index) => {
                const input = tr.querySelector('.porcentaje');

                let valor = porcentajeBase;
                if (index === total - 1) {
                    valor = restante;
                }

                input.value = valor;
                restante -= valor;
            });
        }



        function validarBeneficiarios() {
            const filas = document.querySelectorAll('#tabla-beneficiarios tbody tr');

            if (filas.length === 0) {
                mostrarError('error-beneficiarios', 'Debe agregar al menos un beneficiario');
                return false;
            }

            let suma = 0;

            for (let tr of filas) {

                const nombre = tr.querySelector('.nombre').value.trim();
                const parentesco = tr.querySelector('.parentesco').value.trim();
                const edad = parseInt(tr.querySelector('.edad').value);
                const porcentaje = parseInt(tr.querySelector('.porcentaje').value);

                // Requeridos
                if (!nombre || !parentesco || isNaN(edad) || isNaN(porcentaje)) {
                    mostrarError('error-beneficiarios', 'Todos los campos del beneficiario son obligatorios');
                    return false;
                }

                // Rangos
                if (edad < 1 || edad > 150) {
                    mostrarError('error-beneficiarios', 'La edad debe estar entre 1 y 150');
                    return false;
                }

                if (porcentaje < 1 || porcentaje > 100) {
                    mostrarError('error-beneficiarios', 'El porcentaje debe estar entre 1 y 100');
                    return false;
                }

                suma += porcentaje;

                // ❌ nunca permitir pasar de 100
                if (suma > 100) {
                    mostrarError('error-beneficiarios', 'La suma del porcentaje no puede ser mayor a 100%');
                    return false;
                }
            }

            // ✅ obligatorio llegar a 100 exacto
            if (suma !== 100) {
                mostrarError('error-beneficiarios', 'La suma del porcentaje debe ser exactamente 100%');
                return false;
            }

            ocultarError('error-beneficiarios');
            return true;
        }



        function imprimir(){
            window.open("{{ URL::to('/empleado/reporte/pdf') }}");
        }


        function actualizado(){
            Swal.fire({
                title: 'Actualizado',
                text: "",
                icon: 'success',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            })
        }

    </script>




@endsection
