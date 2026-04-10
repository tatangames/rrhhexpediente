<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Fecha Solicitud</th>
                                <th>Período</th>
                                <th>Días</th>
                                <th>Nombre</th>
                                <th>Unidad</th>
                                <th>Cargo</th>
                                <th>Diagnóstico</th>
                                <th>Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($arrayPermisos as $dato)
                                <tr>

                                    {{-- Fecha Solicitud --}}
                                    @php
                                        $partes     = explode('-', $dato->fecha);
                                        $ordenFecha = count($partes) === 3
                                            ? $partes[2].'-'.$partes[1].'-'.$partes[0]
                                            : $dato->fecha;
                                    @endphp
                                    <td data-order="{{ $ordenFecha }}">
                                        {{ $dato->fecha }}
                                    </td>

                                    {{-- Período (fecha inicio - fecha fin) --}}
                                    <td>
                                        @if(!$dato->fecha_inicio_fmt)
                                            —
                                        @elseif(!$dato->fecha_fin_fmt || $dato->fecha_inicio_fmt === $dato->fecha_fin_fmt)
                                            {{ $dato->fecha_inicio_fmt }}
                                        @else
                                            {{ $dato->fecha_inicio_fmt }} – {{ $dato->fecha_fin_fmt }}
                                        @endif
                                    </td>

                                    {{-- Días --}}
                                    <td>
                                        @if($dato->dias)
                                            <span class="badge badge-secondary">
                                                    {{ $dato->dias }} {{ $dato->dias == 1 ? 'día' : 'días' }}
                                                </span>
                                        @else
                                            —
                                        @endif
                                    </td>

                                    {{-- Datos del empleado --}}
                                    <td>{{ $dato->nombreEmpleado }}</td>
                                    <td>{{ $dato->unidad ?? '—' }}</td>
                                    <td>{{ $dato->cargo ?? '—' }}</td>

                                    {{-- Diagnóstico --}}
                                    <td>{{ $dato->diagnostico ?? '—' }}</td>

                                    {{-- Opciones --}}
                                    <td>
                                        <button type="button"
                                                class="btn btn-info btn-xs"
                                                onclick="informacion({{ $dato->id }})">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <button type="button"
                                                style="margin: 5px"
                                                class="btn btn-danger btn-xs"
                                                onclick="informacionBorrar({{ $dato->id }})">
                                            <i class="fas fa-trash"></i> Borrar
                                        </button>
                                    </td>

                                </tr>
                            @endforeach

                            <script>
                                setTimeout(function () {
                                    closeLoading();
                                }, 1000);
                            </script>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
