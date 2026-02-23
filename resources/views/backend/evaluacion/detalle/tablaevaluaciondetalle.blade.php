<table id="tabla" class="table table-bordered table-striped">
    <thead>
    <tr>
        <th style="width: 6%">Posición</th>
        <th style="width: 15%">Nombre</th>
        <th style="width: 15%">Puntos</th>
        <th style="width: 8%">Opciones</th>
    </tr>
    </thead>
    <tbody>

    @foreach($arrayDetalle as $dato)
        <tr>
            <td>{{ $dato->posicion }}</td>
            <td>{{ $dato->nombre }}</td>
            <td>{{ $dato->puntos }}</td>
            <td>
                <button type="button"
                        class="btn btn-info btn-xs" onclick="informacion({{ $dato->id }})">
                    <i class="fas fa-edit" title="Editar"></i> Editar</button>

                <button type="button"
                        class="btn btn-danger btn-xs" onclick="infoBorrar({{ $dato->id }})">
                    <i class="fas fa-trash" title="Borrar"></i> Borrar</button>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
