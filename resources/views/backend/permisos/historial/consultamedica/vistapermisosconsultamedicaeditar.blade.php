@extends('adminlte::page')

@section('title', 'Historial Permisos - Consulta Medica')

@section('content_header')
    <h1>Historial Permisos - Consulta Medica</h1>
@stop
{{-- Activa plugins que necesitas --}}
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

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">

            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Permisos</li>
                    <li class="breadcrumb-item active">Listado de Consulta Medica</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-blue">
                <div class="card-header">
                    <h3 class="card-title">Listado</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="tablaDatatable">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- modal editar -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Permiso</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-editar">
                        <input type="hidden" id="id-editar">

                        <div class="row">

                            <!-- Fecha Entregó -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fecha: <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit-fechaEntrego">
                                </div>
                            </div>

                            <!-- Condición radios -->
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Condición: <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center" style="gap:20px; margin-top:5px;">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="edit-radio-fraccionado"
                                                   name="edit-condicion" value="1">
                                            <label for="edit-radio-fraccionado" class="custom-control-label">
                                                <strong>Fraccionado</strong> (Por horas)
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="edit-radio-completo"
                                                   name="edit-condicion" value="0">
                                            <label for="edit-radio-completo" class="custom-control-label">
                                                <strong>Completo</strong> (Día(s) completo(s))
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <hr>

                        <!-- Sección FRACCIONADO -->
                        <div id="edit-seccion-fraccionado" style="display:none;">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Fecha del Permiso: <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="edit-fecha-solicitud">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Hora Inicio: <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="edit-hora-inicio">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Hora Fin: <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="edit-hora-fin">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Duración:</label>
                                        <input type="text" class="form-control" id="edit-duracion" readonly
                                               placeholder="Se calcula automático">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección COMPLETO -->
                        <div id="edit-seccion-completo" style="display:none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha Inicio: <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="edit-fecha-inicio">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha Fin: <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="edit-fecha-fin">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Días Solicitados:</label>
                                        <input type="number" class="form-control" id="edit-dias" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Empleado -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Empleado: <span class="text-danger">*</span></label>
                                    <input type="hidden" id="edit-empleado-id">

                                    <!-- Muestra el empleado actual (readonly) -->
                                    <div class="input-group" id="edit-empleado-actual">
                                        <input type="text" class="form-control" id="edit-empleado-nombre" readonly
                                               placeholder="Cargando empleado...">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-warning" id="btn-cambiar-empleado"
                                                    title="Cambiar empleado">
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
                                                <button type="button" class="btn btn-secondary" id="btn-cancelar-busqueda"
                                                        title="Cancelar">
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
                            </div>

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

                        <hr>

                        <!-- Razón -->
                        <div class="form-group">
                            <label>Razón del Permiso:</label>
                            <textarea class="form-control" rows="3" maxlength="800" id="edit-razon"></textarea>
                        </div>

                        <!-- Unidad atención -->
                        <div class="form-group">
                            <label>Unidad atención:</label>
                            <input class="form-control" maxlength="800" id="edit-unidadatencion">
                        </div>

                        <!-- especialidad -->
                        <div class="form-group">
                            <label>Especialidad:</label>
                            <input class="form-control" maxlength="500" id="edit-especialidad">
                        </div>

                        <!-- condicion medica -->
                        <div class="form-group">
                            <label>Condición médica:</label>
                            <textarea class="form-control" rows="3" maxlength="800" id="edit-condicionmedica"></textarea>
                        </div>

                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success btn-sm" onclick="editar()">Actualizar</button>
                </div>
            </div>
        </div>
    </div>


@stop



