@if(count($arrayTitulos) == 0)
    <p class="text-center text-muted my-3">Este tipo de evaluación aún no tiene títulos registrados.</p>
@else
    <table id="tabla" class="table table-bordered table-striped">
        <thead>
        <tr>
            <th style="width: 4%"></th>
            <th style="width: 6%">Posición</th>
            <th style="width: 25%">Nombre</th>
            <th style="width: 30%">Descripción</th>
            <th style="width: 8%">Visible</th>
            <th style="width: 18%">Opciones</th>
        </tr>
        </thead>
        <tbody id="tbody-posicion">
        @foreach($arrayTitulos as $dato)
            <tr data-id="{{ $dato->id }}">
                <td class="text-center handle" style="cursor: move;" title="Arrastrar">
                    <i class="fas fa-grip-vertical text-muted"></i>
                </td>
                <td class="col-posicion">{{ $dato->posicion }}</td>
                <td>{{ $dato->nombre }}</td>
                <td>{{ $dato->descripcion }}</td>
                <td>
                    @if($dato->estado == 0)
                        <span class="badge badge-danger">NO VISIBLE</span>
                    @else
                        <span class="badge badge-success">VISIBLE</span>
                    @endif
                </td>
                <td>
                    <button type="button"
                            class="btn btn-info btn-xs" onclick="informacion({{ $dato->id }})">
                        <i class="fas fa-edit" title="Editar"></i> Editar
                    </button>

                    <button type="button"
                            class="btn btn-warning btn-xs" onclick="infoItems({{ $dato->id }})">
                        <i class="fas fa-list" title="Items"></i> Items
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
