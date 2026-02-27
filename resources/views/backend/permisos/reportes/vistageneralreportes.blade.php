@extends('adminlte::page')

@section('title', 'Reportes de Permisos')

@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">

    <section class="content" style="margin-top: 15px">
        <div class="container-fluid">
            <div class="row justify-content-center">

                <div class="col-md-7">
                    <div class="card card-secondary shadow">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-pdf mr-2"></i>Generar Reporte de Permisos
                            </h3>
                        </div>

                        <div class="card-body">

                            <!-- Empleado -->
                            <div class="form-group">
                                <label>Empleado: <span style="color:red">*</span></label>
                                <select class="form-control" id="select-empleado">
                                    <option value="">-- Seleccione un empleado --</option>
                                    @foreach($arrayEmpleados as $item)
                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger d-none" id="error-empleado">
                                    Debe seleccionar un empleado.
                                </small>
                            </div>

                            <!-- Tipo de Permiso -->
                            <div class="form-group">
                                <label>Tipo de Permiso: <span style="color:red">*</span></label>
                                <select class="form-control" id="select-tipopermiso">
                                    <option value="1">Personal</option>
                                    <option value="2">Compensatorio</option>
                                    <option value="3">Enfermedad</option>
                                    <option value="4">Consulta Médica</option>
                                    <option value="5">Incapacidad</option>
                                    <option value="6">Otros</option>
                                </select>
                            </div>

                            <div class="row">
                                <!-- Fecha Desde -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Desde: <span style="color:red">*</span></label>
                                        <input type="date" class="form-control" id="fecha-desde">
                                        <small class="text-danger d-none" id="error-desde">
                                            La fecha de inicio es requerida.
                                        </small>
                                    </div>
                                </div>

                                <!-- Fecha Hasta -->
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

                            <!-- Error de rango de fechas -->
                            <small class="text-danger d-none" id="error-rango">
                                La fecha "Desde" no puede ser mayor que la fecha "Hasta".
                            </small>

                        </div>

                        <div class="card-footer d-flex gap-2">
                            <button type="button" onclick="generarReporte('pdf')"
                                    class="btn btn-outline-danger d-flex align-items-center" id="btn-pdf">
                                <img src="{{ asset('images/logopdf.png') }}" width="28" height="28"
                                     style="margin-right:8px;">
                                Generar PDF
                            </button>

                            <button style="margin-left: 15px" type="button" onclick="generarReporte('excel')"
                                    class="btn btn-outline-success d-flex align-items-center" id="btn-excel">
                                <img src="{{ asset('images/logoexcel.png') }}" width="28" height="28"
                                     style="margin-right:8px;">
                                Generar Excel
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

@stop

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>

        $('#select-empleado').select2({
            theme: "bootstrap-5",
            language: { noResults: () => "Búsqueda no encontrada" }
        });

        $('#select-tipopermiso').select2({
            theme: "bootstrap-5",
            minimumResultsForSearch: Infinity
        });

        // Limpiar errores al cambiar campos
        $('#select-empleado').on('change', function () {
            $('#error-empleado').addClass('d-none');
        });

        $('#fecha-desde').on('change', function () {
            $('#error-desde, #error-rango').addClass('d-none');
            $(this).removeClass('is-invalid');
        });

        $('#fecha-hasta').on('change', function () {
            $('#error-hasta, #error-rango').addClass('d-none');
            $(this).removeClass('is-invalid');
        });

        // ──────────────────────────────────────────────────
        //  Validación y generación del reporte (PDF / Excel)
        // ──────────────────────────────────────────────────
        function generarReporte(tipo) {
            const idEmpleado = $('#select-empleado').val();
            const tipoPerm   = $('#select-tipopermiso').val();
            const fechaDesde = $('#fecha-desde').val();
            const fechaHasta = $('#fecha-hasta').val();

            let valido = true;

            // Limpiar estado anterior
            $('#error-empleado, #error-desde, #error-hasta, #error-rango').addClass('d-none');
            $('#fecha-desde, #fecha-hasta').removeClass('is-invalid');

            // Validar empleado
            if (!idEmpleado) {
                $('#error-empleado').removeClass('d-none');
                valido = false;
            }

            // Validar fecha desde
            if (!fechaDesde) {
                $('#error-desde').removeClass('d-none');
                $('#fecha-desde').addClass('is-invalid');
                valido = false;
            }

            // Validar fecha hasta
            if (!fechaHasta) {
                $('#error-hasta').removeClass('d-none');
                $('#fecha-hasta').addClass('is-invalid');
                valido = false;
            }

            // Validar rango: desde <= hasta
            if (fechaDesde && fechaHasta && fechaDesde > fechaHasta) {
                $('#error-rango').removeClass('d-none');
                $('#fecha-desde, #fecha-hasta').addClass('is-invalid');
                valido = false;
            }

            if (!valido) return;

            // Definir la ruta según el tipo
            const rutas = {
                pdf:   '{{ route("permiso.pdf.generar") }}',
                excel: '{{ route("permiso.excel.generar") }}'
            };

            // Construir formulario dinámico POST → nueva pestaña
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
    </script>
@endsection
