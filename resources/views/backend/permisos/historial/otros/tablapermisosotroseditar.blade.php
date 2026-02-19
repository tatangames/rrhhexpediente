<table id="tabla" class="table table-bordered table-striped">
    <thead>
    <tr>
        <th style="width: 6%">Fecha</th>
        <th style="width: 8%">Nombre</th>
        <th style="width: 7%">Unidad</th>
        <th style="width: 7%">Cargo</th>
        <th style="width: 4%">Opciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($arrayPermisos as $dato)
        <tr>
            <td>{{ $dato->fecha }}</td>
            <td>{{ $dato->nombreEmpleado }}</td>
            <td>{{ $dato->unidad }}</td>
            <td>{{ $dato->cargo }}</td>
            <td>
                <button type="button"
                        class="btn btn-info btn-xs" onclick="informacion({{ $dato->id }})">
                    <i class="fas fa-edit" title="Editar"></i> Editar</button>
                <button type="button"
                        class="btn btn-danger btn-xs" onclick="informacionBorrar({{ $dato->id }})">
                    <i class="fas fa-trash" title="Borrar"></i> Borrar</button>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
