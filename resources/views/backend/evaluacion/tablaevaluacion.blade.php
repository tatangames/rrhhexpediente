<table id="tabla" class="table table-bordered table-striped">
    <thead>
    <tr>
        <th style="width: 6%">Posición</th>
        <th style="width: 15%">Nombre</th>
        <th style="width: 15%">Descripción</th>
        <th style="width: 8%">Visible</th>
        <th style="width: 8%">Opciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($arrayEvaluacion as $dato)
        <tr>
            <td>{{ $dato->posicion }}</td>
            <td>{{ $dato->nombre }}</td>
            <td>{{ $dato->descripcion }}</td>
            <td>
                @if($dato->estado == 0)
                    NO VISIBLE
                @endif
            </td>
            <td>
                <button type="button"
                        class="btn btn-info btn-xs" onclick="informacion({{ $dato->id }})">
                    <i class="fas fa-edit" title="Editar"></i> Editar</button>

                <button type="button"
                        class="btn btn-warning btn-xs" onclick="infoExtras({{ $dato->id }})">
                    <i class="fas fa-edit" title="Extras"></i> Extras</button>

                <button type="button"
                        class="btn btn-danger btn-xs" onclick="infoBorrar({{ $dato->id }})">
                    <i class="fas fa-trash" title="Borrar"></i> Borrar</button>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
