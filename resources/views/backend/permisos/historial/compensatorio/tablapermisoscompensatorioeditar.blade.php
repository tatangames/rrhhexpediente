<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 3%">Fecha Solicitud</th>
                                <th style="width: 3%">Tipo</th>
                                <th style="width: 8%">Nombre</th>
                                <th style="width: 7%">Unidad</th>
                                <th style="width: 7%">Cargo</th>
                                <th style="width: 4%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($arrayPermisos as $dato)
                                <tr>
                                    @php
                                        $partes = explode('-', $dato->fecha);
                                        $ordenFecha = count($partes) === 3 ? $partes[2].'-'.$partes[1].'-'.$partes[0] : $dato->fecha;
                                    @endphp
                                    <td data-order="{{ $ordenFecha }}">
                                        {{ $dato->fecha }}
                                    </td>

                                    <td>
                                        @if($dato->condicion == 0)
                                            Dias
                                        @else
                                            Fraccionado
                                        @endif
                                    </td>
                                    <td>{{ $dato->nombreEmpleado }}</td>
                                    <td>{{ $dato->unidad }}</td>
                                    <td>{{ $dato->cargo }}</td>
                                    <td>
                                        <button type="button"
                                                class="btn btn-info btn-xs" onclick="informacion({{ $dato->id }})">
                                            <i class="fas fa-edit" title="Editar"></i> Editar
                                        </button>

                                        <button type="button"
                                                class="btn btn-danger btn-xs" onclick="informacionBorrar({{ $dato->id }})">
                                            <i class="fas fa-trash" title="Borrar"></i> Borrar
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


