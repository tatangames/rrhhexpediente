<table id="tabla" class="table table-bordered table-striped">
    <thead>
    <tr>
        <th style="width: 15%">Nombre</th>
        <th style="width: 8%">DUI</th>

        <th style="width: 10%">Distrito</th>
        <th style="width: 10%">Unidad</th>
        <th style="width: 10%">Cargo</th>
        <th style="width: 8%">Opciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($arrayEmpleado as $dato)
        <tr>
            <td>{{ $dato->nombre }}</td>
            <td>{{ $dato->dui }}</td>
            <td>{{ $dato->nombreDistrito }}</td>
            <td>{{ $dato->nombreUnidad }}</td>
            <td>{{ $dato->nombreCargo }}</td>
            <td>
                <button type="button"
                        class="btn btn-info btn-xs" onclick="infoFicha({{ $dato->id_administrador }})">
                    <i class="fas fa-edit" title="FICHA"></i> FICHA</button>

                <button type="button"
                        class="btn btn-success btn-xs" style="margin: 2px" onclick="infoPDF({{ $dato->id_administrador }})">
                    <i class="fas fa-edit" title="PDF"></i> PDF</button>

                <button type="button"
                        class="btn btn-info btn-xs" style="margin: 2px" onclick="infoDocumentos({{ $dato->id_administrador }})">
                    <i class="fas fa-edit" title="Documentos"></i> Documentos</button>

            </td>
        </tr>
    @endforeach
    </tbody>
</table>
