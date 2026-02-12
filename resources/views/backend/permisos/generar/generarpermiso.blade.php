@extends('adminlte::page')

@section('title', 'Generar Permiso')

@section('content_header')
    <h1>Generar Permiso</h1>
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

                <!-- PRIMER CARD: TIPO DE PERMISO -->
                <div class="col-md-6">
                    <div class="card card-blue">
                        <div class="card-header">
                            <h3 class="card-title">Tipo de Permiso</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Tipo de Permiso: <span class="text-danger">*</span></label>
                                <select class="form-control" id="select-tipopermiso">
                                    <option value="">Seleccionar</option>
                                    @foreach($arrayTipoPermiso as $item)
                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mt-4">
                                <button type="button" class="btn btn-primary btn-block" id="btn-guardar">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- CARD DINÁMICO SEGÚN TIPO DE PERMISO -->
                    <div id="bloque-tipo-permiso" style="display:none;">

                        <!-- Card para Permiso Personal (ID: 1) -->
                        <div class="card card-warning card-tipo-permiso" id="card-permiso-1" style="display:none;">
                            <div class="card-header">
                                <h3 class="card-title">Permiso Personal</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Motivo Personal</label>
                                    <textarea class="form-control" rows="3" placeholder="Ingrese el motivo"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Fecha</label>
                                    <input type="date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Card para Consulta Médica (ID: 2) -->
                        <div class="card card-success card-tipo-permiso" id="card-permiso-2" style="display:none;">
                            <div class="card-header">
                                <h3 class="card-title">Consulta Médica</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Centro Médico</label>
                                    <input type="text" class="form-control" placeholder="Nombre del centro médico">
                                </div>
                                <div class="form-group">
                                    <label>Fecha y Hora de Cita</label>
                                    <input type="datetime-local" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Consulta</label>
                                    <select class="form-control">
                                        <option value="">Seleccionar</option>
                                        <option value="general">General</option>
                                        <option value="especialista">Especialista</option>
                                        <option value="emergencia">Emergencia</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Card para Incapacidad (ID: 3) -->
                        <div class="card card-danger card-tipo-permiso" id="card-permiso-3" style="display:none;">
                            <div class="card-header">
                                <h3 class="card-title">Incapacidad</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Diagnóstico</label>
                                    <input type="text" class="form-control" placeholder="Diagnóstico médico">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha Inicio</label>
                                            <input type="date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha Fin</label>
                                            <input type="date" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Adjuntar Certificado Médico</label>
                                    <input type="file" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Card para Otros (ID: 4) -->
                        <div class="card card-secondary card-tipo-permiso" id="card-permiso-4" style="display:none;">
                            <div class="card-header">
                                <h3 class="card-title">Otros Permisos</h3>
                            </div>
                            <div class="card-body">



                                <!-- Campo Otros (aparece solo si selecciona "Otro") -->
                                <div class="form-group" id="grupo-especificar-otro" style="display:none;">
                                    <label>Especificar: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="input-especificar-otro" placeholder="Especifique el tipo de permiso">
                                </div>

                                <!-- Condición: Fraccionado o Completo -->
                                <div class="form-group">
                                    <label>Condición del Permiso: <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center" style="gap: 20px;">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="radio-fraccionado-4" name="condicion-otros" value="fraccionado" checked>
                                            <label for="radio-fraccionado-4" class="custom-control-label">
                                                <strong>Fraccionado</strong> (Por horas)
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
                                                <input type="date" class="form-control" id="fecha-permiso-frac-4">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Hora de Inicio: <span class="text-danger">*</span></label>
                                                <input type="time" class="form-control" id="hora-inicio-4">
                                                <small class="form-text text-muted">Desde qué hora necesita el permiso</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Hora de Fin: <span class="text-danger">*</span></label>
                                                <input type="time" class="form-control" id="hora-fin-4">
                                                <small class="form-text text-muted">Hasta qué hora necesita el permiso</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Duración del Permiso:</label>
                                                <input type="text" class="form-control" id="horas-permiso-4" readonly>
                                                <small class="form-text text-muted">Se calcula automáticamente</small>
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
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Fecha de Reingreso:</label>
                                                <input type="date" class="form-control" id="fecha-reingreso-comp-4" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Razón -->
                                <div class="form-group">
                                    <label>Razón del Permiso: <span class="text-danger">*</span></label>
                                    <textarea class="form-control" rows="3" id="razon-permiso-4" placeholder="Describa brevemente el motivo del permiso"></textarea>
                                </div>

                                <!-- Nota informativa -->
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Nota:</strong> Este permiso siempre es con goce de sueldo.
                                </div>

                            </div>
                        </div>

                        <!-- Card para Permiso Compensatorio (ID: 5) -->
                        <div class="card card-info card-tipo-permiso" id="card-permiso-5" style="display:none;">
                            <div class="card-header">
                                <h3 class="card-title">Permiso Compensatorio</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Horas Extras Acumuladas</label>
                                    <input type="number" class="form-control" placeholder="Cantidad de horas" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Horas a Compensar</label>
                                    <input type="number" class="form-control" placeholder="Horas que desea compensar">
                                </div>
                                <div class="form-group">
                                    <label>Fecha de Compensación</label>
                                    <input type="date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Card para Permiso por Enfermedad (ID: 6) -->
                        <div class="card card-purple card-tipo-permiso" id="card-permiso-6" style="display:none;">
                            <div class="card-header">
                                <h3 class="card-title">Permiso por Enfermedad</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Síntomas</label>
                                    <textarea class="form-control" rows="3" placeholder="Describa los síntomas"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Días de Reposo</label>
                                    <input type="number" class="form-control" placeholder="Cantidad de días">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha Inicio</label>
                                            <input type="date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha Fin</label>
                                            <input type="date" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- SEGUNDO CARD: EMPLEADO (OCULTO INICIALMENTE) -->
                <div class="col-md-6" id="bloque-empleado" style="display:none;">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Datos del Empleado</h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label>Fecha del Permiso</label>
                                <input type="date" class="form-control" id="fecha-permiso" >
                            </div>


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

