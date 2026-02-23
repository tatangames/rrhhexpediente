<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluación de Desempeño</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy: #0f1f3d;
            --navy-mid: #1a3260;
            --gold: #c9973a;
            --gold-light: #e8b96a;
            --cream: #f8f5ef;
            --cream-dark: #ede8df;
            --text: #1a1a2e;
            --text-muted: #6b6b80;
            --white: #ffffff;
            --border: #ddd8ce;
            --success: #2e7d5b;
            --error: #b94040;
            --shadow: 0 4px 32px rgba(15,31,61,0.10);
            --shadow-hover: 0 8px 40px rgba(15,31,61,0.18);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--text);
            min-height: 100vh;
        }

        .top-bar {
            height: 6px;
            background: linear-gradient(90deg, var(--navy) 0%, var(--gold) 50%, var(--navy) 100%);
        }

        .page-header {
            background: var(--navy);
            padding: 40px 0 32px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .page-header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(ellipse at 70% 0%, rgba(201,151,58,0.18) 0%, transparent 70%);
        }
        .header-badge {
            display: inline-block;
            background: rgba(201,151,58,0.15);
            border: 1px solid rgba(201,151,58,0.4);
            color: var(--gold-light);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            padding: 6px 18px;
            border-radius: 2px;
            margin-bottom: 16px;
        }
        .page-header h1 {
            font-family: 'Playfair Display', serif;
            color: var(--white);
            font-size: clamp(28px, 4vw, 44px);
            font-weight: 700;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }
        .page-header p {
            color: rgba(255,255,255,0.55);
            font-size: 14px;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        .main-container {
            max-width: 860px;
            margin: 0 auto;
            padding: 40px 20px 80px;
        }

        .section-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 4px;
            box-shadow: var(--shadow);
            margin-bottom: 28px;
            overflow: hidden;
            animation: fadeUp 0.5s ease both;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .section-card:nth-child(1) { animation-delay: 0.05s; }
        .section-card:nth-child(2) { animation-delay: 0.12s; }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 28px;
            background: var(--navy);
            border-bottom: 3px solid var(--gold);
        }
        .section-number {
            width: 28px;
            height: 28px;
            background: var(--gold);
            color: var(--navy);
            font-size: 13px;
            font-weight: 700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .section-header h2 {
            font-family: 'Playfair Display', serif;
            color: var(--white);
            font-size: 17px;
            font-weight: 600;
        }

        .section-body { padding: 28px; }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-grid .full-width { grid-column: 1 / -1; }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .form-control {
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: 3px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: var(--text);
            background: var(--cream);
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--navy-mid);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(26,50,96,0.08);
        }
        .form-control.error {
            border-color: var(--error);
            box-shadow: 0 0 0 3px rgba(185,64,64,0.1);
        }

        .field-error {
            font-size: 11px;
            color: var(--error);
            display: none;
        }
        .field-error.visible { display: block; }

        .periodo-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 14px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
            border-radius: 3px;
            color: var(--gold-light);
            font-weight: 600;
            font-size: 14px;
        }

        /* ── Bloques de evaluación ── */
        .evaluacion-bloque {
            margin-bottom: 24px;
            border: 1.5px solid var(--border);
            border-radius: 4px;
            overflow: hidden;
            transition: box-shadow 0.2s;
        }
        .evaluacion-bloque:last-child { margin-bottom: 0; }
        .evaluacion-bloque:hover { box-shadow: var(--shadow); }

        .bloque-header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 20px;
            background: var(--cream);
            border-bottom: 1px solid var(--border);
        }
        .bloque-num {
            flex-shrink: 0;
            width: 26px;
            height: 26px;
            background: var(--navy);
            color: var(--white);
            font-size: 12px;
            font-weight: 700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1px;
        }
        .bloque-nombre {
            font-weight: 600;
            font-size: 14px;
            color: var(--navy);
            line-height: 1.45;
        }
        .bloque-descripcion {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 3px;
            line-height: 1.5;
        }

        .opciones-lista { padding: 4px 0; }

        .opcion-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            border-bottom: 1px solid var(--cream-dark);
            cursor: pointer;
            transition: background 0.15s;
        }
        .opcion-item:last-child { border-bottom: none; }
        .opcion-item:hover { background: rgba(201,151,58,0.05); }
        .opcion-item.selected { background: rgba(201,151,58,0.07); }
        .opcion-item.selected .opcion-nombre { color: var(--navy); font-weight: 500; }

        .opcion-item input[type="radio"] {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid var(--border);
            border-radius: 50%;
            flex-shrink: 0;
            cursor: pointer;
            transition: all 0.15s;
        }
        .opcion-item input[type="radio"]:checked {
            border-color: var(--gold);
            background: var(--gold);
            box-shadow: inset 0 0 0 3px var(--white);
        }

        .opcion-nombre { font-size: 13.5px; color: var(--text); flex: 1; line-height: 1.4; }
        .opcion-puntos {
            font-size: 11px;
            font-weight: 700;
            color: var(--gold);
            background: rgba(201,151,58,0.12);
            padding: 3px 8px;
            border-radius: 2px;
            white-space: nowrap;
            letter-spacing: 0.5px;
        }

        .evaluacion-bloque.error .bloque-header { border-left: 3px solid var(--error); }
        .bloque-error-msg {
            display: none;
            font-size: 11px;
            color: var(--error);
            padding: 6px 20px;
            background: rgba(185,64,64,0.05);
            border-top: 1px solid rgba(185,64,64,0.15);
        }
        .evaluacion-bloque.error .bloque-error-msg { display: block; }

        /* Resumen */
        .puntaje-resumen {
            background: var(--navy);
            color: var(--white);
            border-radius: 4px;
            padding: 20px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
            animation: fadeUp 0.5s 0.25s ease both;
        }
        .puntaje-label {
            font-size: 13px;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        .puntaje-max { font-size: 14px; color: rgba(255,255,255,0.4); margin-top: 2px; }
        .puntaje-valor {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--gold-light);
            line-height: 1;
        }

        .btn-submit {
            display: block;
            width: 100%;
            padding: 16px;
            background: var(--navy);
            color: var(--white);
            border: none;
            border-radius: 3px;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.5px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: background 0.2s, transform 0.1s;
            animation: fadeUp 0.5s 0.3s ease both;
        }
        .btn-submit::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            background: var(--gold);
        }
        .btn-submit:hover { background: var(--navy-mid); transform: translateY(-1px); }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        /* Vacío */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--text-muted);
            font-size: 14px;
        }

        /* Toast */
        .toast-container {
            position: fixed;
            top: 20px; right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .toast {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: var(--shadow-hover);
            animation: slideIn 0.3s ease;
            max-width: 320px;
            color: var(--white);
        }
        .toast.success { background: var(--success); }
        .toast.error   { background: var(--error); }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(30px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-grid .full-width { grid-column: 1; }
            .puntaje-resumen { flex-direction: column; text-align: center; }
            .section-body { padding: 20px; }
        }

        .indicaciones-banner {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 28px;
            background: rgba(201,151,58,0.08);
            border-bottom: 1px solid rgba(201,151,58,0.25);
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.5;
        }
        .indicaciones-banner strong {
            color: var(--navy);
        }

    </style>
</head>
<body>

<div class="top-bar"></div>

<header class="page-header">
    <div class="header-badge">TALENTO HUMANO</div>
    <h1>FICHA DE EVALUACION DE DESEMPEÑO</h1>
    <p>Complete todos los campos y seleccione una opción por cada criterio</p>
</header>

<div class="main-container">

    {{-- ── Sección 1: Datos del Empleado ── --}}
    <div class="section-card">
        <div class="section-header">
            <h2>I.- DATOS DE IDENTIFICACION:</h2>
        </div>
        <div class="section-body">
            <div class="form-grid">

                <div class="form-group full-width">
                    <label for="nombre_completo">Nombre Completo del Empleado</label>
                    <input type="text" id="nombre_completo" name="nombre_completo"
                           class="form-control" maxlength="100">
                    <span class="field-error" id="err-nombre">Este campo es requerido.</span>
                </div>

                <div class="form-group">
                    <label for="puesto">Puesto o Cargo Actual</label>
                    <input type="text" id="puesto" name="puesto"
                           class="form-control" maxlength="100">
                    <span class="field-error" id="err-puesto">Este campo es requerido.</span>
                </div>

                <div class="form-group">
                    <label for="unidad">Unidad a la que Pertenece</label>
                    <input type="text" id="unidad" name="unidad"
                           class="form-control" maxlength="100">
                    <span class="field-error" id="err-unidad">Este campo es requerido.</span>
                </div>

                <div class="form-group">
                    <label for="dependencia">Dependencia Jerárquica</label>
                    <input type="text" id="dependencia" name="dependencia"
                           class="form-control" maxlength="100">
                    <span class="field-error" id="err-dependencia">Este campo es requerido.</span>
                </div>

                <div class="form-group">
                    <label for="jefe_inmediato">Nombre del Jefe Inmediato</label>
                    <input type="text" id="jefe_inmediato" name="jefe_inmediato"
                           class="form-control" maxlength="100">
                    <span class="field-error" id="err-jefe">Este campo es requerido.</span>
                </div>

                <div class="form-group">
                    <label>Período Evaluado</label>
                    <div class="periodo-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        De Julio-Diciembre 2025
                    </div>
                    <input type="hidden" name="periodo" value="De Julio-Diciembre 2025">
                </div>

            </div>
        </div>
    </div>

    {{-- ── Sección 2: Criterios de Evaluación ── --}}
    <div class="section-card">
        <div class="section-header">
            <h2>II.- FACTORES Y EVALUACION</h2>
        </div>

        <div class="indicaciones-banner">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span>Indicaciones: Marque con una "X" el grado que mejor define la posición del empleado:
            <strong>1- Deficiente, 2- Regular, 3- Bueno, 4- Muy Bueno y 5- Excelente</strong></span>
        </div>

        <div class="section-body">

            @if($evaluaciones->isEmpty())
                <div class="empty-state">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:0.3;margin-bottom:12px;">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <p>No hay criterios de evaluación disponibles.</p>
                </div>
            @else
                @foreach($evaluaciones as $evaluacion)
                    <div class="evaluacion-bloque" id="bloque-{{ $evaluacion->id }}">
                        <div class="bloque-header">
                            <span class="bloque-num">{{ $evaluacion->posicion }}</span>
                            <div>
                                <div class="bloque-nombre">{{ $evaluacion->nombre }}</div>
                                @if($evaluacion->descripcion)
                                    <div class="bloque-descripcion">{{ $evaluacion->descripcion }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="opciones-lista">
                            @foreach($evaluacion->detalles as $detalle)
                                <label class="opcion-item" id="opcion-{{ $evaluacion->id }}-{{ $detalle->id }}"
                                       onclick="marcarSeleccionado({{ $evaluacion->id }}, {{ $detalle->id }})">
                                    <input type="radio"
                                           name="evaluacion_{{ $evaluacion->id }}"
                                           value="{{ $detalle->id }}"
                                           data-puntos="{{ $detalle->puntos }}"
                                           data-eval="{{ $evaluacion->id }}"
                                           onchange="seleccionarOpcion(this)">
                                    <span class="opcion-nombre">{{ $detalle->nombre }}</span>
                                    <span class="opcion-puntos">{{ $detalle->puntos }} pts</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="bloque-error-msg">Por favor seleccione una opción.</div>
                    </div>
                @endforeach
            @endif

        </div>
    </div>

    {{-- ── Resumen de Puntaje ── --}}
    @if($evaluaciones->isNotEmpty())
        <div class="puntaje-resumen">
            <div>
                <div class="puntaje-label">Puntaje Acumulado</div>
                {{-- Calcular el puntaje máximo posible (mayor puntos de cada evaluación) --}}
                @php
                    $puntajeMax = $evaluaciones->sum(function($ev) {
                        return $ev->detalles->max('puntos') ?? 0;
                    });
                @endphp
                <div class="puntaje-max">de {{ $puntajeMax }} puntos posibles</div>
            </div>
            <div style="text-align:right;">
                <div class="puntaje-valor" id="puntaje-actual">0</div>
            </div>
        </div>

        <button class="btn-submit" id="btn-enviar" onclick="enviarEvaluacion()">
            Enviar Evaluación
        </button>
    @endif

</div>

<div class="toast-container" id="toast-container"></div>

<script>
    var CSRF_TOKEN = '{{ csrf_token() }}';
    var URL_REGISTRAR = '{{ route("evaluacion.registrar") }}'; // ajusta el nombre de tu ruta
    var respuestas = {}; // { evaluacion_id: { detalle_id, puntos } }

    // IDs de evaluaciones desde Blade para validación JS
    var evaluacionIds = @json($evaluaciones->pluck('id'));

    // ── Seleccionar opción ──
    function seleccionarOpcion(radio) {
        var evalId    = radio.dataset.eval;
        var puntos    = parseInt(radio.dataset.puntos);
        var detalleId = radio.value;

        respuestas[evalId] = { detalle_id: detalleId, puntos: puntos };

        // Quitar error del bloque
        document.getElementById('bloque-' + evalId).classList.remove('error');

        // Actualizar puntaje
        actualizarPuntaje();
    }

    function marcarSeleccionado(evalId, detalleId) {
        // Quitar .selected de todas las opciones del bloque
        var bloque = document.getElementById('bloque-' + evalId);
        bloque.querySelectorAll('.opcion-item').forEach(function (item) {
            item.classList.remove('selected');
        });
        // Agregar .selected a la opción clickeada
        var opcion = document.getElementById('opcion-' + evalId + '-' + detalleId);
        if (opcion) opcion.classList.add('selected');
    }

    function actualizarPuntaje() {
        var total = 0;
        Object.values(respuestas).forEach(function (r) { total += r.puntos; });
        document.getElementById('puntaje-actual').textContent = total;
    }

    // ── Validar y enviar ──
    function enviarEvaluacion() {
        var valido = true;

        // Validar campos de texto
        var campos = [
            { id: 'nombre_completo', err: 'err-nombre' },
            { id: 'puesto',          err: 'err-puesto' },
            { id: 'unidad',          err: 'err-unidad' },
            { id: 'dependencia',     err: 'err-dependencia' },
            { id: 'jefe_inmediato',  err: 'err-jefe' },
        ];

        campos.forEach(function (c) {
            var el  = document.getElementById(c.id);
            var err = document.getElementById(c.err);
            if (!el.value.trim()) {
                el.classList.add('error');
                err.classList.add('visible');
                valido = false;
            } else {
                el.classList.remove('error');
                err.classList.remove('visible');
            }
            // Quitar error al escribir
            el.oninput = function () {
                if (el.value.trim()) {
                    el.classList.remove('error');
                    err.classList.remove('visible');
                }
            };
        });

        // Validar que cada evaluación tenga respuesta
        evaluacionIds.forEach(function (id) {
            if (!respuestas[id]) {
                document.getElementById('bloque-' + id).classList.add('error');
                valido = false;
            }
        });

        if (!valido) {
            showToast('Por favor complete todos los campos requeridos.', 'error');
            var primerError = document.querySelector('.form-control.error, .evaluacion-bloque.error');
            if (primerError) primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // Construir payload
        var btn = document.getElementById('btn-enviar');
        btn.disabled = true;
        btn.textContent = 'Enviando...';

        var respuestasArr = [];
        Object.keys(respuestas).forEach(function (evalId) {
            respuestasArr.push({
                evaluacion_id: parseInt(evalId),
                detalle_id:    parseInt(respuestas[evalId].detalle_id),
                puntos:        respuestas[evalId].puntos
            });
        });

        var formData = new FormData();
        formData.append('_token',           CSRF_TOKEN);
        formData.append('nombre_completo',  document.getElementById('nombre_completo').value.trim());
        formData.append('puesto',           document.getElementById('puesto').value.trim());
        formData.append('unidad',           document.getElementById('unidad').value.trim());
        formData.append('dependencia',      document.getElementById('dependencia').value.trim());
        formData.append('jefe_inmediato',   document.getElementById('jefe_inmediato').value.trim());
        formData.append('periodo',          'De Julio-Diciembre 2025');
        formData.append('respuestas',       JSON.stringify(respuestasArr));

        fetch(URL_REGISTRAR, {
            method: 'POST',
            body: formData
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success === 1) {
                    showToast('Evaluación enviada correctamente.', 'success');
                    setTimeout(function () { location.reload(); }, 2000);
                } else {
                    showToast(data.message || 'Error al enviar. Intente nuevamente.', 'error');
                }
            })
            .catch(function () {
                showToast('Error de conexión. Intente nuevamente.', 'error');
            })
            .finally(function () {
                btn.disabled = false;
                btn.textContent = 'GENERAR EVALUACIÓN';
            });
    }

    // ── Toast ──
    function showToast(msg, type) {
        var container = document.getElementById('toast-container');
        var toast = document.createElement('div');
        toast.className = 'toast ' + type;
        var icon = type === 'success'
            ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>'
            : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';
        toast.innerHTML = icon + msg;
        container.appendChild(toast);
        setTimeout(function () { toast.remove(); }, 4000);
    }
</script>

</body>
</html>
