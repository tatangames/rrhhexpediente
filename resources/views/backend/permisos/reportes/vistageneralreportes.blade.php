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
                                    <option value="">-- Todos los empleados --</option>
                                    @foreach($arrayEmpleados as $item)
                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
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
                                        <label>Desde:</label>
                                        <input type="date" class="form-control" id="fecha-desde">
                                    </div>
                                </div>

                                <!-- Fecha Hasta -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Hasta:</label>
                                        <input type="date" class="form-control" id="fecha-hasta">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer">
                            <div style="display:flex; gap:12px;">

                                <!-- Botón PDF -->
                                <button type="button" onclick="generarPdf()"
                                        class="btn btn-outline-danger d-flex align-items-center" id="btn-pdf">
                                    <img src="{{ asset('images/logopdf.png') }}" width="28" height="28"
                                         style="margin-right:8px;">
                                    Generar PDF
                                </button>

                            </div>
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
        // Inicializar Select2
        $('#select-empleado').select2({
            theme: "bootstrap-5",
            language: {
                noResults: () => "Búsqueda no encontrada"
            }
        });

        $('#select-tipopermiso').select2({
            theme: "bootstrap-5",
            minimumResultsForSearch: Infinity
        });

        // ──────────────────────────────────────────────────
        //  Generar PDF: abre en nueva pestaña via POST form
        // ──────────────────────────────────────────────────
        function generarPdf() {
            const idEmpleado  = $('#select-empleado').val();
            const tipoPerm    = $('#select-tipopermiso').val();
            const fechaDesde  = $('#fecha-desde').val();
            const fechaHasta  = $('#fecha-hasta').val();

            // Validación mínima
            if (!tipoPerm) {
                toastr.warning('Seleccione el tipo de permiso.');
                return;
            }

            // Construir formulario dinámico y hacer POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("permiso.pdf.generar") }}';
            form.target = '_blank';   // Nueva pestaña

            const fields = {
                _token:        '{{ csrf_token() }}',
                tipo_permiso:  tipoPerm,
                id_empleado:   idEmpleado,
                fecha_desde:   fechaDesde,
                fecha_hasta:   fechaHasta,
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