@stop

@section('js')

    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        $(function () {

            // ===============================
            // MOSTRAR / OCULTAR BLOQUE EMPLEADO Y CARD DINÁMICO
            // ===============================
            $('#select-tipopermiso').on('change', function () {

                let tipoPermisoId = $(this).val();

                if (tipoPermisoId) {
                    // Mostrar bloque de empleado
                    $('#bloque-empleado').fadeIn(200);

                    // Ocultar todos los cards de tipo permiso
                    $('.card-tipo-permiso').hide();

                    // Mostrar el card correspondiente al tipo seleccionado
                    $('#card-permiso-' + tipoPermisoId).fadeIn(200);
                    $('#bloque-tipo-permiso').fadeIn(200);

                } else {
                    $('#bloque-empleado').fadeOut(200);
                    $('#bloque-tipo-permiso').fadeOut(200);
                    $('.card-tipo-permiso').hide();

                    // limpiar solo cuando quitan el tipo
                    limpiarEmpleado();
                }
            });

            // ===============================
            // FUNCIÓN PARA LIMPIAR EMPLEADO
            // ===============================
            function limpiarEmpleado() {
                $('#input-buscar-empleado').val('');
                $('#empleado-id').val('');
                $('#input-unidad').val('');
                $('#input-cargo').val('');
                $('#lista-empleados').hide().html('');
                $('#bloque-btn-info').hide();
            }

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
            $(document).on('click', '#btn-informacion', function() {
                let empleadoId = $('#empleado-id').val();
                let tipoPermisoId = $('#select-tipopermiso').val();
                let fechaPermiso = $('#fecha-permiso').val();

                if (!fechaPermiso) {
                    toastr.error('Fecha del permiso es requerido');
                    return;
                }

                if (!tipoPermisoId) {
                    toastr.error('Debe seleccionar un tipo de permiso');
                    return;
                }

                if (!empleadoId) {
                    toastr.error('No hay empleado seleccionado');
                    return;
                }


                openLoading()

                // Petición API para obtener información del empleado
                axios.post(urlAdmin + '/admin/empleados/infopermiso', {
                    empleado_id: empleadoId,
                    tipo_permiso_id: tipoPermisoId,
                    fecha_permiso: fechaPermiso
                })
                    .then(resp => {
                        console.log('Información del empleado:', resp.data);
                        toastr.success('Información obtenida (ver consola)');
                    })
                    .catch(err => {
                        console.error('Error al obtener información:', err);
                        toastr.error('Error al obtener la información del empleado');
                    })
                    .finally(() => {
                        closeLoading();
                    });
            });

            // ===============================
            // BOTÓN GUARDAR
            // ===============================
            $(document).on('click', '#btn-guardar', function() {

                let tipoPermisoId = $('#select-tipopermiso').val();
                let empleadoId = $('#empleado-id').val();

                if (!tipoPermisoId) {
                    toastr.error('Debe seleccionar un tipo de permiso');
                    return;
                }

                if (!empleadoId) {
                    toastr.error('Debe seleccionar un empleado');
                    return;
                }

                console.log('ID del empleado seleccionado:', empleadoId);
                console.log('ID del tipo de permiso:', tipoPermisoId);

                toastr.success('Datos validados correctamente');
            });

        });
    </script>

    <script>

        // ===============================
        // LÓGICA PARA PERMISO "OTROS" (ID: 4)
        // ===============================

        // Mostrar/ocultar campo "Especificar" según selección
        $(document).on('change', '#select-tipo-otros', function() {
            if ($(this).val() === 'otro') {
                $('#grupo-especificar-otro').slideDown(200);
            } else {
                $('#grupo-especificar-otro').slideUp(200);
                $('#input-especificar-otro').val('');
            }
        });

        // Cambiar entre Fraccionado y Completo
        $(document).on('change', 'input[name="condicion-otros"]', function() {
            if ($(this).val() === 'fraccionado') {
                $('#seccion-fraccionado-4').slideDown(200);
                $('#seccion-completo-4').slideUp(200);
                // Limpiar campos de completo
                $('#fecha-inicio-comp-4, #fecha-fin-comp-4, #dias-solicitados-4, #fecha-reingreso-comp-4').val('');
            } else {
                $('#seccion-completo-4').slideDown(200);
                $('#seccion-fraccionado-4').slideUp(200);
                // Limpiar campos de fraccionado
                $('#fecha-permiso-frac-4, #horas-permiso-4, #hora-salida-4, #hora-reingreso-4').val('');
            }
        });

        // Calcular hora de reingreso automáticamente (Fraccionado)
        $(document).on('change', '#horas-permiso-4, #hora-salida-4', function() {
            let horas = parseInt($('#horas-permiso-4').val()) || 0;
            let horaSalida = $('#hora-salida-4').val();

            if (horas > 0 && horaSalida) {
                let [hora, minuto] = horaSalida.split(':');
                let fecha = new Date();
                fecha.setHours(parseInt(hora));
                fecha.setMinutes(parseInt(minuto));

                // Sumar las horas
                fecha.setHours(fecha.getHours() + horas);

                let horaReingreso = String(fecha.getHours()).padStart(2, '0') + ':' + String(fecha.getMinutes()).padStart(2, '0');
                $('#hora-reingreso-4').val(horaReingreso);
            }
        });

        // Calcular días y fecha de reingreso (Completo)
        // Calcular tiempo de permiso automáticamente (Fraccionado)
        // Calcular duración de permiso automáticamente (Fraccionado)
        $(document).on('change', '#hora-inicio-4, #hora-fin-4', function() {
            let horaInicio = $('#hora-inicio-4').val();
            let horaFin = $('#hora-fin-4').val();

            if (horaInicio && horaFin) {
                let [horaIni, minIni] = horaInicio.split(':');
                let [horaFinH, minFin] = horaFin.split(':');

                let inicio = new Date();
                inicio.setHours(parseInt(horaIni), parseInt(minIni), 0);

                let fin = new Date();
                fin.setHours(parseInt(horaFinH), parseInt(minFin), 0);

                // Calcular diferencia en minutos
                let diferencia = (fin - inicio) / (1000 * 60);

                if (diferencia > 0) {
                    let horas = Math.floor(diferencia / 60);
                    let minutos = diferencia % 60;

                    let texto = '';
                    if (horas > 0) texto += horas + (horas === 1 ? ' hora' : ' horas');
                    if (minutos > 0) texto += (horas > 0 ? ' y ' : '') + minutos + ' minutos';

                    $('#horas-permiso-4').val(texto);
                } else {
                    $('#horas-permiso-4').val('');
                    toastr.error('La hora de fin debe ser mayor a la hora de inicio');
                }
            }
        });


    </script>

@endsection
