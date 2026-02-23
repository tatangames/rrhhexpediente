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
        .section-header h2 {
            font-family: 'Playfair Display', serif;
            color: var(--white);
            font-size: 17px;
            font-weight: 600;
        }

        .section-body { padding: 28px; }

        /* Grid principal 1 columna */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .form-grid .full-width { grid-column: 1; }

        /* Subgrid de 1 columna para puesto/unidad/dependencia */
        .form-grid-3col {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

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

        /* ── Custom Select con buscador ── */
        .custom-select-wrapper { position: relative; }

        .custom-select-trigger {
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: 3px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: var(--text);
            background: var(--cream);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            user-select: none;
            min-height: 44px;
        }
        .custom-select-trigger:hover,
        .custom-select-trigger.open {
            border-color: var(--navy-mid);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(26,50,96,0.08);
        }
        .custom-select-trigger.error {
            border-color: var(--error);
            box-shadow: 0 0 0 3px rgba(185,64,64,0.1);
        }
        .trigger-text {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .trigger-text.placeholder { color: var(--text-muted); }
        .trigger-text.selected-text { color: var(--text); font-weight: 500; }
        .select-arrow {
            flex-shrink: 0;
            transition: transform 0.2s;
            color: var(--text-muted);
        }
        .custom-select-trigger.open .select-arrow { transform: rotate(180deg); }

        .custom-select-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            left: 0; right: 0;
            background: var(--white);
            border: 1.5px solid var(--navy-mid);
            border-radius: 3px;
            box-shadow: var(--shadow-hover);
            z-index: 200;
            display: none;
            overflow: hidden;
        }
        .custom-select-dropdown.open { display: block; }

        .select-search {
            padding: 9px 12px;
            border: none;
            border-bottom: 1px solid var(--border);
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            width: 100%;
            outline: none;
            background: var(--cream);
            color: var(--text);
        }
        .select-search:focus { background: var(--white); }

        .select-options {
            max-height: 200px;
            overflow-y: auto;
        }
        .select-option {
            padding: 10px 14px;
            font-size: 13.5px;
            cursor: pointer;
            transition: background 0.12s;
            border-bottom: 1px solid var(--cream-dark);
            line-height: 1.4;
        }
        .select-option:last-child { border-bottom: none; }
        .select-option:hover { background: rgba(201,151,58,0.07); }
        .select-option.selected { background: rgba(201,151,58,0.12); color: var(--navy); font-weight: 600; }
        .select-option.hidden { display: none; }
        .select-no-results {
            padding: 12px 14px;
            font-size: 13px;
            color: var(--text-muted);
            text-align: center;
            display: none;
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

        /* Botón */
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
        .indicaciones-banner strong { color: var(--navy); }

        @media (max-width: 700px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-grid .full-width { grid-column: 1; }
            .form-grid-3col { grid-template-columns: 1fr; }
            .section-body { padding: 20px; }
        }
        @media (min-width: 701px) and (max-width: 860px) {
            .form-grid-3col { grid-template-columns: 1fr; }
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

                {{-- Nombre completo --}}
                <div class="form-group full-width">
                    <label for="nombre_completo">Nombre Completo del Empleado</label>
                    <input type="text" id="nombre_completo" name="nombre_completo"
                           class="form-control" maxlength="100"
                           placeholder="Ingrese el nombre completo">
                    <span class="field-error" id="err-nombre">Este campo es requerido.</span>
                </div>

                {{-- Fila de 3 selects: Puesto | Unidad | Dependencia --}}
                <div class="form-group full-width">
                    <div class="form-grid-3col">

                        {{-- Puesto --}}
                        <div class="form-group">
                            <label>Puesto o Cargo Actual</label>
                            <div class="custom-select-wrapper" id="wrap-puesto">
                                <div class="custom-select-trigger" id="trigger-puesto"
                                     onclick="toggleSelect('puesto')">
                                    <span class="trigger-text placeholder" id="display-puesto">Seleccionar cargo...</span>
                                    <svg class="select-arrow" width="14" height="14" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2.5">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </div>
                                <div class="custom-select-dropdown" id="dropdown-puesto">
                                    <input class="select-search" type="text"
                                           placeholder="&#128269; Buscar cargo..."
                                           oninput="filterOptions('puesto', this.value)">
                                    <div class="select-options" id="options-puesto">
                                        @foreach($arrayCargos as $cargo)
                                            <div class="select-option"
                                                 data-value="{{ $cargo->nombre }}"
                                                 onclick="selectOption('puesto', '{{ addslashes($cargo->nombre) }}')">
                                                {{ $cargo->nombre }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="select-no-results" id="no-results-puesto">Sin resultados</div>
                                </div>
                            </div>
                            <input type="hidden" name="puesto" id="val-puesto">
                            <span class="field-error" id="err-puesto">Este campo es requerido.</span>
                        </div>

                        {{-- Unidad --}}
                        <div class="form-group">
                            <label>Unidad a la que Pertenece</label>
                            <div class="custom-select-wrapper" id="wrap-unidad">
                                <div class="custom-select-trigger" id="trigger-unidad"
                                     onclick="toggleSelect('unidad')">
                                    <span class="trigger-text placeholder" id="display-unidad">Seleccionar unidad...</span>
                                    <svg class="select-arrow" width="14" height="14" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2.5">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </div>
                                <div class="custom-select-dropdown" id="dropdown-unidad">
                                    <input class="select-search" type="text"
                                           placeholder="&#128269; Buscar unidad..."
                                           oninput="filterOptions('unidad', this.value)">
                                    <div class="select-options" id="options-unidad">
                                        @foreach($arrayUnidades as $unidad)
                                            <div class="select-option"
                                                 data-value="{{ $unidad->nombre }}"
                                                 onclick="selectOption('unidad', '{{ addslashes($unidad->nombre) }}')">
                                                {{ $unidad->nombre }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="select-no-results" id="no-results-unidad">Sin resultados</div>
                                </div>
                            </div>
                            <input type="hidden" name="unidad" id="val-unidad">
                            <span class="field-error" id="err-unidad">Este campo es requerido.</span>
                        </div>

                        {{-- Dependencia --}}
                        <div class="form-group">
                            <label>Dependencia Jerárquica</label>
                            <div class="custom-select-wrapper" id="wrap-dependencia">
                                <div class="custom-select-trigger" id="trigger-dependencia"
                                     onclick="toggleSelect('dependencia')">
                                    <span class="trigger-text placeholder" id="display-dependencia">Seleccionar dependencia...</span>
                                    <svg class="select-arrow" width="14" height="14" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2.5">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </div>
                                <div class="custom-select-dropdown" id="dropdown-dependencia">
                                    <input class="select-search" type="text"
                                           placeholder="&#128269; Buscar dependencia..."
                                           oninput="filterOptions('dependencia', this.value)">
                                    <div class="select-options" id="options-dependencia">
                                        @foreach($arrayDependencias as $dep)
                                            <div class="select-option"
                                                 data-value="{{ $dep->nombre }}"
                                                 onclick="selectOption('dependencia', '{{ addslashes($dep->nombre) }}')">
                                                {{ $dep->nombre }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="select-no-results" id="no-results-dependencia">Sin resultados</div>
                                </div>
                            </div>
                            <input type="hidden" name="dependencia" id="val-dependencia">
                            <span class="field-error" id="err-dependencia">Este campo es requerido.</span>
                        </div>

                    </div>
                </div>

                {{-- Jefe inmediato --}}
                <div class="form-group">
                    <label for="jefe_inmediato">Nombre del Jefe Inmediato</label>
                    <input type="text" id="jefe_inmediato" name="jefe_inmediato"
                           class="form-control" maxlength="100"
                           placeholder="Ingrese el nombre del jefe">
                    <span class="field-error" id="err-jefe">Este campo es requerido.</span>
                </div>

                {{-- Período evaluado --}}
                <div class="form-group">
                    <label>Período Evaluado</label>
                    <div class="periodo-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2">
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
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" style="flex-shrink:0;margin-top:1px;">
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
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.5" style="opacity:0.3;margin-bottom:12px;display:block;margin-left:auto;margin-right:auto;">
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
                                <label class="opcion-item"
                                       id="opcion-{{ $evaluacion->id }}-{{ $detalle->id }}"
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

    {{-- ── Botón Enviar ── --}}
    @if($evaluaciones->isNotEmpty())
        <button class="btn-submit" id="btn-enviar" onclick="enviarEvaluacion()">
            Enviar Evaluación
        </button>
    @endif

</div>

<div class="toast-container" id="toast-container"></div>

<script>
    var CSRF_TOKEN   = '{{ csrf_token() }}';
    var URL_REGISTRAR = '{{ route("evaluacion.registrar") }}';
    var respuestas   = {};
    var evaluacionIds = @json($evaluaciones->pluck('id'));

    /* ════════════════════════════════
       CUSTOM SELECT CON BUSCADOR
    ════════════════════════════════ */
    var openSelect = null;

    function toggleSelect(id) {
        if (openSelect && openSelect !== id) closeSelect(openSelect);
        var dropdown = document.getElementById('dropdown-' + id);
        var trigger  = document.getElementById('trigger-'  + id);
        if (dropdown.classList.contains('open')) {
            closeSelect(id);
        } else {
            dropdown.classList.add('open');
            trigger.classList.add('open');
            openSelect = id;
            setTimeout(function() {
                var search = dropdown.querySelector('.select-search');
                if (search) search.focus();
            }, 50);
        }
    }

    function closeSelect(id) {
        var dropdown = document.getElementById('dropdown-' + id);
        var trigger  = document.getElementById('trigger-'  + id);
        if (dropdown) dropdown.classList.remove('open');
        if (trigger)  trigger.classList.remove('open');
        if (openSelect === id) openSelect = null;
    }

    function selectOption(id, value) {
        document.getElementById('val-' + id).value = value;

        var display = document.getElementById('display-' + id);
        display.textContent = value;
        display.className   = 'trigger-text selected-text';

        var trigger = document.getElementById('trigger-' + id);
        trigger.classList.remove('error');
        document.getElementById('err-' + id).classList.remove('visible');

        // Marcar opción activa
        document.querySelectorAll('#options-' + id + ' .select-option').forEach(function(opt) {
            opt.classList.toggle('selected', opt.dataset.value === value);
        });

        // Limpiar buscador
        var search = document.querySelector('#dropdown-' + id + ' .select-search');
        if (search) {
            search.value = '';
            filterOptions(id, '');
        }

        closeSelect(id);
    }

    function filterOptions(id, query) {
        var q    = query.toLowerCase().trim();
        var opts = document.querySelectorAll('#options-' + id + ' .select-option');
        var visible = 0;
        opts.forEach(function(opt) {
            var match = opt.textContent.toLowerCase().includes(q);
            opt.classList.toggle('hidden', !match);
            if (match) visible++;
        });
        var noResults = document.getElementById('no-results-' + id);
        if (noResults) noResults.style.display = visible === 0 ? 'block' : 'none';
    }

    // Cerrar al click fuera
    document.addEventListener('click', function(e) {
        if (!openSelect) return;
        var wrapper = document.getElementById('wrap-' + openSelect);
        if (wrapper && !wrapper.contains(e.target)) {
            closeSelect(openSelect);
        }
    });

    /* ════════════════════════════════
       EVALUACIÓN — RADIO BUTTONS
    ════════════════════════════════ */
    function seleccionarOpcion(radio) {
        var evalId    = radio.dataset.eval;
        var puntos    = parseInt(radio.dataset.puntos);
        var detalleId = radio.value;
        respuestas[evalId] = { detalle_id: detalleId, puntos: puntos };
        document.getElementById('bloque-' + evalId).classList.remove('error');
    }

    function marcarSeleccionado(evalId, detalleId) {
        var bloque = document.getElementById('bloque-' + evalId);
        bloque.querySelectorAll('.opcion-item').forEach(function(item) {
            item.classList.remove('selected');
        });
        var opcion = document.getElementById('opcion-' + evalId + '-' + detalleId);
        if (opcion) opcion.classList.add('selected');
    }

    /* ════════════════════════════════
       VALIDACIÓN Y ENVÍO
    ════════════════════════════════ */
    function enviarEvaluacion() {
        var valido = true;

        // Validar input texto
        var inputCampos = [
            { id: 'nombre_completo', err: 'err-nombre' },
            { id: 'jefe_inmediato',  err: 'err-jefe'   },
        ];
        inputCampos.forEach(function(c) {
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
        });

        // Validar selects
        var selectCampos = ['puesto', 'unidad', 'dependencia'];
        selectCampos.forEach(function(id) {
            var val     = document.getElementById('val-' + id).value;
            var trigger = document.getElementById('trigger-' + id);
            var err     = document.getElementById('err-' + id);
            if (!val.trim()) {
                trigger.classList.add('error');
                err.classList.add('visible');
                valido = false;
            } else {
                trigger.classList.remove('error');
                err.classList.remove('visible');
            }
        });

        // Validar criterios de evaluación
        evaluacionIds.forEach(function(id) {
            if (!respuestas[id]) {
                document.getElementById('bloque-' + id).classList.add('error');
                valido = false;
            }
        });

        if (!valido) {
            showToast('Por favor complete todos los campos requeridos.', 'error');
            // Scroll al primer error
            var firstError = document.querySelector('.error, .form-control.error');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // Construir y enviar formulario
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = URL_REGISTRAR;

        var csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = CSRF_TOKEN;
        form.appendChild(csrf);

        var respuestasArr = [];
        Object.keys(respuestas).forEach(function(evalId) {
            respuestasArr.push({
                evaluacion_id: parseInt(evalId),
                detalle_id:    parseInt(respuestas[evalId].detalle_id),
                puntos:        respuestas[evalId].puntos
            });
        });

        var camposEnviar = {
            nombre_completo: document.getElementById('nombre_completo').value.trim(),
            puesto:          document.getElementById('val-puesto').value,
            unidad:          document.getElementById('val-unidad').value,
            dependencia:     document.getElementById('val-dependencia').value,
            jefe_inmediato:  document.getElementById('jefe_inmediato').value.trim(),
            periodo:         'De Julio-Diciembre 2025',
            respuestas:      JSON.stringify(respuestasArr)
        };

        for (var key in camposEnviar) {
            var input = document.createElement('input');
            input.type = 'hidden'; input.name = key; input.value = camposEnviar[key];
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    }

    /* ════════════════════════════════
       TOAST
    ════════════════════════════════ */
    function showToast(msg, type) {
        var container = document.getElementById('toast-container');
        var toast = document.createElement('div');
        toast.className = 'toast ' + type;
        var icon = type === 'success'
            ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>'
            : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';
        toast.innerHTML = icon + msg;
        container.appendChild(toast);
        setTimeout(function() { toast.remove(); }, 4000);
    }
</script>

</body>
</html>