@section('js')
    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}" type="text/javascript"></script>

    <script>
        $(function () {
            const ruta = "{{ url('/admin/historial/consultamedica/tabla') }}";

            function initDataTable() {
                // Si ya hay instancia, destrúyela antes de re-crear
                if ($.fn.DataTable.isDataTable('#tabla')) {
                    $('#tabla').DataTable().destroy();
                }

                // Inicializa
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
                        oPaginate: {sFirst: "Primero", sLast: "Último", sNext: "Siguiente", sPrevious: "Anterior"},
                        oAria: {sSortAscending: ": Orden ascendente", sSortDescending: ": Orden descendente"}
                    },
                    dom:
                        "<'row align-items-center'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-md-right'f>>" +
                        "tr" +
                        "<'row align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
                });

                // Estilitos
                $('#tabla_length select').addClass('form-control form-control-sm');
                $('#tabla_filter input').addClass('form-control form-control-sm').css('display', 'inline-block');
            }

            function cargarTabla() {
                $('#tablaDatatable').load(ruta, function () {
                    // AQUI debe existir exactamente un <table id="tabla"> en la parcial
                    initDataTable();
                });
            }

            // Primera carga
            cargarTabla();

            // Exponer recarga para tus flujos (crear/editar)
            window.recargar = function () {
                cargarTabla();
            };
        });
    </script>

    <script>

        function recargar() {
            var ruta = "{{ url('/admin/historial/consultamedica/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        $('#select-empleado').select2({
            theme: "bootstrap-5",
            "language": {
                "noResults": function(){
                    return "Búsqueda no encontrada";
                }
            },
        });

        function informacionBorrar(id){

            Swal.fire({
                title: 'Borrar Registro',
                text: '',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                allowOutsideClick: false,
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarRegistro(id)
                }
            })
        }

        function borrarRegistro(id){
            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(urlAdmin + '/admin/historial/consultamedica/borrar', formData)
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {
                        toastr.success('Borrado correctamente');
                        recargar();
                    } else {
                        toastr.error('Error al borrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al borrar');
                    closeLoading();
                });
        }






        // ===============================
        // CAMBIAR CONDICIÓN EN EL MODAL
        // ===============================
        $(document).on('change', 'input[name="edit-condicion"]', function () {
            toggleEditCondicion($(this).val());
        });

        function toggleEditCondicion(val) {
            if (val == '1') { // fraccionado
                $('#edit-seccion-fraccionado').slideDown(200);
                $('#edit-seccion-completo').slideUp(200);
                $('#edit-fecha-inicio, #edit-fecha-fin, #edit-dias').val('');
            } else { // completo
                $('#edit-seccion-completo').slideDown(200);
                $('#edit-seccion-fraccionado').slideUp(200);
                $('#edit-fecha-solicitud, #edit-hora-inicio, #edit-hora-fin, #edit-duracion').val('');
            }
        }

        // ===============================
        // CALCULAR DURACIÓN AUTOMÁTICA
        // ===============================
        $(document).on('change', '#edit-hora-inicio, #edit-hora-fin', function () {
            let horaInicio = $('#edit-hora-inicio').val();
            let horaFin    = $('#edit-hora-fin').val();

            if (horaInicio && horaFin) {
                let [hI, mI] = horaInicio.split(':');
                let [hF, mF] = horaFin.split(':');

                let ini = new Date(); ini.setHours(parseInt(hI), parseInt(mI), 0, 0);
                let fin = new Date(); fin.setHours(parseInt(hF), parseInt(mF), 0, 0);

                let diffMs = fin - ini;

                if (diffMs > 0) {
                    let totalMin = diffMs / (1000 * 60);
                    let horas    = Math.floor(totalMin / 60);
                    let minutos  = totalMin % 60;
                    let texto    = '';

                    if (horas > 0)   texto += horas + (horas === 1 ? ' hora' : ' horas');
                    if (minutos > 0) texto += (texto ? ' ' : '') + minutos + (minutos === 1 ? ' minuto' : ' minutos');

                    $('#edit-duracion').val(texto);
                } else {
                    $('#edit-duracion').val('');
                    toastr.error('La hora de fin debe ser mayor a la hora de inicio');
                }
            }
        });

        // Calcular días automáticamente (completo)
        $(document).on('change', '#edit-fecha-inicio, #edit-fecha-fin', function () {
            let fi = $('#edit-fecha-inicio').val();
            let ff = $('#edit-fecha-fin').val();

            if (fi && ff) {
                let diferencia = Math.ceil((new Date(ff) - new Date(fi)) / (1000 * 60 * 60 * 24)) + 1;
                $('#edit-dias').val(diferencia > 0 ? diferencia : '');
            }
        });

        // ===============================
        // ABRIR MODAL: CARGAR DATOS
        // ===============================
        function informacion(id) {
            openLoading();
            document.getElementById("formulario-editar").reset();
            $('#edit-seccion-fraccionado, #edit-seccion-completo').hide();
            $('#edit-lista-empleados').hide();

            axios.post(urlAdmin + '/admin/historial/consultamedica/informacion', { id: id })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        const info = response.data.info;

                        // IDs y fechas generales
                        $('#id-editar').val(info.id);
                        $('#edit-fechaEntrego').val(info.fecha);
                        $('#edit-razon').val(info.razon);

                        $('#edit-unidadatencion').val(info.unidad_atencion);
                        $('#edit-especialidad').val(info.especialidad);
                        $('#edit-condicionmedica').val(info.condicion_medica);

                        // Empleado — usa nombre_empleado del backend
                        $('#edit-empleado-id').val(info.id_empleado);
                        $('#edit-empleado-nombre').val(info.nombre_empleado ?? '');
                        $('#edit-unidad').val(info.unidad ?? '');
                        $('#edit-cargo').val(info.cargo ?? '');
                        $('#edit-empleado-actual').show();
                        $('#edit-bloque-buscar').hide();

                        // Condición y campos dinámicos
                        $(`input[name="edit-condicion"][value="${info.condicion}"]`).prop('checked', true);
                        toggleEditCondicion(info.condicion);

                        if (info.condicion == 1) { // fraccionado
                            $('#edit-fecha-solicitud').val(info.fecha_fraccionado);
                            $('#edit-hora-inicio').val(info.hora_inicio);
                            $('#edit-hora-fin').val(info.hora_fin);
                            $('#edit-hora-fin').trigger('change'); // recalcula duración
                        } else { // completo
                            $('#edit-fecha-inicio').val(info.fecha_inicio);
                            $('#edit-fecha-fin').val(info.fecha_fin);
                            $('#edit-fecha-fin').trigger('change'); // recalcula días
                        }

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
            const id          = $('#id-editar').val();
            const empleadoId  = $('#edit-empleado-id').val();
            const condicion   = $('input[name="edit-condicion"]:checked').val();
            const fechaEntrego = $('#edit-fechaEntrego').val();
            const razon       = $('#edit-razon').val().trim();

            const unidadAtencion     = $('#edit-unidadatencion').val().trim();
            const especialidad       = $('#edit-especialidad').val().trim();
            const condicionMedica    = $('#edit-condicionmedica').val().trim();

            // Validaciones generales
            if (!fechaEntrego)          { toastr.error('La fecha es requerida');       return; }
            if (!empleadoId)            { toastr.error('El empleado es requerido');    return; }
            if (condicion === undefined){ toastr.error('La condición es requerida');   return; }

            let extras = {};

            if (condicion == '1') { // fraccionado
                const fechaFraccionado = $('#edit-fecha-solicitud').val();
                const horaInicio     = $('#edit-hora-inicio').val();
                const horaFin        = $('#edit-hora-fin').val();
                const duracion       = $('#edit-duracion').val();

                if (!fechaFraccionado || !horaInicio || !horaFin) {
                    toastr.error('Complete todos los campos del permiso fraccionado');
                    return;
                }
                extras = { fecha_fraccionado: fechaFraccionado, hora_inicio: horaInicio, hora_fin: horaFin, duracion };

            } else { // completo
                const fechaInicio = $('#edit-fecha-inicio').val();
                const fechaFin    = $('#edit-fecha-fin').val();
                const dias        = $('#edit-dias').val();

                if (!fechaInicio || !fechaFin) {
                    toastr.error('Complete todos los campos del permiso completo');
                    return;
                }
                if (new Date(fechaFin) < new Date(fechaInicio)) {
                    toastr.error('La fecha fin no puede ser menor que la fecha inicio');
                    return;
                }
                extras = { fecha_inicio: fechaInicio, fecha_fin: fechaFin, dias_solicitados: dias };
            }

            openLoading();

            const formData = new FormData();
            formData.append('id',           id);
            formData.append('empleado_id',  empleadoId);
            formData.append('condicion',    condicion);
            formData.append('fechaEntrego', fechaEntrego);
            formData.append('razon',        razon);

            formData.append('unidadAtencion', unidadAtencion);
            formData.append('especialidad',   especialidad);
            formData.append('condicionMedica', condicionMedica);
            Object.entries(extras).forEach(([k, v]) => formData.append(k, v ?? ''));

            axios.post(urlAdmin + '/admin/historial/consultamedica/actualizar', formData)
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
        // BOTÓN CAMBIAR EMPLEADO
        // ===============================
        $(document).on('click', '#btn-cambiar-empleado', function () {
            $('#edit-empleado-actual').hide();
            $('#edit-bloque-buscar').show();
            $('#edit-buscar-empleado').val('').focus();
            $('#edit-lista-empleados').hide().html('');
        });

        // Cancelar búsqueda — restaura el empleado actual
        $(document).on('click', '#btn-cancelar-busqueda', function () {
            $('#edit-bloque-buscar').hide();
            $('#edit-empleado-actual').show();
            $('#edit-lista-empleados').hide().html('');
        });


        // ===============================
        // BUSCAR EMPLEADO EN EL MODAL
        // ===============================
        $(document).on('keyup', '#edit-buscar-empleado', function () {
            let texto = $(this).val();

            if (texto.length < 2) {
                $('#edit-lista-empleados').hide().html('');
                return;
            }

            // Si hay una petición en curso, no lanzar otra
            if ($(this).data('buscando')) return;

            const $input = $(this);
            $input.data('buscando', true);

            axios.get(urlAdmin + '/admin/empleados/buscar', { params: { q: texto } })
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
                    </button>
                `;
                    });
                    $('#edit-lista-empleados').html(html).show();
                })
                .catch(() => {
                    toastr.error('Error al buscar empleados');
                })
                .finally(() => {
                    // Liberar el flag al terminar (éxito o error)
                    $input.data('buscando', false);
                });
        });

        // Seleccionar empleado de la lista
        $(document).on('click', '.edit-empleado-item', function () {
            const id     = $(this).data('id');
            const nombre = $(this).data('nombre');
            const unidad = $(this).data('unidad');
            const cargo  = $(this).data('cargo');

            // Asignar valores
            $('#edit-empleado-id').val(id);
            $('#edit-empleado-nombre').val(nombre);
            $('#edit-unidad').val(unidad);
            $('#edit-cargo').val(cargo);

            // Ocultar buscador, mostrar nombre asignado
            $('#edit-lista-empleados').hide();
            $('#edit-bloque-buscar').hide();
            $('#edit-empleado-actual').show();
        });

        // Ocultar lista si clic afuera
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#edit-buscar-empleado, #edit-lista-empleados').length) {
                $('#edit-lista-empleados').hide();
            }
        });





    </script>






    <script>
        (function () {
            // ===== Config inicial =====
            const SERVER_DEFAULT = {{ $temaPredeterminado }}; // 0 = light, 1 = dark
            const iconEl = document.getElementById('theme-icon');

            // CSRF para axios
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

            // ===== Funciones =====
            function applyTheme(mode) {
                const dark = mode === 'dark';

                // AdminLTE v3
                document.body.classList.toggle('dark-mode', dark);

                // AdminLTE v4
                document.documentElement.setAttribute('data-bs-theme', dark ? 'dark' : 'light');

                // Icono
                if (iconEl) {
                    iconEl.classList.remove('fa-sun', 'fa-moon');
                    iconEl.classList.add(dark ? 'fa-moon' : 'fa-sun');
                }
            }

            function themeToInt(mode) {
                return mode === 'dark' ? 1 : 0;
            }

            function intToTheme(v) {
                return v === 1 ? 'dark' : 'light';
            }

            // ===== Aplicar tema inicial desde servidor =====
            applyTheme(intToTheme(SERVER_DEFAULT));

            // ===== Manejo de clicks y POST a backend =====
            let saving = false;

            document.addEventListener('click', async (e) => {
                const a = e.target.closest('.dropdown-item[data-theme]');
                if (!a) return;
                e.preventDefault();
                if (saving) return;

                const selectedMode = a.dataset.theme; // 'dark' | 'light'
                const newValue = themeToInt(selectedMode);

                // Modo optimista: aplicar de una vez
                const previousMode = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
                applyTheme(selectedMode);

                try {
                    saving = true;
                    await axios.post(urlAdmin + '/admin/actualizar/tema', {tema: newValue});
                    // Si querés, mostrar un toast:
                    if (window.toastr) toastr.success('Tema actualizado');
                } catch (err) {
                    // Revertir si falló
                    applyTheme(previousMode);
                    if (window.toastr) {
                        toastr.error('No se pudo actualizar el tema');
                    } else {
                        alert('No se pudo actualizar el tema');
                    }
                } finally {
                    saving = false;
                }
            });
        })();
    </script>

@endsection






















