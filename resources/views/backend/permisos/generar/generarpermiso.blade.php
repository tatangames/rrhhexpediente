@extends('adminlte::page')

@section('title', 'Generar Permiso')

@section('content_header')
    <h1>Generar Permiso</h1>
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


    <style>
        #lista-empleados .item-compacto {
            padding: 6px 10px !important;
            font-size: 14px;
            line-height: 1.2;
        }
    </style>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-blue">
                <div class="card-header">
                    <h3 class="card-title">Formulario</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="row">
                                <!-- Columna izquierda -->
                                <div class="col-md-6">
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

                                <!-- Columna derecha -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Buscar empleado por nombre: <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="input-buscar-empleado"
                                               placeholder="Escriba el nombre del empleado">
                                        <input type="hidden" id="empleado-id">
                                        <div id="lista-empleados"
                                             class="list-group mt-1"
                                             style="display:none; position: absolute; z-index: 1000; max-height: 300px; overflow-y: auto; width: 100%;">
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
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script>
        $(function () {
            // Inicialización si es necesaria
        });
    </script>

    <script>
        // Buscar empleado
        $('#input-buscar-empleado').on('keyup', function () {
            let texto = $(this).val();

            if (texto.length < 2) {
                $('#lista-empleados').hide().html('');
                $('#empleado-id').val('');
                $('#input-unidad').val('');
                $('#input-cargo').val('');
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

            });
        });

        // Seleccionar empleado
        $(document).on('click', '.empleado-item', function () {
            $('#empleado-id').val($(this).data('id'));
            $('#input-buscar-empleado').val($(this).data('nombre'));
            $('#input-unidad').val($(this).data('unidad'));
            $('#input-cargo').val($(this).data('cargo'));
            $('#lista-empleados').hide();
        });

        // Ocultar lista si se hace clic fuera
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#input-buscar-empleado, #lista-empleados').length) {
                $('#lista-empleados').hide();
            }
        });

        // Botón guardar
        $('#btn-guardar').on('click', function() {
            const tipoPermisoId = $('#select-tipopermiso').val();
            const empleadoId = $('#empleado-id').val();

            // Validaciones
            if (!tipoPermisoId) {
                toastr.error('Debe seleccionar un tipo de permiso');
                return;
            }

            if (!empleadoId) {
                toastr.error('Debe seleccionar un empleado');
                return;
            }

            // Log del ID del empleado
            console.log('ID del empleado seleccionado:', empleadoId);
            console.log('ID del tipo de permiso:', tipoPermisoId);

            // Aquí puedes agregar la lógica para guardar
            toastr.success('Datos validados correctamente');
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
