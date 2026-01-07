<table id="tabla" class="table table-bordered table-striped">
    <thead>
    <tr>
        <th style="width: 25%">Nombre</th>
        <th style="width: 8%">Opciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($arrayUnidades as $dato)
        <tr>
            <td>{{ $dato->nombre }}</td>
            <td>
                <button type="button"
                        class="btn btn-info btn-xs" onclick="informacion({{ $dato->id }})">
                    <i class="fas fa-edit" title="Editar"></i> Editar</button>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
