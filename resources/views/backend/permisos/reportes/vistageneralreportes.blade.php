@extends('adminlte::page')

@section('title', 'Reportes de Permisos')

@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content_top_nav_right')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />

    <li class="nav-item dropdown">
        <a href="#" class="nav-link" data-toggle="dropdown" role="button">
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
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">

    <section class="content" style="margin-top: 15px">
        <div class="container-fluid">

            <div class="row justify-content-center">

                <!-- Bloque: Reporte por Empleado -->
                <div class="col-md-6">
                    <div class="card card-secondary shadow">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-pdf mr-2"></i>Generar Reporte de Permisos Por Empleado
                            </h3>
                        </div>

                        <div class="card-body">

                            <div class="form-group">
                                <label>Empleado:</label>
                                <select class="form-control" id="select-empleado">
                                    <option value="0">-- TODOS --</option>
                                    @foreach($arrayEmpleados as $item)
                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Tipo de Permiso: <span style="color:red">*</span></label>
                                <select class="form-control" id="select-tipopermiso">
                                    <option value="0">-- TODOS --</option>
                                    <option value="1">Personal</option>
                                    <option value="2">Compensatorio</option>
                                    <option value="3">Enfermedad</option>
                                    <option value="4">Consulta Médica</option>
                                    <option value="5">Incapacidad</option>
                                    <option value="6">Otros</option>
                                </select>
                            </div>

                            <hr>

                            <p>Busca por Fecha de uso del Permiso</p>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Desde: <span style="color:red">*</span></label>
                                        <input type="date" class="form-control" id="fecha-desde">
                                        <small class="text-danger d-none" id="error-desde">
                                            La fecha de inicio es requerida.
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Hasta: <span style="color:red">*</span></label>
                                        <input type="date" class="form-control" id="fecha-hasta">
                                        <small class="text-danger d-none" id="error-hasta">
                                            La fecha de fin es requerida.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <small class="text-danger d-none" id="error-rango">
                                La fecha "Desde" no puede ser mayor que la fecha "Hasta".
                            </small>

                        </div>

                        <div class="card-footer d-flex gap-2">
                            <button type="button" onclick="generarReporte('pdf')"
                                    class="btn btn-outline-danger d-flex align-items-center">
                                <img src="{{ asset('images/logopdf.png') }}" width="28" height="28"
                                     style="margin-right:8px;">
                                Generar PDF
                            </button>

                            <button type="button" onclick="generarReporte('excel')"
                                    class="btn btn-outline-success d-flex align-items-center">
                                <img src="{{ asset('images/logoexcel.png') }}" width="28" height="28"
                                     style="margin-right:8px;">
                                Generar Excel
                            </button>
                        </div>

                    </div>
                </div>


                <!-- Bloque: Reporte por Unidad -->
                <div class="col-md-6">
                    <div class="card card-info shadow">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-building mr-2"></i>Generar Reporte de Permisos por Unidad
                            </h3>
                        </div>

                        <div class="card-body">

                            <div class="form-group">
                                <label>Unidad:</label>
                                <select class="form-control" id="select-unidad">
                                    <option value="0">-- TODAS --</option>
                                    @foreach($arrayUnidades as $item)
                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Tipo de Permiso: <span style="color:red">*</span></label>
                                <select class="form-control" id="select-tipopermiso-unidad">
                                    <option value="0">-- TODOS --</option>
                                    <option value="1">Personal</option>
                                    <option value="2">Compensatorio</option>
                                    <option value="3">Enfermedad</option>
                                    <option value="4">Consulta Médica</option>
                                    <option value="5">Incapacidad</option>
                                    <option value="6">Otros</option>
                                </select>
                            </div>

                            <hr>

                            <p>Busca por Fecha de uso del Permiso</p>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Desde: <span style="color:red">*</span></label>
                                        <input type="date" class="form-control" id="fecha-desde-unidad">
                                        <small class="text-danger d-none" id="error-desde-unidad">
                                            La fecha de inicio es requerida.
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Hasta: <span style="color:red">*</span></label>
                                        <input type="date" class="form-control" id="fecha-hasta-unidad">
                                        <small class="text-danger d-none" id="error-hasta-unidad">
                                            La fecha de fin es requerida.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <small class="text-danger d-none" id="error-rango-unidad">
                                La fecha "Desde" no puede ser mayor que la fecha "Hasta".
                            </small>

                        </div>

                        <div class="card-footer d-flex gap-2">
                            <button type="button" onclick="generarReportePorUnidad('pdf')"
                                    class="btn btn-outline-danger d-flex align-items-center">
                                <img src="{{ asset('images/logopdf.png') }}" width="28" height="28"
                                     style="margin-right:8px;">
                                Generar PDF
                            </button>

                            <button type="button" onclick="generarReportePorUnidad('excel')"
                                    class="btn btn-outline-success d-flex align-items-center">
                                <img src="{{ asset('images/logoexcel.png') }}" width="28" height="28"
                                     style="margin-right:8px;">
                                Generar Excel
                            </button>
                        </div>

                    </div>
                </div>

            </div>



            <!-- Bloque: Información General -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary shadow">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>Información General para Reportes
                            </h3>
                        </div>

                        <form id="form-info-general">
                            @csrf

                            <div class="card-body">

                                <!-- Fila 1: Px. Firmas y Salto de Página -->
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Distancia de Firmas: <span style="color:red">*</span></label>
                                            <input type="number" min="0" class="form-control"
                                                   name="px_firmas" id="px_firmas"
                                                   value="{{ old('px_firmas', $infoGeneral->px_firmas ?? 0) }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="d-block">Salto de Página:</label>
                                            <div class="custom-control custom-switch mt-2">
                                                <input type="checkbox" class="custom-control-input"
                                                       id="salto_pagina" name="salto_pagina" value="1"
                                                    {{ old('salto_pagina', $infoGeneral->salto_pagina ?? false) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="salto_pagina">Activar</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fila 2: Jefe -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Jefe:</label>
                                            <input type="text" maxlength="100" class="form-control"
                                                   name="jefe" id="jefe"
                                                   value="{{ old('jefe', $infoGeneral->jefe ?? '') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Fila 3: Cargo -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Cargo:</label>
                                            <input type="text" maxlength="100" class="form-control"
                                                   name="cargo" id="cargo"
                                                   value="{{ old('cargo', $infoGeneral->cargo ?? '') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Fila 4: Área -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Área:</label>
                                            <input type="text" maxlength="100" class="form-control"
                                                   name="area" id="area"
                                                   value="{{ old('area', $infoGeneral->area ?? '') }}">
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary" id="btn-guardar-info">
                                    <i class="fas fa-save mr-2"></i>Guardar Cambios
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
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>

        axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';

        $('#select-empleado').select2({
            theme: "bootstrap-5",
            language: { noResults: () => "Búsqueda no encontrada" }
        });

        $('#select-tipopermiso').select2({
            theme: "bootstrap-5",
            minimumResultsForSearch: Infinity
        });

        $('#fecha-desde').on('change', function () {
            $('#error-desde, #error-rango').addClass('d-none');
            $(this).removeClass('is-invalid');
        });

        $('#fecha-hasta').on('change', function () {
            $('#error-hasta, #error-rango').addClass('d-none');
            $(this).removeClass('is-invalid');
        });

        function generarReporte(tipo) {
            const idEmpleado = $('#select-empleado').val();
            const tipoPerm   = $('#select-tipopermiso').val();
            const fechaDesde = $('#fecha-desde').val();
            const fechaHasta = $('#fecha-hasta').val();

            let valido = true;

            $('#error-desde, #error-hasta, #error-rango').addClass('d-none');
            $('#fecha-desde, #fecha-hasta').removeClass('is-invalid');

            if (!fechaDesde) {
                $('#error-desde').removeClass('d-none');
                $('#fecha-desde').addClass('is-invalid');
                valido = false;
            }

            if (!fechaHasta) {
                $('#error-hasta').removeClass('d-none');
                $('#fecha-hasta').addClass('is-invalid');
                valido = false;
            }

            if (fechaDesde && fechaHasta && fechaDesde > fechaHasta) {
                $('#error-rango').removeClass('d-none');
                $('#fecha-desde, #fecha-hasta').addClass('is-invalid');
                valido = false;
            }

            if (!valido) return;

            const rutas = {
                pdf:   '{{ route("permiso.pdf.generar") }}',
                excel: '{{ route("permiso.excel.generar") }}'
            };

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = rutas[tipo];
            form.target = '_blank';

            const fields = {
                _token:       '{{ csrf_token() }}',
                tipo_permiso: tipoPerm,
                id_empleado:  idEmpleado,
                fecha_desde:  fechaDesde,
                fecha_hasta:  fechaHasta,
            };

            Object.entries(fields).forEach(([name, value]) => {
                const input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = name;
                input.value = value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }


        $('#select-unidad').select2({
            theme: "bootstrap-5",
            language: { noResults: () => "Búsqueda no encontrada" }
        });

        $('#select-tipopermiso-unidad').select2({
            theme: "bootstrap-5",
            minimumResultsForSearch: Infinity
        });

        $('#fecha-desde-unidad').on('change', function () {
            $('#error-desde-unidad, #error-rango-unidad').addClass('d-none');
            $(this).removeClass('is-invalid');
        });

        $('#fecha-hasta-unidad').on('change', function () {
            $('#error-hasta-unidad, #error-rango-unidad').addClass('d-none');
            $(this).removeClass('is-invalid');
        });

        function generarReportePorUnidad(tipo) {
            const idUnidad   = $('#select-unidad').val();
            const tipoPerm   = $('#select-tipopermiso-unidad').val();
            const fechaDesde = $('#fecha-desde-unidad').val();
            const fechaHasta = $('#fecha-hasta-unidad').val();

            let valido = true;

            $('#error-desde-unidad, #error-hasta-unidad, #error-rango-unidad').addClass('d-none');
            $('#fecha-desde-unidad, #fecha-hasta-unidad').removeClass('is-invalid');

            if (!fechaDesde) {
                $('#error-desde-unidad').removeClass('d-none');
                $('#fecha-desde-unidad').addClass('is-invalid');
                valido = false;
            }

            if (!fechaHasta) {
                $('#error-hasta-unidad').removeClass('d-none');
                $('#fecha-hasta-unidad').addClass('is-invalid');
                valido = false;
            }

            if (fechaDesde && fechaHasta && fechaDesde > fechaHasta) {
                $('#error-rango-unidad').removeClass('d-none');
                $('#fecha-desde-unidad, #fecha-hasta-unidad').addClass('is-invalid');
                valido = false;
            }

            if (!valido) return;

            const rutas = {
                pdf:   '{{ route("permiso.pdf.generar.unidad") }}',
                excel: '{{ route("permiso.excel.generar.unidad") }}'
            };

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = rutas[tipo];
            form.target = '_blank';

            const fields = {
                _token:       '{{ csrf_token() }}',
                tipo_permiso: tipoPerm,
                id_unidad:    idUnidad,
                fecha_desde:  fechaDesde,
                fecha_hasta:  fechaHasta,
            };

            Object.entries(fields).forEach(([name, value]) => {
                const input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = name;
                input.value = value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }


        // Actualizar Bloque de Información General (Firmas)
        $('#form-info-general').on('submit', function (e) {
            e.preventDefault();

            const $btn = $('#btn-guardar-info');
            const textoOriginal = $btn.html();

            $('#form-info-general .is-invalid').removeClass('is-invalid');
            $('#form-info-general .invalid-feedback').remove();

            $btn.prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm mr-2"></span> Guardando...');

            const formData = {
                px_firmas:    $('#px_firmas').val(),
                salto_pagina: $('#salto_pagina').is(':checked') ? 1 : 0,
                jefe:         $('#jefe').val(),
                cargo:        $('#cargo').val(),
                area:         $('#area').val(),
            };

            axios.post('{{ route("permisos.infogeneral.actualizar") }}', formData)
                .then(function (response) {
                    if (response.data.success) {
                        toastr.success('Información actualizada correctamente.');
                    }
                })
                .catch(function (error) {
                    if (error.response && error.response.status === 422) {
                        const errores = error.response.data.errors;

                        Object.keys(errores).forEach(function (campo) {
                            const $input = $('#' + campo);
                            $input.addClass('is-invalid');
                            $input.after('<div class="invalid-feedback d-block">' + errores[campo][0] + '</div>');
                        });

                        toastr.error('Revisa los campos, hay errores de validación.');
                    } else {
                        toastr.error('Ocurrió un error al actualizar la información.');
                    }
                })
                .finally(function () {
                    $btn.prop('disabled', false).html(textoOriginal);
                });
        });

    </script>

@endsection
