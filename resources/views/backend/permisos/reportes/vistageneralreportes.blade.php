@extends('adminlte::page')

@section('title', 'Generar Permiso')

@section('content_header')

@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />



    <section class="content" style="margin-top: 15px">
        <div class="container-fluid">
            <div class="row">

                <!-- COLUMNA IZQUIERDA: DATOS DEL PERMISO -->
                <div class="col-md-6">

                    <!-- Card para Otros -->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Formulario</h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label>Empleado: <span style="color: red">*</span></label>
                                <br>
                                <select width="100%" class="form-control" id="select-empleado">
                                    @foreach($arrayEmpleados as $item)
                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group col-md-6">
                                <label>Tipo de Permiso: <span style="color: red">*</span></label>
                                <br>
                                <select  class="form-control" id="select-tipopermiso">
                                    <option value="1">Personal</option>
                                    <option value="2">Compensatorio</option>
                                    <option value="3">Enfermedad</option>
                                    <option value="4">Consulta Medica</option>
                                    <option value="5">Incapacidad</option>
                                    <option value="6">Otros</option>
                                </select>
                            </div>


                            <div class="form-group col-md-4" >
                                <label style="color: #686868">Desde: </label>
                                <input type="date" autocomplete="off" class="form-control" id="fecha-desde2">
                            </div>

                            <div class="form-group col-md-4" >
                                <label style="color: #686868">Hasta: </label>
                                <input type="date" autocomplete="off" class="form-control" id="fecha-hasta2">
                            </div>


                            <div class="form-group col-md-3" style="margin-top: 30px">
                                <button type="button" class="btn btn-success form-control" onclick="verificarTotal()">Generar</button>
                            </div>

                        </div>
                    </div>
                </div>



            </div>
        </div>
    </section>


@stop

@section('js')

    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>

        $('#select-empleado').select2({
            theme: "bootstrap-5",
            "language": {
                "noResults": function(){
                    return "Búsqueda no encontrada";
                }
            },
        });


    </script>

@endsection
