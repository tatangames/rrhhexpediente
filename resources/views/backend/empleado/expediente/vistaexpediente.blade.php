@extends('adminlte::page')

@section('title', 'Expediente')

@section('content_header')
    <h1>Expediente</h1>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

{{-- =======================
    CSS
======================= --}}
@section('css')
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">

    <style>
        .dropzone {
            border: 2px dashed #c3c4c7;
            background: #f6f7f7;
            border-radius: 6px;
            padding: 40px;
        }
        .dropzone .dz-message {
            font-size: 18px;
            color: #50575e;
        }
        .dropzone .dz-message span {
            display: block;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
@endsection

{{-- =======================
    TOP NAV
======================= --}}
@section('content_top_nav_right')
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
            <i id="theme-icon" class="fas fa-sun"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="#" data-theme="dark">
                <i class="far fa-moon mr-2"></i> Dark
            </a>
            <a class="dropdown-item" href="#" data-theme="light">
                <i class="far fa-sun mr-2"></i> Light
            </a>
        </div>
    </li>

    <li class="nav-item dropdown">
        <a href="#" class="nav-link" data-toggle="dropdown">
            <i class="fas fa-cogs"></i>
            <span class="d-none d-md-inline">
            {{ Auth::guard('admin')->user()->nombre }}
        </span>
        </a>

        <div class="dropdown-menu dropdown-menu-right">
            <a href="{{ route('admin.perfil') }}" class="dropdown-item">
                <i class="fas fa-user mr-2"></i> Editar Perfil
            </a>

            <div class="dropdown-divider"></div>

            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </li>
@endsection

{{-- =======================
    CONTENT
======================= --}}
@section('content')

    {{-- UPLOAD --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Subir documentos</h3>
        </div>

        <div class="card-body">

            <div class="alert alert-info py-2 mb-4">
                <i class="fas fa-info-circle mr-1"></i>
                Adjunta aquí los documentos que forman parte del expediente, como
                <strong>currículum (CV)</strong>, <strong>DUI</strong>, constancias laborales,
                títulos académicos, certificaciones, identificaciones y otros documentos relevantes.
            </div>

            <form action="{{ route('empleado.media.upload') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="dropzone"
                  id="media-dropzone">
                @csrf

                <div class="dz-message">
                    Arrastra los archivos para subirlos
                    <span>o haz clic para seleccionar archivos</span>
                    <small class="text-muted d-block mt-2">
                        Tamaño máximo por archivo: 300 MB
                    </small>
                </div>
            </form>
        </div>
    </div>

    {{-- LISTADO --}}
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Documentos subidos</h3>
        </div>

        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead>
                <tr>
                    <th>Archivo</th>
                    <th>Tamaño</th>
                    <th>Fecha</th>
                    <th width="120">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($documentos as $doc)
                    <tr>
                        <td>
                            <i class="fas fa-file mr-1"></i>
                            {{ $doc->nombre_original }}
                        </td>

                        <td>
                            {{ number_format($doc->size / 1024 / 1024, 2) }} MB
                        </td>

                        <td>
                            {{ $doc->created_at->format('d-m-Y') }}
                        </td>

                        <td class="text-center">
                            {{-- DESCARGAR --}}
                            <a href="{{ route('empleado.media.download', $doc->id) }}"
                               class="btn btn-sm btn-success">
                                <i class="fas fa-download"></i>
                            </a>

                            {{-- BORRAR --}}
                            <button class="btn btn-sm btn-danger"
                                    onclick="eliminarDocumento({{ $doc->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            No hay documentos subidos
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>
    </div>

@stop

{{-- =======================
    JS
======================= --}}
@section('js')
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

    <script>
        window.AppConfig = {
            themeDefault: {{ $temaPredeterminado }},
            updateThemeUrl: "{{ route('admin.tema.update') }}",
        };
    </script>

    <script src="{{ asset('js/theme.js') }}"></script>

    <script>
        Dropzone.autoDiscover = false;

        new Dropzone("#media-dropzone", {
            paramName: "files",
            uploadMultiple: true,
            parallelUploads: 10,
            maxFilesize: 300,
            acceptedFiles: ".jpg,.png,.pdf,.doc,.docx,.xls,.xlsx",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function () {
                toastr.success('Archivo subido correctamente');
                setTimeout(() => location.reload(), 800);
            },
            error: function (file, message) {
                toastr.error(message);
            }
        });
    </script>



    <script>
        function eliminarDocumento(id) {
            Swal.fire({
                title: '¿Eliminar documento?',
                text: 'Este archivo se eliminará permanentemente',
                showCancelButton: true,
                confirmButtonColor: '#dc3545', // rojo AdminLTE
                cancelButtonColor: '#6c757d',  // gris
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete('{{ url('/empleado/eliminar/media') }}/' + id)
                        .then(response => {
                            toastr.success(response.data.message);
                            setTimeout(() => location.reload(), 600);
                        })
                        .catch(() => {
                            toastr.error('Error al eliminar el archivo');
                        });
                }
            });

        }
    </script>




@endsection
