<!DOCTYPE html>
<html lang="es">

@include('backend.urlglobal')

<head>
    <title>REGISTRO</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/login/bootstrap.min.css') }}">

    <!-- icono del sistema -->
    <link rel="icon" type="image/png"  href="{{ asset('images/logosistema.png') }}">

    <!-- libreria -->
    <link href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}" type="text/css" rel="stylesheet" />

    <!-- estilo de toast -->
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    <!-- estilo de sweet -->
    <link href="{{ asset('css/sweetalert2.min.css') }}" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background-image: url({{ asset('images/fondo3.jpg') }});
            background-size: cover;
            background-repeat: no-repeat;
        }

        .demo-container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start; /* 👈 ya no centrado */
            padding-top: 60px;       /* 👈 ajusta aquí */
        }

        .btn-lg {
            padding: 12px 26px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 999px;
        }

        ::placeholder {
            font-size:14px;
            letter-spacing:0.5px;
        }

        .form-control-lg {
            font-size: 16px;
            padding: 25px 20px;
        }

        .font-500{
            font-weight:500;
        }

        .login-card{
            padding: 50px 55px;
            border-radius: 12px;
        }

        .login-logo{
            width:170px;
            margin-bottom: 15px;
        }

        .login-title{
            font-weight:700;
            letter-spacing:1px;
            margin-bottom:30px;
        }

        .login-form label{
            text-align: left !important;
            display: block;
        }

        small.text-danger {
            display: block;
            margin-top: 5px;
            margin-bottom: 10px;
            font-size: 13px;
        }

        .login-form label,
        .login-form small {
            text-align: left !important;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="demo-container" style="margin-top: 0px">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-12 mx-auto">

                    <div class="bg-white shadow-lg text-center login-card">

                        <!-- LOGO -->
                        <img src="{{ asset('images/logo.png') }}" class="login-logo" alt="Santa Ana Norte">

                        <!-- TÍTULO -->
                        <h4 class="login-title">REGISTRO DE EMPLEADO</h4>

                        <form class="login-form">
                            @csrf

                            <div>
                                <!-- NOMBRE -->
                                <label class="font-500">Nombre completo</label>
                                <input class="form-control form-control-lg mb-3"
                                       maxlength="100"
                                       id="nombre"
                                       autocomplete="off"
                                       type="text"
                                       placeholder="Ingrese su nombre"
                                       required>
                                <small id="error-nombre" class="text-danger d-none"></small>

                                <!-- USUARIO -->
                                <label class="font-500">Usuario</label>
                                <input class="form-control form-control-lg mb-3"
                                       maxlength="50"
                                       id="usuario"
                                       autocomplete="off"
                                       type="text"
                                       placeholder="Nombre de usuario"
                                       required>
                                <small id="error-usuario" class="text-danger d-none"></small>


                                <!-- CONTRASEÑA -->
                                <label class="font-500">Contraseña</label>

                                <div class="input-group mb-3">
                                    <input class="form-control form-control-lg"
                                           name="password"
                                           id="password"
                                           maxlength="25"
                                           type="password"
                                           placeholder="Contraseña"
                                           required>

                                    <div class="input-group-append">
                                        <span class="input-group-text bg-white" style="cursor:pointer"
                                              onclick="togglePassword()">
                                            <i id="eyeIcon" class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <small id="error-password" class="text-danger d-none"></small>


                                <button type="button"
                                        style="margin-top: 20px"
                                        onclick="registro()"
                                        class="btn btn-primary btn-lg w-100 shadow-lg">
                                    REGISTRAR
                                </button>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="{{ asset('js/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/sweetalert2.all.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/alertaPersonalizada.js') }}"></script>


<script type="text/javascript">

    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.getElementById("eyeIcon");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        }
    }

    // onkey Enter
    var input = document.getElementById("password");
    input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            registro();
        }
    });

    function mostrarError(id, mensaje) {
        const el = document.getElementById(id);
        el.innerText = mensaje;
        el.classList.remove('d-none');
    }

    function ocultarError(id) {
        const el = document.getElementById(id);
        el.innerText = '';
        el.classList.add('d-none');
    }

    // inicio de sesion
    function registro() {

        limpiarErrores();

        var nombre = document.getElementById('nombre').value;
        var usuario = document.getElementById('usuario').value;
        var password = document.getElementById('password').value;

        let valido = true;

        if (nombre.trim() === '') {
            mostrarError('error-nombre', 'El nombre es obligatorio');
            valido = false;
        } else {
            ocultarError('error-nombre');
        }

        if (usuario.trim() === '') {
            mostrarError('error-usuario', 'El usuario es obligatorio');
            valido = false;
        } else {
            ocultarError('error-usuario');
        }

        if (password.trim() === '') {
            mostrarError('error-password', 'La contraseña es obligatoria');
            valido = false;
        }
        else if (password.length < 4) {
            mostrarError('error-password', 'La contraseña debe tener mínimo 4 caracteres');
            valido = false;
        }
        else {
            ocultarError('error-password');
        }

        if (!valido) {
            return;
        }

        openLoading();


        let formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('usuario', usuario);
        formData.append('password', password);

        axios.post(urlAdmin+'registro/empleado', formData, {
        })
            .then((response) => {
                closeLoading();

                verificar(response);
            })
            .catch((error) => {
                toastr.error('error al iniciar sesión');
                closeLoading();
            });
    }

    function verificar(response) {

        if (response.data.success === 0) {
            toastr.error('validación incorrecta')
        } else if (response.data.success === 1) {
           // usuario repetido
            mostrarError('error-usuario', 'Usuario repetido');

            Swal.fire({
                title: 'Error',
                text: 'El usuario ya se encuentra registrado; por favor, cambiarlo.',
                icon: 'info',
                showCancelButton: false,
                confirmButtonColor: '#007bff',
                allowOutsideClick: false,
                confirmButtonText: 'OK',
            }).then((result) => {
                if (result.isConfirmed) {

                }
            })

        } else if (response.data.success === 2) {
            window.location = response.data.ruta;
        } else {
            toastr.error('Error al registrar');
        }
    }


    function limpiarErrores() {
        ocultarError('error-nombre');
        ocultarError('error-usuario');
        ocultarError('error-password');
    }


</script>
</body>

</html>
