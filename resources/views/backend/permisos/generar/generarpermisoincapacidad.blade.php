@extends('adminlte::page')

@section('title', 'Generar Permiso')

@section('content_header')
    <h1>Generar Permiso - Incapacidad</h1>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />

    <style>
        #lista-empleados .item-compacto {
            padding: 6px 10px !important;
            font-size: 14px;
            line-height: 1.2;
        }
    </style>

    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <!-- COLUMNA IZQUIERDA: DATOS DE INCAPACIDAD -->
                <div class="col-md-6">

                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Datos de la Incapacidad</h3>
                        </div>
                        <div class="card-body">

                            <!-- Fecha -->
                            <div class="form-group col-md-4">
                                <label>Fecha: <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha-incapacidad">
                            </div>

                            <!-- Tipo de Incapacidad -->
                            <div class="form-group">
                                <label>Tipo de Incapacidad: <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="tipo-incapacidad" style="width: 100%;">
                                    <option value="">Seleccione...</option>
                                    @foreach($arrayTipoIncapacidad as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Riesgo -->
                            <div class="form-group">
                                <label>Riesgo: <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="riesgo" style="width: 100%;">
                                    <option value="">Seleccione...</option>
                                    @foreach($arrayRiesgo as $riesgo)
                                        <option value="{{ $riesgo->id }}">{{ $riesgo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <hr>

                            <!-- Fecha de Inicio de Incapacidad -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha Inicio Incapacidad: <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="fecha-inicio-incapacidad">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Días de Incapacidad: <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="dias-incapacidad" min="1" placeholder="Ingrese días">
                                    </div>
                                </div>
                            </div>

                            <!-- Fecha Fin (calculada automáticamente) -->
                            <div class="form-group">
                                <label>Fecha Fin Incapacidad:</label>
                                <input type="date" class="form-control" id="fecha-fin-incapacidad" readonly>
                                <small class="form-text text-muted">Se calcula automáticamente</small>
                            </div>

                            <hr>

                            <!-- Diagnóstico -->
                            <div class="form-group">
                                <label>Diagnóstico: </label>
                                <textarea class="form-control" rows="3" maxlength="500" id="diagnostico" placeholder="Ingrese el diagnóstico"></textarea>
                            </div>

                            <!-- Número -->
                            <div class="form-group">
                                <label>Número: </label>
                                <input type="text" class="form-control" id="numero" placeholder="Ingrese el número de incapacidad">
                            </div>



                            <hr>

                            <!-- Hospitalización -->
                            <div class="form-group">
                                <label>Hospitalización:</label>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="hospitalizacion">
                                    <label for="hospitalizacion" class="custom-control-label">
                                        ¿Requirió hospitalización?
                                    </label>
                                </div>
                            </div>

                            <!-- Período de Hospitalización (solo si se marca el checkbox) -->
                            <div id="periodo-hospitalizacion-section" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha Inicio Hospitalización:</label>
                                            <input type="date" class="form-control" id="fecha-inicio-hospitalizacion">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha Fin Hospitalización:</label>
                                            <input type="date" class="form-control" id="fecha-fin-hospitalizacion">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón Guardar -->
                            <div class="form-group mt-4">
                                <button type="button" class="btn btn-primary btn-block" id="btn-guardar">
                                    <i class="fas fa-save"></i> Guardar Incapacidad
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- COLUMNA DERECHA: DATOS DEL EMPLEADO -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Datos del Empleado</h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group" style="position:relative;">
                                <label>Buscar empleado por nombre: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="input-buscar-empleado"
                                       placeholder="Escriba el nombre del empleado">
                                <input type="hidden" id="empleado-id">
                                <div id="lista-empleados"
                                     class="list-group mt-1"
                                     style="display:none; position:absolute; z-index:1000; max-height:300px; overflow-y:auto; width:100%;">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Unidad</label>
                                <input type="text" class="form-control" id="input-unidad" readonly>
                            </div>

                            <div class="form-group">
                                <label>Cargo</label>
                                <input type="text" class="form-control" id="input-cargo" readonly>
                            </div>

                            <div class="form-group mt-4" id="bloque-btn-info" style="display:none;">
                                <button type="button" class="btn btn-info btn-block" id="btn-informacion">
                                    <i class="fas fa-info-circle"></i> Información
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Modal Información -->
    <div class="modal fade" id="modalInfoPermiso" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Información de Incapacidades</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Año:</strong> <span id="info-anio"></span></p>
                    <p><strong>Total incapacidades:</strong> <span id="info-total"></span></p>
                    <hr>
                    <ul id="info-fechas" class="list-group"></ul>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')

    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        $(function () {

            // Inicializar Select2
            $('.select2').select2({
                theme: 'bootstrap-5'
            });

            // ===============================
            // BUSCAR EMPLEADO
            // ===============================
            $('#input-buscar-empleado').on('keyup', function () {
                let texto = $(this).val();

                if (texto.length < 2) {
                    $('#lista-empleados').hide().html('');
                    $('#empleado-id').val('');
                    $('#input-unidad').val('');
                    $('#input-cargo').val('');
                    $('#bloque-btn-info').hide();
                    return;
                }

                axios.get(urlAdmin + '/admin/empleados/buscar', {
                    params: { q: texto }
                }).then(resp => {
                    let html = '';
                    resp.data.forEach(e => {
                        html += `
                    <button type="button"
                        class="list-group-item list-group-item-action empleado-item item-compacto"
                        data-id="${e.id}"
                        data-unidad="${e.unidad}"
                        data-cargo="${e.cargo}"
                        data-nombre="${e.nombre}">
                        ${e.nombre}
                    </button>
                `;
                    });

                    $('#lista-empleados').html(html).show();
                }).catch(err => {
                    console.error('Error al buscar empleados:', err);
                });
            });

            // ===============================
            // SELECCIONAR EMPLEADO
            // ===============================
            $(document).on('click', '.empleado-item', function () {
                $('#empleado-id').val($(this).data('id'));
                $('#input-buscar-empleado').val($(this).data('nombre'));
                $('#input-unidad').val($(this).data('unidad'));
                $('#input-cargo').val($(this).data('cargo'));
                $('#lista-empleados').hide();
                $('#bloque-btn-info').fadeIn(200);
            });

            // ===============================
            // OCULTAR LISTA SI CLIC AFUERA
            // ===============================
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#input-buscar-empleado, #lista-empleados').length) {
                    $('#lista-empleados').hide();
                }
            });

            // ===============================
            // BOTÓN INFORMACIÓN
            // ===============================
            $(document).on('click', '#btn-informacion', function() {

                let empleadoId = $('#empleado-id').val();
                let fecha = $('#fecha-incapacidad').val();

                if (!fecha) {
                    toastr.error('Fecha es requerido');
                    return;
                }

                if (!empleadoId) {
                    toastr.error('No hay empleado seleccionado');
                    return;
                }

                openLoading();

                axios.post(urlAdmin + '/admin/empleados/infopermiso/incapacidad', {
                    empleado_id: empleadoId
                })
                    .then(resp => {

                        if (resp.data.success) {

                            $('#info-anio').text(resp.data.anio);
                            $('#info-total').text(resp.data.total);

                            let lista = '';

                            if (resp.data.incapacidades.length === 0) {
                                lista = `
                                <li class="list-group-item text-center text-muted">
                                    No hay incapacidades registradas este año
                                </li>
                            `;
                            } else {

                                resp.data.incapacidades.forEach(function(item) {

                                    lista += `
                                <li class="list-group-item">
                                    <strong>Desde:</strong> ${item.fecha_inicio}<br>
                                    <strong>Hasta:</strong> ${item.fecha_fin}<br>
                                    <strong>Días:</strong> ${item.dias}<br>
                                    <strong>Diagnóstico:</strong> ${item.diagnostico ?? 'Sin diagnóstico'}
                                </li>
                            `;
                                });
                            }

                            $('#info-fechas').html(lista);
                            $('#modalInfoPermiso').modal('show');
                        }
                    })
                    .catch(() => {
                        toastr.error('Error al obtener la información del empleado');
                    })
                    .finally(() => {
                        closeLoading();
                    });
            });

            // ===============================
            // CALCULAR FECHA FIN INCAPACIDAD
            // ===============================
            $(document).on('change', '#fecha-inicio-incapacidad, #dias-incapacidad', function() {

                let fechaInicio = $('#fecha-inicio-incapacidad').val();
                let diasInput = $('#dias-incapacidad').val();

                // 👉 Si no hay días escritos, NO hacer nada ni mostrar toast
                if (!diasInput) {
                    $('#fecha-fin-incapacidad').val('');
                    return;
                }

                let dias = parseInt(diasInput);

                // Validar que los días sean positivos SOLO si el usuario escribió algo
                if (isNaN(dias) || dias <= 0) {
                    toastr.error('Los días de incapacidad deben ser mayor a 0');
                    $('#dias-incapacidad').val('');
                    $('#fecha-fin-incapacidad').val('');
                    return;
                }

                if (fechaInicio && dias > 0) {

                    let [year, month, day] = fechaInicio.split('-');
                    let inicio = new Date(year, month - 1, day);

                    let fechaFin = new Date(inicio);
                    fechaFin.setDate(fechaFin.getDate() + dias - 1);

                    let yearFin = fechaFin.getFullYear();
                    let monthFin = String(fechaFin.getMonth() + 1).padStart(2, '0');
                    let dayFin = String(fechaFin.getDate()).padStart(2, '0');

                    $('#fecha-fin-incapacidad').val(`${yearFin}-${monthFin}-${dayFin}`);
                }
            });


            // ===============================
            // VALIDAR INPUT DE DÍAS (en tiempo real)
            // ===============================
            $(document).on('input', '#dias-incapacidad', function() {
                let valor = $(this).val();

                // Eliminar caracteres no numéricos excepto números
                valor = valor.replace(/[^0-9]/g, '');

                // Si hay valor, convertir a número y validar
                if (valor !== '') {
                    let numero = parseInt(valor);

                    // Si es 0 o negativo, mostrar error y limpiar
                    if (numero === 0) {
                        toastr.error('Los días de incapacidad no pueden ser 0');
                        $(this).val('');
                        $('#fecha-fin-incapacidad').val('');
                    }
                }

                // Actualizar el valor limpio
                $(this).val(valor);
            });

            // ===============================
            // MOSTRAR/OCULTAR PERÍODO HOSPITALIZACIÓN
            // ===============================
            $(document).on('change', '#hospitalizacion', function() {
                if ($(this).is(':checked')) {
                    $('#periodo-hospitalizacion-section').slideDown(200);
                } else {
                    $('#periodo-hospitalizacion-section').slideUp(200);
                    $('#fecha-inicio-hospitalizacion, #fecha-fin-hospitalizacion').val('');
                }
            });

            // ===============================
            // BOTÓN GUARDAR
            // ===============================
            $(document).on('click', '#btn-guardar', function () {

                let empleadoId = $('#empleado-id').val();
                let fecha = $('#fecha-incapacidad').val();
                let tipoIncapacidad = $('#tipo-incapacidad').val();
                let riesgo = $('#riesgo').val();
                let fechaInicio = $('#fecha-inicio-incapacidad').val();
                let dias = $('#dias-incapacidad').val();
                let fechaFin = $('#fecha-fin-incapacidad').val();
                let diagnostico = $('#diagnostico').val().trim();
                let numero = $('#numero').val().trim();
                let hospitalizacion = $('#hospitalizacion').is(':checked') ? 1 : 0;
                let fechaInicioHosp = $('#fecha-inicio-hospitalizacion').val();
                let fechaFinHosp = $('#fecha-fin-hospitalizacion').val();

                // VALIDACIONES
                if (!empleadoId) {
                    toastr.error('Debe seleccionar un empleado');
                    return;
                }

                if (!fecha) {
                    toastr.error('Debe ingresar la fecha');
                    return;
                }

                if (!tipoIncapacidad) {
                    toastr.error('Debe seleccionar el tipo de incapacidad');
                    return;
                }

                if (!riesgo) {
                    toastr.error('Debe seleccionar el riesgo');
                    return;
                }

                if (!fechaInicio) {
                    toastr.error('Debe ingresar la fecha de inicio de incapacidad');
                    return;
                }

                if (!dias || dias < 1) {
                    toastr.error('Debe ingresar los días de incapacidad');
                    return;
                }

                if (hospitalizacion === 1) {
                    if (!fechaInicioHosp || !fechaFinHosp) {
                        toastr.error('Debe completar las fechas de hospitalización');
                        return;
                    }

                    if (new Date(fechaFinHosp) < new Date(fechaInicioHosp)) {
                        toastr.error('La fecha fin de hospitalización no puede ser menor que la fecha inicio');
                        return;
                    }
                }

                // CREAR OBJETO DE DATOS
                let datosIncapacidad = {
                    empleado_id: empleadoId,
                    fecha: fecha,
                    tipo_incapacidad_id: tipoIncapacidad,
                    riesgo_id: riesgo,
                    fecha_inicio: fechaInicio,
                    dias: dias,
                    fecha_fin: fechaFin,
                    diagnostico: diagnostico,
                    numero: numero,
                    hospitalizacion: hospitalizacion,
                    fecha_inicio_hospitalizacion: fechaInicioHosp || null,
                    fecha_fin_hospitalizacion: fechaFinHosp || null
                };

                // ENVIAR
                openLoading();

                axios.post(urlAdmin + '/admin/guardar/permiso/incapacidad', datosIncapacidad)
                    .then(resp => {
                        if (resp.data.success === 1) {
                            toastr.success('Incapacidad guardada exitosamente');
                            limpiarFormulario();
                        } else {
                            toastr.error('Error al guardar la incapacidad');
                        }
                    })
                    .catch(err => {
                        toastr.error('Error al guardar la incapacidad');
                    })
                    .finally(() => {
                        closeLoading();
                    });
            });

        });

        // ===============================
        // LIMPIAR FORMULARIO
        // ===============================
        function limpiarFormulario() {
            // Limpiar empleado
            $('#input-buscar-empleado').val('');
            $('#empleado-id').val('');
            $('#input-unidad').val('');
            $('#input-cargo').val('');
            $('#bloque-btn-info').hide();

            // Limpiar campos de incapacidad
            $('#fecha-incapacidad').val('');
            $('#tipo-incapacidad').val('').trigger('change');
            $('#riesgo').val('').trigger('change');
            $('#fecha-inicio-incapacidad').val('');
            $('#dias-incapacidad').val('');
            $('#fecha-fin-incapacidad').val('');
            $('#diagnostico').val('');
            $('#numero').val('');
            $('#hospitalizacion').prop('checked', false);
            $('#periodo-hospitalizacion-section').hide();
            $('#fecha-inicio-hospitalizacion').val('');
            $('#fecha-fin-hospitalizacion').val('');
        }
    </script>

@endsection
