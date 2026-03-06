@extends('adminlte::page')

@section('title', 'Generar Permiso')

@section('content_header')
    <h1>Generar Permiso - Consulta Medica</h1>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content_top_nav_right')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />

    <li class="nav-item dropdown">
        <a href="#" class="nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
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
                                            <strong>Fraccionado</strong> (Por horas/minutos)
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
                                            <small class="form-text text-muted">Se calcula automáticamente</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Razón -->
                            <div class="form-group">
                                <label>Razón del Permiso:</label>
                                <textarea class="form-control" rows="3" maxlength="800" id="razon-permiso-4"
                                          placeholder="Describa brevemente el motivo del permiso"></textarea>
                            </div>

                            <!-- Condición Médica -->
                            <div class="form-group">
                                <label>Condición Medica:</label>
                                <input class="form-control" type="text" maxlength="800" id="condicion-medica" placeholder="">
                            </div>

                            <!-- Unidad de atención -->
                            <div class="form-group">
                                <label>Unidad de atención:</label>
                                <input class="form-control" type="text" maxlength="800" id="unidad-atencion" placeholder="">
                            </div>

                            <!-- Especialidad -->
                            <div class="form-group">
                                <label>Especialidad:</label>
                                <input class="form-control" type="text" maxlength="100" id="especialidad" placeholder="">
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
                                    <i class="fas fa-info-circle"></i> Información
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- MODAL: Información de Permisos                               --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalInfoPermiso" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Información de Permisos</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Año:</strong> <span id="info-anio"></span></p>
                    <p><strong>Total permisos:</strong> <span id="info-total"></span></p>
                    <hr>
                    <ul id="info-fechas" class="list-group"></ul>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: Permisos Duplicados                                   --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalDuplicados" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-warning">
                    <h4 class="modal-title text-dark">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Permisos Duplicados Encontrados
                    </h4>
                </div>

                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Se encontraron los siguientes permisos ya registrados para este empleado que
                        <strong>coinciden</strong> con los datos ingresados:
                    </p>
                    <ul id="lista-duplicados" class="list-group mb-3"></ul>
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-question-circle mr-1"></i>
                        <strong>¿Desea guardar el permiso de todas formas?</strong>
                        <br>
                        <small>Al confirmar, el permiso quedará registrado aunque ya existan entradas similares.</small>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" id="btn-cancelar-duplicado">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-forzar-guardado">
                        <i class="fas fa-save mr-1"></i> Guardar de todas formas
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

        // ===============================
        // HELPER: Y-m-d → d-m-Y (solo para mostrar)
        // ===============================
        function formatearFecha(fecha) {
            if (!fecha) return '-';
            let partes = fecha.split('-');
            if (partes.length !== 3) return fecha;
            if (partes[0].length === 4) {
                return partes[2] + '-' + partes[1] + '-' + partes[0];
            }
            return fecha;
        }

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
                $('#bloque-btn-info').fadeIn(200);
            });

            // ===============================
            // OCULTAR LISTA SI CLIC AFUERA
            // ===============================
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#input-buscar-empleado, #lista-empleados').length) {
                    $('#lista-empleados').hide();
                }
            });

            // ===============================
            // BOTÓN INFORMACIÓN
            // ===============================
            $(document).on('click', '#btn-informacion', function () {
                let empleadoId = $('#empleado-id').val();
                let fecha      = $('#fecha-entrego').val();

                if (!fecha) {
                    toastr.error('Fecha es requerido');
                    return;
                }

                if (!empleadoId) {
                    toastr.error('No hay empleado seleccionado');
                    return;
                }

                openLoading();

                axios.post(urlAdmin + '/admin/empleados/infopermiso/consultamedica', {
                    empleado_id: empleadoId,
                    fecha: fecha
                })
                    .then(resp => {
                        if (resp.data.success) {

                            $('#info-anio').text(resp.data.anio);
                            $('#info-total').text(resp.data.total);

                            let lista = '';

                            if (!resp.data.permisos || resp.data.permisos.length === 0) {
                                lista = `
                                    <li class="list-group-item text-center text-muted">
                                        No hay permisos registrados en este año
                                    </li>
                                `;
                            } else {
                                resp.data.permisos.forEach(function (item) {

                                    let tiempoHtml = '';

                                    if (item.condicion == 0) {
                                        tiempoHtml = `
                                            <div class="mt-2">
                                                <span class="badge badge-primary">
                                                    <i class="fas fa-calendar-day"></i> ${item.tipo}
                                                </span>
                                            </div>
                                            ${item.fecha_inicio && item.fecha_fin ? `
                                                <div class="mt-1">
                                                    <small class="text-muted">
                                                        Desde: ${item.fecha_inicio} - Hasta: ${item.fecha_fin}
                                                    </small>
                                                </div>
                                            ` : ''}
                                        `;
                                    } else {
                                        tiempoHtml = `
                                            <div class="mt-2">
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> ${item.tipo}
                                                </span>
                                            </div>
                                            ${item.hora_inicio && item.hora_fin ? `
                                                <div class="mt-1">
                                                    <small class="text-muted">
                                                        De: ${item.hora_inicio} - A: ${item.hora_fin}
                                                    </small>
                                                </div>
                                            ` : ''}
                                            ${item.horas_texto ? `
                                                <div class="mt-1">
                                                    <span class="badge badge-info">
                                                        Total: ${item.horas_texto}
                                                    </span>
                                                </div>
                                            ` : ''}
                                        `;
                                    }

                                    lista += `
                                        <li class="list-group-item">
                                            <strong>Fecha:</strong> ${item.fecha}<br>
                                            <strong>Razón:</strong> ${item.razon ?? 'Sin descripción'}
                                            ${tiempoHtml}
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
            // CAMBIAR ENTRE FRACCIONADO Y COMPLETO
            // ===============================
            $(document).on('change', 'input[name="condicion-otros"]', function () {
                if ($(this).val() === 'fraccionado') {
                    $('#seccion-fraccionado-4').slideDown(200);
                    $('#seccion-completo-4').slideUp(200);
                    $('#fecha-inicio-comp-4, #fecha-fin-comp-4, #dias-solicitados-4').val('');
                } else {
                    $('#seccion-completo-4').slideDown(200);
                    $('#seccion-fraccionado-4').slideUp(200);
                    $('#fecha-solicitud-permiso, #hora-inicio-4, #hora-fin-4, #horas-permiso-4').val('');
                }
            });

            // ===============================
            // CALCULAR DURACIÓN (FRACCIONADO)
            // ===============================
            $(document).on('change', '#hora-inicio-4, #hora-fin-4', function () {
                let horaInicio = $('#hora-inicio-4').val();
                let horaFin    = $('#hora-fin-4').val();

                if (horaInicio && horaFin) {
                    let [hIni, mIni] = horaInicio.split(':');
                    let [hFin, mFin] = horaFin.split(':');

                    let inicio = new Date();
                    inicio.setHours(parseInt(hIni), parseInt(mIni), 0, 0);

                    let fin = new Date();
                    fin.setHours(parseInt(hFin), parseInt(mFin), 0, 0);

                    let diferenciaMs = fin - inicio;

                    if (diferenciaMs > 0) {
                        let totalMinutos = diferenciaMs / (1000 * 60);
                        let horas   = Math.floor(totalMinutos / 60);
                        let minutos = totalMinutos % 60;

                        let texto = '';
                        if (horas > 0)   texto += horas + (horas === 1 ? ' hora' : ' horas');
                        if (minutos > 0) texto += (texto !== '' ? ' ' : '') + minutos + (minutos === 1 ? ' minuto' : ' minutos');

                        $('#horas-permiso-4').val(texto);
                    } else {
                        $('#horas-permiso-4').val('');
                        toastr.error('La hora de fin debe ser mayor a la hora de inicio');
                    }
                }
            });

            // ===============================
            // CALCULAR DÍAS (COMPLETO)
            // ===============================
            $(document).on('change', '#fecha-inicio-comp-4, #fecha-fin-comp-4', function () {
                let fechaInicio = $('#fecha-inicio-comp-4').val();
                let fechaFin    = $('#fecha-fin-comp-4').val();

                if (fechaInicio && fechaFin) {
                    let inicio     = new Date(fechaInicio);
                    let fin        = new Date(fechaFin);
                    let diferencia = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24)) + 1;
                    $('#dias-solicitados-4').val(diferencia);
                }
            });

            // ===============================
            // BOTÓN GUARDAR
            // ===============================
            $(document).on('click', '#btn-guardar', function () {
                enviarPermiso(false);
            });

            // ===============================
            // CANCELAR MODAL — limpiar payload
            // ===============================
            $(document).on('click', '#btn-cancelar-duplicado', function () {
                $('#btn-forzar-guardado').removeData('payload');
                $('#modalDuplicados').modal('hide');
            });

            // Limpiar si el modal se cierra por cualquier medio
            $('#modalDuplicados').on('hidden.bs.modal', function () {
                $('#btn-forzar-guardado').removeData('payload');
                $('#lista-duplicados').html('');
            });

            // ===============================
            // FORZAR GUARDADO DESDE MODAL
            // ===============================
            $(document).on('click', '#btn-forzar-guardado', function () {

                let payload = $(this).data('payload');

                if (!payload) return;

                payload.forzar_guardado = true;

                $('#btn-forzar-guardado').removeData('payload');
                $('#modalDuplicados').modal('hide');

                openLoading();

                axios.post(urlAdmin + '/admin/guardar/permiso/consultamedica', payload)
                    .then(resp => {
                        if (resp.data.success === 1) {
                            toastr.success('Permiso guardado exitosamente');
                            limpiarFormulario();
                        } else {
                            toastr.error(resp.data.message || 'Error al guardar el permiso');
                        }
                    })
                    .catch(() => {
                        toastr.error('Error al guardar el permiso');
                    })
                    .finally(() => {
                        closeLoading();
                    });
            });

        }); // fin document ready


        // ===============================
        // FUNCIÓN: ARMAR Y ENVIAR DATOS
        // ===============================
        function enviarPermiso(forzar) {

            let empleadoId      = $('#empleado-id').val();
            let condicionTexto  = $('input[name="condicion-otros"]:checked').val();
            let condicion       = (condicionTexto === 'fraccionado') ? 1 : 0;
            let razon           = $('#razon-permiso-4').val().trim();
            let fechaEntrego    = $('#fecha-entrego').val();
            let unidadAtencion  = $('#unidad-atencion').val();
            let especialidad    = $('#especialidad').val();
            let condicionMedica = $('#condicion-medica').val();

            // --- Validaciones generales ---
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

            // --- Objeto base ---
            let datosPermiso = {
                empleado_id:     empleadoId,
                condicion:       condicion,
                razon:           razon || null,
                fechaEntrego:    fechaEntrego,
                forzar_guardado: forzar,
                unidadAtencion:  unidadAtencion,
                especialidad:    especialidad,
                condicionMedica: condicionMedica
            };

            // --- Validar según condición ---
            if (condicion === 1) {

                let fechaFraccionado = $('#fecha-solicitud-permiso').val();
                let horaInicio       = $('#hora-inicio-4').val();
                let horaFin          = $('#hora-fin-4').val();
                let duracion         = $('#horas-permiso-4').val();

                if (!fechaFraccionado || !horaInicio || !horaFin) {
                    toastr.error('Complete todos los campos del permiso fraccionado');
                    return;
                }

                datosPermiso.fecha_fraccionado = fechaFraccionado;
                datosPermiso.hora_inicio       = horaInicio;
                datosPermiso.hora_fin          = horaFin;
                datosPermiso.duracion          = duracion;

            } else {

                let fechaInicio = $('#fecha-inicio-comp-4').val();
                let fechaFin    = $('#fecha-fin-comp-4').val();
                let dias        = $('#dias-solicitados-4').val();

                if (!fechaInicio || !fechaFin) {
                    toastr.error('Complete todos los campos del permiso completo');
                    return;
                }

                if (new Date(fechaFin) < new Date(fechaInicio)) {
                    toastr.error('La fecha fin no puede ser menor que la fecha inicio');
                    return;
                }

                datosPermiso.fecha_inicio     = fechaInicio;
                datosPermiso.fecha_fin        = fechaFin;
                datosPermiso.dias_solicitados = dias;
            }

            // --- Enviar ---
            openLoading();

            axios.post(urlAdmin + '/admin/guardar/permiso/consultamedica', datosPermiso)
                .then(resp => {

                    if (resp.data.success === 1) {

                        toastr.success('Permiso guardado exitosamente');
                        limpiarFormulario();

                    } else if (resp.data.success === 2) {

                        let html = '';

                        resp.data.duplicados.forEach(function (d) {

                            let tiempoHtml = '';

                            if (d.condicion === 'Fraccionado') {
                                tiempoHtml = `
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock"></i> Fraccionado
                                    </span>
                                    <small class="d-block text-muted mt-1">
                                        Fecha: ${formatearFecha(d.fecha_fraccionado)}
                                        &nbsp;|&nbsp;
                                        De: ${d.hora_inicio ?? '-'} &nbsp;-&nbsp; A: ${d.hora_fin ?? '-'}
                                    </small>
                                `;
                            } else {
                                tiempoHtml = `
                                    <span class="badge badge-primary">
                                        <i class="fas fa-calendar-day"></i> Completo
                                    </span>
                                    <small class="d-block text-muted mt-1">
                                        Desde: ${formatearFecha(d.fecha_inicio)}
                                        &nbsp;|&nbsp;
                                        Hasta: ${formatearFecha(d.fecha_fin)}
                                    </small>
                                `;
                            }

                            html += `
                                <li class="list-group-item">
                                    <div>
                                        <strong>Fecha entregó:</strong> ${formatearFecha(d.fecha)}<br>
                                        <strong>Razón:</strong> ${d.razon}
                                        <div class="mt-2">${tiempoHtml}</div>
                                    </div>
                                </li>
                            `;
                        });

                        $('#lista-duplicados').html(html);
                        $('#btn-forzar-guardado').data('payload', datosPermiso);
                        $('#modalDuplicados').modal('show');

                    } else {
                        toastr.error(resp.data.message || 'Error al guardar el permiso');
                    }
                })
                .catch(() => {
                    toastr.error('Error al guardar el permiso');
                })
                .finally(() => {
                    closeLoading();
                });
        }


        // ===============================
        // LIMPIAR FORMULARIO
        // ===============================
        function limpiarFormulario() {

            $('#input-buscar-empleado').val('');
            $('#empleado-id').val('');
            $('#input-unidad').val('');
            $('#input-cargo').val('');
            $('#bloque-btn-info').hide();

            $('#razon-permiso-4').val('');
            $('#unidad-atencion').val('');
            $('#especialidad').val('');
            $('#condicion-medica').val('');

            $('#radio-fraccionado-4').prop('checked', true);
            $('#seccion-fraccionado-4').show();
            $('#seccion-completo-4').hide();

            $('#fecha-solicitud-permiso, #hora-inicio-4, #hora-fin-4, #horas-permiso-4').val('');
            $('#fecha-inicio-comp-4, #fecha-fin-comp-4, #dias-solicitados-4').val('');

            $('#btn-forzar-guardado').removeData('payload');
        }
    </script>

@endsection
