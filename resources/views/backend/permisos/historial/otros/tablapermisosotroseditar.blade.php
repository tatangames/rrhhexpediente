<table id="tabla" class="table table-bordered table-striped">
    <thead>
    <tr>
        <th style="width: 25%">Nombre</th>
        <th style="width: 25%">Unidad</th>
        <th style="width: 25%">Cargo</th>
        <th style="width: 8%">Opciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($arrayPermisos as $dato)
        <tr>
            <td>{{ $dato->nombreEmpleado }}</td>
            <td>{{ $dato->unidad }}</td>
            <td>{{ $dato->cargo }}</td>
            <td>
                <button type="button"
                        class="btn btn-info btn-xs" onclick="informacion({{ $dato->id }})">
                    <i class="fas fa-edit" title="Editar"></i> Editar</button>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
