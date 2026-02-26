@extends('adminlte::page')

@section('title', 'Historial Permisos - Incapacidad')

@section('content_header')
    <h1>Historial Permisos - Incapacidad</h1>
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

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6"></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Permisos</li>
                    <li class="breadcrumb-item active">Listado de Incapacidad</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Listado</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="tablaDatatable"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== MODAL EDITAR ===================== -->
    <div class="modal fade" id="modalEditar" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Editar Incapacidad</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="formulario-editar">
                        <input type="hidden" id="id-editar">

                        <!-- Fecha -->
                        <div class="form-group col-md-4 px-0">
                            <label>Fecha: <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit-fecha">
                        </div>

                        <!-- Tipo de Incapacidad -->
                        <div class="form-group">
                            <label>Tipo de Incapacidad: <span class="text-danger">*</span></label>
                            <select class="form-control select2-modal" id="edit-tipo-incapacidad" style="width:100%;">
                                <option value="">Seleccione...</option>
                                @foreach($arrayTipoIncapacidad as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Riesgo -->
                        <div class="form-group">
                            <label>Riesgo: <span class="text-danger">*</span></label>
                            <select class="form-control select2-modal" id="edit-riesgo" style="width:100%;">
                                <option value="">Seleccione...</option>
                                @foreach($arrayRiesgo as $riesgo)
                                    <option value="{{ $riesgo->id }}">{{ $riesgo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr>

                        <!-- Fecha Inicio + Días + Fecha Fin -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Inicio Incapacidad: <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit-fecha-inicio">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Días de Incapacidad: <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="edit-dias" min="1"
                                           placeholder="Ingrese días">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Fecha Fin Incapacidad:</label>
                            <input type="date" class="form-control" id="edit-fecha-fin" readonly>
                            <small class="form-text text-muted">Se calcula automáticamente</small>
                        </div>

                        <hr>

                        <!-- Diagnóstico -->
                        <div class="form-group">
                            <label>Diagnóstico:</label>
                            <textarea class="form-control" rows="3" maxlength="500" id="edit-diagnostico"
                                      placeholder="Ingrese el diagnóstico"></textarea>
                        </div>

                        <!-- Número -->
                        <div class="form-group">
                            <label>Número:</label>
                            <input type="text" class="form-control" id="edit-numero"
                                   placeholder="Ingrese el número de incapacidad">
                        </div>

                        <hr>

                        <!-- Hospitalización -->
                        <div class="form-group">
                            <label>Hospitalización:</label>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="edit-hospitalizacion">
                                <label for="edit-hospitalizacion" class="custom-control-label">
                                    ¿Requirió hospitalización?
                                </label>
                            </div>
                        </div>

                        <!-- Fechas Hospitalización -->
                        <div id="edit-periodo-hospitalizacion-section" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha Inicio Hospitalización:</label>
                                        <input type="date" class="form-control" id="edit-fecha-inicio-hosp">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha Fin Hospitalización:</label>
                                        <input type="date" class="form-control" id="edit-fecha-fin-hosp">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Empleado -->
                        <div class="form-group">
                            <label>Empleado: <span class="text-danger">*</span></label>
                            <input type="hidden" id="edit-empleado-id">

                            <!-- Empleado actual (readonly) -->
                            <div class="input-group" id="edit-empleado-actual">
                                <input type="text" class="form-control" id="edit-empleado-nombre" readonly
                                       placeholder="Cargando empleado...">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-warning" id="btn-cambiar-empleado">
                                        <i class="fas fa-exchange-alt"></i> Cambiar
                                    </button>
                                </div>
                            </div>

                            <!-- Buscador (oculto por defecto) -->
                            <div id="edit-bloque-buscar" style="display:none; position:relative;">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit-buscar-empleado"
                                           placeholder="Escriba el nombre del empleado...">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-secondary" id="btn-cancelar-busqueda">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div id="edit-lista-empleados"
                                     class="list-group mt-1"
                                     style="display:none; position:absolute; z-index:9999; max-height:220px; overflow-y:auto; width:100%;">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Unidad</label>
                                    <input type="text" class="form-control" id="edit-unidad" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cargo</label>
                                    <input type="text" class="form-control" id="edit-cargo" readonly>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="editar()">
                        <i class="fas fa-save"></i> Actualizar
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
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>

    <script>
        $(function () {
            const ruta = "{{ url('/admin/historial/incapacidad/tabla') }}";

            // ===============================
            // SELECT2 — inicializar UNA SOLA VEZ
            // ===============================
            $('.select2-modal').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalEditar')
            });

            // ===============================
            // DATATABLE
            // ===============================
            function initDataTable() {
                if ($.fn.DataTable.isDataTable('#tabla')) {
                    $('#tabla').DataTable().destroy();
                }
                $('#tabla').DataTable({
                    paging: true,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    responsive: true,
                    pagingType: "full_numbers",
                    lengthMenu: [[100, 150, -1], [100, 150, "Todo"]],
                    language: {
                        sProcessing: "Procesando...",
                        sLengthMenu: "Mostrar _MENU_ registros",
                        sZeroRecords: "No se encontraron resultados",
                        sEmptyTable: "Ningún dato disponible en esta tabla",
                        sInfo: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        sInfoEmpty: "Mostrando 0 a 0 de 0 registros",
                        sInfoFiltered: "(filtrado de _MAX_ registros)",
                        sSearch: "Buscar:",
                        oPaginate: {
                            sFirst: "Primero",
                            sLast: "Último",
                            sNext: "Siguiente",
                            sPrevious: "Anterior"
                        },
                    },
                    dom:
                        "<'row align-items-center'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-md-right'f>>" +
                        "tr" +
                        "<'row align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
                });
                $('#tabla_length select').addClass('form-control form-control-sm');
                $('#tabla_filter input').addClass('form-control form-control-sm').css('display', 'inline-block');
            }

            function cargarTabla() {
                $('#tablaDatatable').load(ruta, function () {
                    initDataTable();
                });
            }

            cargarTabla();

            window.recargar = function () {
                cargarTabla();
            };
        });

        // ===============================
        // RECARGAR TABLA
        // ===============================
        function recargar() {
            $('#tablaDatatable').load("{{ url('/admin/historial/incapacidad/tabla') }}");
        }

        // ===============================
        // BORRAR
        // ===============================
        function informacionBorrar(id) {
            Swal.fire({
                title: 'Borrar Registro',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                allowOutsideClick: false,
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) borrarRegistro(id);
            });
        }

        function borrarRegistro(id) {
            openLoading();
            let formData = new FormData();
            formData.append('id', id);

            axios.post(urlAdmin + '/admin/historial/incapacidad/borrar', formData)
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Borrado correctamente');
                        recargar();
                    } else {
                        toastr.error('Error al borrar');
                    }
                })
                .catch(() => {
                    closeLoading();
                    toastr.error('Error al borrar');
                });
        }

        // ===============================
        // CALCULAR FECHA FIN AUTOMÁTICA
        // ===============================
        $(document).on('change input', '#edit-fecha-inicio, #edit-dias', function () {
            let fechaInicio = $('#edit-fecha-inicio').val();
            let diasInput   = $('#edit-dias').val();

            if (!fechaInicio || !diasInput) {
                $('#edit-fecha-fin').val('');
                return;
            }

            let dias = parseInt(diasInput);
            if (isNaN(dias) || dias <= 0) {
                $('#edit-fecha-fin').val('');
                return;
            }

            let [year, month, day] = fechaInicio.split('-');
            let inicio   = new Date(year, month - 1, day);
            let fechaFin = new Date(inicio);
            fechaFin.setDate(fechaFin.getDate() + dias - 1);

            $('#edit-fecha-fin').val(
                fechaFin.getFullYear() + '-' +
                String(fechaFin.getMonth() + 1).padStart(2, '0') + '-' +
                String(fechaFin.getDate()).padStart(2, '0')
            );
        });

        // Validar que días no sea 0
        $(document).on('input', '#edit-dias', function () {
            let valor = $(this).val().replace(/[^0-9]/g, '');
            if (valor !== '' && parseInt(valor) === 0) {
                toastr.error('Los días no pueden ser 0');
                $(this).val('');
                $('#edit-fecha-fin').val('');
            } else {
                $(this).val(valor);
            }
        });

        // ===============================
        // HOSPITALIZACIÓN
        // ===============================
        $(document).on('change', '#edit-hospitalizacion', function () {
            if ($(this).is(':checked')) {
                $('#edit-periodo-hospitalizacion-section').slideDown(200);
            } else {
                $('#edit-periodo-hospitalizacion-section').slideUp(200);
                $('#edit-fecha-inicio-hosp, #edit-fecha-fin-hosp').val('');
            }
        });

        // ===============================
        // ABRIR MODAL: CARGAR DATOS
        // ===============================
        function informacion(id) {
            openLoading();

            document.getElementById('formulario-editar').reset();
            $('#edit-periodo-hospitalizacion-section').hide();
            $('#edit-bloque-buscar').hide();
            $('#edit-lista-empleados').hide();
            $('#edit-empleado-actual').show();

            // Limpiar select2 también
            $('#edit-tipo-incapacidad').val('').trigger('change');
            $('#edit-riesgo').val('').trigger('change');

            axios.post(urlAdmin + '/admin/historial/incapacidad/informacion', {id: id})
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {
                        const info = response.data.info;

                        // Datos principales
                        $('#id-editar').val(info.id);
                        $('#edit-fecha').val(info.fecha);
                        $('#edit-fecha-inicio').val(info.fecha_inicio);
                        $('#edit-dias').val(info.dias);
                        $('#edit-fecha-fin').val(info.fecha_fin);
                        $('#edit-diagnostico').val(info.diagnostico ?? '');
                        $('#edit-numero').val(info.numero ?? '');

                        // Select2 — asignar ANTES de abrir el modal
                        $('#edit-tipo-incapacidad').val(info.id_tipo_incapacidad).trigger('change');
                        $('#edit-riesgo').val(info.id_riesgo).trigger('change');

                        // Hospitalización
                        const tieneHosp = parseInt(info.hospitalizacion) === 1;
                        $('#edit-hospitalizacion').prop('checked', tieneHosp);
                        if (tieneHosp) {
                            $('#edit-periodo-hospitalizacion-section').show();
                            $('#edit-fecha-inicio-hosp').val(info.fecha_inicio_hospitalizacion ?? '');
                            $('#edit-fecha-fin-hosp').val(info.fecha_fin_hospitalizacion ?? '');
                        }

                        // Empleado
                        $('#edit-empleado-id').val(info.id_empleado);
                        $('#edit-empleado-nombre').val(info.nombre_empleado ?? '');
                        $('#edit-unidad').val(info.unidad ?? '');
                        $('#edit-cargo').val(info.cargo ?? '');

                        // Abrir modal AL FINAL
                        $('#modalEditar').modal('show');

                    } else {
                        toastr.error('Información no encontrada');
                    }
                })
                .catch(() => {
                    closeLoading();
                    toastr.error('Error al obtener información');
                });
        }

        // ===============================
        // GUARDAR EDICIÓN
        // ===============================
        function editar() {
            const id              = $('#id-editar').val();
            const empleadoId      = $('#edit-empleado-id').val();
            const fecha           = $('#edit-fecha').val();
            const tipoIncapacidad = $('#edit-tipo-incapacidad').val();
            const riesgo          = $('#edit-riesgo').val();
            const fechaInicio     = $('#edit-fecha-inicio').val();
            const dias            = $('#edit-dias').val();
            const fechaFin        = $('#edit-fecha-fin').val();
            const diagnostico     = $('#edit-diagnostico').val().trim();
            const numero          = $('#edit-numero').val().trim();
            const hospitalizacion = $('#edit-hospitalizacion').is(':checked') ? 1 : 0;
            const fechaInicioHosp = $('#edit-fecha-inicio-hosp').val();
            const fechaFinHosp    = $('#edit-fecha-fin-hosp').val();

            // Validaciones
            if (!empleadoId)       { toastr.error('Debe seleccionar un empleado');            return; }
            if (!fecha)            { toastr.error('Debe ingresar la fecha');                   return; }
            if (!tipoIncapacidad)  { toastr.error('Debe seleccionar el tipo de incapacidad');  return; }
            if (!riesgo)           { toastr.error('Debe seleccionar el riesgo');               return; }
            if (!fechaInicio)      { toastr.error('Debe ingresar la fecha de inicio');         return; }
            if (!dias || dias < 1) { toastr.error('Debe ingresar los días de incapacidad');    return; }

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

            openLoading();

            const formData = new FormData();
            formData.append('id',                           id);
            formData.append('empleado_id',                  empleadoId);
            formData.append('fecha',                        fecha);
            formData.append('tipocondicion',                tipoIncapacidad);
            formData.append('riesgo',                       riesgo);
            formData.append('fecha_inicio',                 fechaInicio);
            formData.append('dias',                         dias);
            formData.append('fecha_fin',                    fechaFin);
            formData.append('diagnostico',                  diagnostico);
            formData.append('numero',                       numero);
            formData.append('hospitalizacion',              hospitalizacion);
            formData.append('fecha_inicio_hospitalizacion', fechaInicioHosp || '');
            formData.append('fecha_fin_hospitalizacion',    fechaFinHosp    || '');

            axios.post(urlAdmin + '/admin/historial/incapacidad/actualizar', formData)
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
                    } else {
                        toastr.error(response.data.message ?? 'Error al actualizar');
                    }
                })
                .catch(() => {
                    closeLoading();
                    toastr.error('Error al actualizar');
                });
        }

        // ===============================
        // CAMBIAR / BUSCAR EMPLEADO
        // ===============================
        $(document).on('click', '#btn-cambiar-empleado', function () {
            $('#edit-empleado-actual').hide();
            $('#edit-bloque-buscar').show();
            $('#edit-buscar-empleado').val('').focus();
            $('#edit-lista-empleados').hide().html('');
        });

        $(document).on('click', '#btn-cancelar-busqueda', function () {
            $('#edit-bloque-buscar').hide();
            $('#edit-empleado-actual').show();
            $('#edit-lista-empleados').hide().html('');
        });

        $(document).on('keyup', '#edit-buscar-empleado', function () {
            let texto = $(this).val();

            if (texto.length < 2) {
                $('#edit-lista-empleados').hide().html('');
                return;
            }

            if ($(this).data('buscando')) return;
            const $input = $(this);
            $input.data('buscando', true);

            axios.get(urlAdmin + '/admin/empleados/buscar', {params: {q: texto}})
                .then(resp => {
                    let html = '';
                    resp.data.forEach(e => {
                        html += `
                            <button type="button"
                                class="list-group-item list-group-item-action edit-empleado-item"
                                data-id="${e.id}"
                                data-unidad="${e.unidad}"
                                data-cargo="${e.cargo}"
                                data-nombre="${e.nombre}">
                                <strong>${e.nombre}</strong>
                                <small class="text-muted d-block">${e.cargo ?? ''} — ${e.unidad ?? ''}</small>
                            </button>`;
                    });
                    $('#edit-lista-empleados').html(html).show();
                })
                .catch(() => toastr.error('Error al buscar empleados'))
                .finally(() => $input.data('buscando', false));
        });

        $(document).on('click', '.edit-empleado-item', function () {
            $('#edit-empleado-id').val($(this).data('id'));
            $('#edit-empleado-nombre').val($(this).data('nombre'));
            $('#edit-unidad').val($(this).data('unidad'));
            $('#edit-cargo').val($(this).data('cargo'));
            $('#edit-lista-empleados').hide();
            $('#edit-bloque-buscar').hide();
            $('#edit-empleado-actual').show();
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('#edit-buscar-empleado, #edit-lista-empleados').length) {
                $('#edit-lista-empleados').hide();
            }
        });

    </script>

    <script>
        (function () {
            const SERVER_DEFAULT = {{ $temaPredeterminado }};
            const iconEl = document.getElementById('theme-icon');

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

            function applyTheme(mode) {
                const dark = mode === 'dark';
                document.body.classList.toggle('dark-mode', dark);
                document.documentElement.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
                if (iconEl) {
                    iconEl.classList.remove('fa-sun', 'fa-moon');
                    iconEl.classList.add(dark ? 'fa-moon' : 'fa-sun');
                }
            }

            applyTheme(SERVER_DEFAULT === 1 ? 'dark' : 'light');

            let saving = false;
            document.addEventListener('click', async (e) => {
                const a = e.target.closest('.dropdown-item[data-theme]');
                if (!a || saving) return;
                e.preventDefault();

                const selectedMode = a.dataset.theme;
                const previousMode = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
                applyTheme(selectedMode);

                try {
                    saving = true;
                    await axios.post(urlAdmin + '/admin/actualizar/tema', {tema: selectedMode === 'dark' ? 1 : 0});
                    if (window.toastr) toastr.success('Tema actualizado');
                } catch {
                    applyTheme(previousMode);
                    if (window.toastr) toastr.error('No se pudo actualizar el tema');
                } finally {
                    saving = false;
                }
            });
        })();
    </script>

@endsection
