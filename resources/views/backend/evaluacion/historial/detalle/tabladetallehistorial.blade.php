@if(count($arrayDetalle) == 0)
    <p class="text-center text-muted my-3">Este título aún no tiene items registrados.</p>
@else
    <table id="tabla" class="table table-bordered table-striped">
        <thead>
        <tr>
            <th style="width: 4%"></th>
            <th style="width: 6%">Posición</th>
            <th style="width: 50%">Nombre</th>
            <th style="width: 10%">Puntos</th>
            <th style="width: 12%">Opciones</th>
        </tr>
        </thead>
        <tbody id="tbody-posicion">
        @foreach($arrayDetalle as $dato)
            <tr data-id="{{ $dato->id }}">
                <td class="text-center handle" style="cursor: move;" title="Arrastrar">
                    <i class="fas fa-grip-vertical text-muted"></i>
                </td>
                <td class="col-posicion">{{ $dato->posicion }}</td>
                <td>{{ $dato->nombre }}</td>
                <td>{{ number_format($dato->puntos, 2) }}</td>
                <td>
                    <button type="button"
                            class="btn btn-info btn-xs" onclick="informacion({{ $dato->id }})">
                        <i class="fas fa-edit" title="Editar"></i> Editar
                    </button>

                    <button type="button"
                            class="btn btn-danger btn-xs" onclick="infoBorrar({{ $dato->id }})">
                        <i class="fas fa-trash" title="Borrar"></i> Borrar
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
