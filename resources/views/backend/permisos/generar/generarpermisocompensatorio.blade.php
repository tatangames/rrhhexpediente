@extends('adminlte::page')

@section('title', 'Generar Permiso')

@section('content_header')
    <h1>Generar Permiso - Compensatorio</h1>
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

                <!-- COLUMNA IZQUIERDA: DATOS DEL PERMISO -->
                <div class="col-md-6">

                    <!-- Card para Otros -->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Datos del Permiso</h3>
                        </div>
                        <div class="card-body">

                            <!-- Fecha del Permiso -->
                            <div class="form-group col-md-4">
                                <label>Fecha: <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha-entrego">
                            </div>


                            <!-- Condición: Fraccionado o Completo -->
                            <div class="form-group">
                                <label>Condición del Permiso: <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px;">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="radio-fraccionado-4" name="condicion-otros" value="fraccionado" checked>
                                        <label for="radio-fraccionado-4" class="custom-control-label">
                                            <strong>Fraccionado</strong> (Por horas y/o minutos)
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="radio-completo-4" name="condicion-otros" value="completo">
                                        <label for="radio-completo-4" class="custom-control-label">
                                            <strong>Completo</strong> (Día(s) completo(s))
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Sección FRACCIONADO -->
                            <div id="seccion-fraccionado-4">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Fecha del Permiso: <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="fecha-solicitud-permiso">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Hora de Inicio: <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" id="hora-inicio-4">
                                            <small class="form-text text-muted">Hora exacta de inicio del permiso</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Hora de Fin: <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" id="hora-fin-4">
                                            <small class="form-text text-muted">Hora exacta de finalización del permiso</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Duración del Permiso (Tiempo Total):</label>
                                            <input type="text" class="form-control" id="horas-permiso-4" readonly>
                                            <!-- ✅ CAMPO OCULTO PARA GUARDAR LOS MINUTOS EXACTOS -->
                                            <input type="hidden" id="minutos-permiso-4">
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle text-info"></i>
                                                Se calcula automáticamente en horas y minutos
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección COMPLETO -->
                            <div id="seccion-completo-4" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha de Inicio: <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="fecha-inicio-comp-4">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha de Fin: <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="fecha-fin-comp-4">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Días Solicitados:</label>
                                            <input type="number" class="form-control" id="dias-solicitados-4" readonly>
                                            <small class="form-text text-muted">Se calcula automáticamente</small>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <hr>

                            <!-- Razón -->
                            <div class="form-group">
                                <label>Razón del Permiso:</label>
                                <textarea class="form-control" rows="3" maxlength="800" id="razon-permiso-4" placeholder="Describa brevemente el motivo del permiso"></textarea>
                            </div>

                            <!-- Nota informativa -->
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i>
                                <strong>Nota:</strong> Este permiso siempre es con goce de sueldo.
                            </div>


                            <!-- Botón Guardar -->
                            <div class="form-group mt-4">
                                <button type="button" class="btn btn-primary btn-block" id="btn-guardar">
                                    <i class="fas fa-save"></i> Guardar Permiso
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
                                    <i class="fas fa-info-circle"></i> Información de Permisos
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <div class="modal fade" id="modalInfoPermiso" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h4 class="modal-title text-white">
                        <i class="fas fa-info-circle"></i> Información de Permisos del Año <span id="info-anio"></span>
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <p><strong>Año:</strong> <span id="info-anio-text"></span></p>
                            <p><strong>Total de permisos compensatorios:</strong> <span id="info-total"></span></p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-secondary">
                            <h3 class="card-title">
                                <i class="fas fa-list"></i> Historial Detallado
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <ul id="info-fechas" class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                                <!-- Se llenará con JavaScript -->
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
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

                // Mostrar botón de información
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
            $(document).on('click', '#btn-informacion', function () {

                let empleadoId = $('#empleado-id').val();
                let fechaSeleccionada = $('#fecha-entrego').val();

                if (!empleadoId) {
                    toastr.error('No hay empleado seleccionado');
                    return;
                }

                if (!fechaSeleccionada) {
                    toastr.error('Debe seleccionar una fecha');
                    return;
                }

                openLoading();

                axios.post(urlAdmin + '/admin/empleados/infopermiso/compensatorio', {
                    empleado_id: empleadoId,
                    fecha: fechaSeleccionada
                })
                    .then(function (response) {
                        closeLoading();

                        if (!response.data || !response.data.success) {
                            toastr.error('No se pudo obtener la información');
                            return;
                        }

                        let data = response.data;

                        // 🔹 Año
                        $('#info-anio').text(data.anio || '');
                        $('#info-anio-text').text(data.anio || '');

                        // 🔹 Total
                        $('#info-total').text(data.total || 0);

                        // 🔹 Limpiar lista
                        $('#info-fechas').empty();

                        if (!data.permisos || data.permisos.length === 0) {
                            $('#info-fechas').append(`
                <li class="list-group-item text-center text-muted">
                    No hay permisos registrados en este año
                </li>
            `);
                        } else {
                            data.permisos.forEach(function (item) {

                                // Construir información de tiempo según el tipo
                                let tiempoHtml = '';

                                if (item.condicion == 0) {
                                    // Día completo
                                    tiempoHtml = `
                        <div class="mt-2">
                            <span class="badge badge-primary">
                                <i class="fas fa-calendar-day"></i> ${item.tipo}
                            </span>
                        </div>
                        ${item.fecha_inicio && item.fecha_fin ? `
                            <div class="mt-1">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt"></i>
                                    Desde: ${item.fecha_inicio} - Hasta: ${item.fecha_fin}
                                </small>
                            </div>
                        ` : ''}
                    `;
                                } else {
                                    // Fraccionado
                                    tiempoHtml = `
                        <div class="mt-2">
                            <span class="badge badge-warning">
                                <i class="fas fa-clock"></i> ${item.tipo}
                            </span>
                        </div>
                       ${item.hora_inicio && item.hora_fin ? `
                            <div class="mt-1">
                                <small class="text-muted">
                                    <i class="fas fa-hourglass-half"></i>
                                    De: ${item.hora_inicio} - A: ${item.hora_fin}
                                </small>
                            </div>

                            ${item.horas_texto ? `
                                    <div class="mt-1">
                                        <span class="badge badge-info">
                                            <i class="fas fa-clock"></i>
                                            Total: ${item.horas_texto}
                                        </span>
                                    </div>
                                ` : ''}
                            ` : ''}


                    `;
                                }

                                $('#info-fechas').append(`
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div>
                                    <strong><i class="fas fa-calendar"></i> Fecha:</strong> ${item.fecha || 'N/A'}
                                </div>
                                <div class="mt-1">
                                    <strong><i class="fas fa-comment"></i> Razón:</strong> ${item.razon || 'Sin descripción'}
                                </div>
                                ${tiempoHtml}
                            </div>
                        </div>
                    </li>
                `);
                            });
                        }

                        // 🔹 Mostrar modal
                        if ($('#modalInfoPermiso').length) {
                            $('#modalInfoPermiso').modal('show');
                        } else {
                            console.error('El modal #modalInfoPermiso no existe en el DOM');
                            toastr.error('Error al mostrar el modal');
                        }

                    })
                    .catch(function (error) {
                        closeLoading();
                        console.error('Error completo:', error);
                        toastr.error('Error al obtener información');
                    });

            });


            // ===============================
            // BOTÓN GUARDAR
            // ===============================
            $(document).on('click', '#btn-guardar', function () {

                let empleadoId = $('#empleado-id').val();
                let condicionTexto = $('input[name="condicion-otros"]:checked').val();
                let condicion = (condicionTexto === 'fraccionado') ? 1 : 0;
                let razon = $('#razon-permiso-4').val().trim();
                let fechaEntrego = $('#fecha-entrego').val();

                // ===============================
                // VALIDACIONES GENERALES
                // ===============================

                if (!fechaEntrego) {
                    toastr.error('Debe ingresar la fecha');
                    return;
                }

                if (!empleadoId) {
                    toastr.error('Debe seleccionar un empleado');
                    return;
                }

                if (razon.length > 800) {
                    toastr.error('La razón del permiso no puede exceder los 800 caracteres');
                    return;
                }

                // ===============================
                // CREAR OBJETO BASE PRIMERO
                // ===============================

                let datosPermiso = {
                    empleado_id: empleadoId,
                    condicion: condicion,
                    razon: razon || null,
                    fechaEntrego: fechaEntrego
                };

                // ===============================
                // VALIDAR SEGÚN CONDICIÓN
                // ===============================

                if (condicion === 1) { // fraccionado

                    let fechaPermiso = $('#fecha-solicitud-permiso').val();
                    let horaInicio = $('#hora-inicio-4').val();
                    let horaFin = $('#hora-fin-4').val();
                    let duracionMinutos = $('#minutos-permiso-4').val();

                    if (!fechaPermiso || !horaInicio || !horaFin) {
                        toastr.error('Complete todos los campos del permiso fraccionado');
                        return;
                    }

                    // ✅ Validar que la duración no esté vacía (indicaría 0 minutos)
                    if (!duracionMinutos || duracionMinutos == 0) {
                        toastr.error('El permiso debe ser de al menos 1 minuto');
                        return;
                    }

                    datosPermiso.fecha = fechaPermiso;
                    datosPermiso.hora_inicio = horaInicio;
                    datosPermiso.hora_fin = horaFin;
                    datosPermiso.duracion_minutos = parseInt(duracionMinutos);

                } else {

                    let fechaInicio = $('#fecha-inicio-comp-4').val();
                    let fechaFin = $('#fecha-fin-comp-4').val();
                    let dias = $('#dias-solicitados-4').val();

                    if (!fechaInicio || !fechaFin) {
                        toastr.error('Complete todos los campos del permiso completo');
                        return;
                    }

                    if (new Date(fechaFin) < new Date(fechaInicio)) {
                        toastr.error('La fecha fin no puede ser menor que la fecha inicio');
                        return;
                    }

                    datosPermiso.fecha_inicio = fechaInicio;
                    datosPermiso.fecha_fin = fechaFin;
                    datosPermiso.dias_solicitados = dias;
                }

                // ===============================
                // ENVIAR
                // ===============================

                openLoading();

                axios.post(urlAdmin + '/admin/guardar/permiso/compensatorio', datosPermiso)
                    .then(resp => {
                        toastr.success('Permiso guardado exitosamente');
                        limpiarFormulario();
                    })
                    .catch(err => {
                        toastr.error('Error al guardar el permiso');
                    })
                    .finally(() => {
                        closeLoading();
                    });
            });


            // ===============================
            // CAMBIAR ENTRE FRACCIONADO Y COMPLETO
            // ===============================
            $(document).on('change', 'input[name="condicion-otros"]', function() {
                if ($(this).val() === 'fraccionado') {
                    $('#seccion-fraccionado-4').slideDown(200);
                    $('#seccion-completo-4').slideUp(200);
                    // Limpiar campos de completo
                    $('#fecha-inicio-comp-4, #fecha-fin-comp-4, #dias-solicitados-4').val('');
                } else {
                    $('#seccion-completo-4').slideDown(200);
                    $('#seccion-fraccionado-4').slideUp(200);
                    // Limpiar campos de fraccionado
                    $('#fecha-solicitud-permiso, #hora-inicio-4, #hora-fin-4, #horas-permiso-4, #minutos-permiso-4').val('');
                }
            });

            // ===============================
            // CALCULAR DURACIÓN (FRACCIONADO) - AHORA EN MINUTOS
            // ===============================
            $(document).on('change', '#hora-inicio-4, #hora-fin-4', function() {

                let horaInicio = $('#hora-inicio-4').val();
                let horaFin = $('#hora-fin-4').val();

                if (horaInicio && horaFin) {

                    let [horaIni, minIni] = horaInicio.split(':');
                    let [horaFinH, minFin] = horaFin.split(':');

                    let inicio = new Date();
                    inicio.setHours(parseInt(horaIni), parseInt(minIni), 0, 0);

                    let fin = new Date();
                    fin.setHours(parseInt(horaFinH), parseInt(minFin), 0, 0);

                    let diferenciaMinutos = (fin - inicio) / (1000 * 60);

                    if (diferenciaMinutos > 0) {

                        // Guardar minutos totales en campo oculto
                        $('#minutos-permiso-4').val(diferenciaMinutos);

                        // Mostrar formato legible
                        let horas = Math.floor(diferenciaMinutos / 60);
                        let minutos = diferenciaMinutos % 60;

                        let textoMostrar = '';
                        if (horas > 0) {
                            textoMostrar += horas + (horas === 1 ? ' hora' : ' horas');
                        }
                        if (minutos > 0) {
                            if (textoMostrar) textoMostrar += ' y ';
                            textoMostrar += minutos + ' minutos';
                        }

                        $('#horas-permiso-4').val(textoMostrar);

                    } else {
                        $('#horas-permiso-4').val('');
                        $('#minutos-permiso-4').val('');
                        toastr.error('La hora de fin debe ser mayor a la hora de inicio');
                    }
                }
            });


            // ===============================
            // CALCULAR DÍAS Y FECHA REINGRESO (COMPLETO)
            // ===============================
            $(document).on('change', '#fecha-inicio-comp-4, #fecha-fin-comp-4', function() {
                let fechaInicio = $('#fecha-inicio-comp-4').val();
                let fechaFin = $('#fecha-fin-comp-4').val();

                if (fechaInicio && fechaFin) {
                    let inicio = new Date(fechaInicio);
                    let fin = new Date(fechaFin);

                    // Calcular días
                    let diferencia = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24)) + 1;
                    $('#dias-solicitados-4').val(diferencia);

                    // Calcular fecha de reingreso (día siguiente al fin)
                    let reingreso = new Date(fin);
                    reingreso.setDate(reingreso.getDate() + 1);
                }
            });
        });


        function limpiarFormulario() {

            // Limpiar empleado
            $('#input-buscar-empleado').val('');
            $('#empleado-id').val('');
            $('#input-unidad').val('');
            $('#input-cargo').val('');
            $('#bloque-btn-info').hide();

            // Limpiar razón
            $('#razon-permiso-4').val('');

            // Resetear a fraccionado
            $('#radio-fraccionado-4').prop('checked', true);
            $('#seccion-fraccionado-4').show();
            $('#seccion-completo-4').hide();

            // Limpiar campos fraccionado
            $('#fecha-solicitud-permiso, #hora-inicio-4, #hora-fin-4, #horas-permiso-4, #minutos-permiso-4').val('');

            // Limpiar campos completo
            $('#fecha-inicio-comp-4, #fecha-fin-comp-4, #dias-solicitados-4').val('');
        }
    </script>

@endsection
