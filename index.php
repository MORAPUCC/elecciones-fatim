<?php
require_once 'config/conexion.php';
// Consultar todas las listas con sus datos completos
$resultado_listas = $conexion->query("SELECT * FROM listas");
$listas = [];
while ($row = $resultado_listas->fetch_assoc()) {
    $listas[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elecciones Escolares 2026 - Sistema de Votación Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .card-custom { border-radius: 18px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .lista-card { cursor: pointer; transition: all 0.25s ease; border: 2px solid #e9ecef; border-radius: 14px; }
        .lista-card:hover { transform: translateY(-4px); border-color: #0d6efd; box-shadow: 0 6px 16px rgba(13,110,253,0.18); }
        .lista-card.seleccionada { border-color: #198754; background-color: #f4fbf7; box-shadow: 0 6px 18px rgba(25,135,84,0.25); }
        .img-candidata { width: 90px; height: 90px; object-fit: cover; border-radius: 50%; border: 3px solid #dee2e6; }
        .integrantes-list { font-size: 0.88rem; text-align: left; }
        
        /* FICHA CABECERA ELECTORA */
        .banner-electora {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 50%, #0a58ca 100%);
            border-radius: 14px;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.25);
        }
        .badge-aula {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(4px);
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        .btn-volver-custom {
            background-color: #ffffff;
            color: #0d6efd;
            border: none;
            font-weight: 600;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.12);
            transition: all 0.2s ease;
        }
        .btn-volver-custom:hover {
            background-color: #f8f9fa;
            color: #0a58ca;
            transform: translateX(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.18);
        }

        /* ESTILOS NUEVOS PARA EL COMPROBANTE DE ÉXITO */
        .ticket-comprobante {
            background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%);
            border: 2px dashed #198754 !important;
            border-radius: 14px;
            position: relative;
        }
        .icono-exito-glow {
            width: 75px;
            height: 75px;
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto;
            box-shadow: 0 0 0 8px rgba(25, 135, 84, 0.05);
        }
    </style>
</head>
<body>

<div class="container d-flex flex-column justify-content-center align-items-center min-vh-100 py-4">
    
    <div class="text-center mb-4">
        <div class="mb-2">
            <i class="fas fa-check-square text-primary fa-3x"></i>
        </div>
        <h2 class="fw-bold text-dark">Elecciones Escolares 2026</h2>
        <p class="text-muted small mb-0">Sistema de Votación Digital</p>
    </div>

    <div class="col-md-7">
        
        <!-- PASO 1: IDENTIFICACIÓN DE LA ALUMNA -->
        <div id="paso-dni" class="card card-custom p-4 bg-white">
            <h5 class="fw-bold mb-3"><i class="fas fa-id-card text-primary me-2"></i>Identificación de la Alumna</h5>
            
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label small fw-semibold">Grado</label>
                    <select class="form-select form-select-sm" id="select-grado">
                        <option value="">Seleccionar...</option>
                        <option value="1">1° de Secundaria</option>
                        <option value="2">2° de Secundaria</option>
                        <option value="3">3° de Secundaria</option>
                        <option value="4">4° de Secundaria</option>
                        <option value="5">5° de Secundaria</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">Sección</label>
                    <select class="form-select form-select-sm" id="select-seccion">
                        <option value="">Seleccionar...</option>
                        <script>
                            const secs = ["A","B","C","D","E","F","G","H","I","J","K","L"];
                            secs.forEach(s => document.write(`<option value="${s}">${s}</option>`));
                        </script>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Tipo de Documento</label>
                <select class="form-select form-select-sm" id="select-tipodoc">
                    <option value="DNI">DNI</option>
                    <option value="EXTRANJERIA">Carnet de Extranjería</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Número de Documento</label>
                <input type="text" id="input-dni" class="form-control" placeholder="Ingrese DNI o Carnet" autocomplete="off">
            </div>

            <button onclick="verificarAlumna()" class="btn btn-primary w-100 fw-bold py-2">
                <i class="fas fa-search me-2"></i>Validar / Ingresar
            </button>

            <div id="error-dni" class="alert alert-danger mt-3 d-none py-2 text-center small" role="alert"></div>
        </div>

        <!-- PASO 2: CÉDULA DE VOTACIÓN CON DETALLES COMPLETOS -->
        <div id="paso-votacion" class="card card-custom p-4 bg-white d-none">
            
            <!-- FICHA DE ELECTORA LLAMATIVA -->
            <div class="banner-electora p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div>
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.75rem;">Electora Acreditada</div>
                            <h5 class="fw-bold mb-1 text-capitalize" id="lbl-nombre">---</h5>
                            <div class="d-flex align-items-center gap-2 small">
                                <span><i class="fas fa-id-card me-1 opacity-75"></i><span id="lbl-dni">---</span></span>
                                <span>•</span>
                                <span class="badge badge-aula px-2 py-1"><i class="fas fa-school me-1"></i><span id="lbl-aula">---</span></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button onclick="volverAIdentificacion()" class="btn btn-sm btn-volver-custom px-3 py-2">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </button>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold text-center mb-1">Escoja alguna opción</h5>
            <p class="text-muted text-center small mb-4">Haga clic sobre la lista u opción para seleccionarla:</p>

            <div class="row g-3 mb-4" id="contenedor-listas">
                <?php if (empty($listas)): ?>
                    <div class="col-12 text-center text-muted py-3">
                        <small>No hay listas registradas por el docente todavía.</small>
                    </div>
                <?php else: ?>
                    <?php foreach ($listas as $l): ?>
                        <div class="col-12">
                            <div class="card lista-card p-3" onclick="seleccionarLista(this, <?php echo $l['id']; ?>, '<?php echo htmlspecialchars($l['nombre_lista'], ENT_QUOTES); ?>')">
                                <div class="row align-items-center">
                                    <div class="col-md-3 text-center mb-2 mb-md-0">
                                        <img src="<?php echo !empty($l['logo_url']) ? $l['logo_url'] : 'assets/img/default-logo.png'; ?>" class="img-candidata mb-2" alt="Logo">
                                        <h6 class="fw-bold text-primary mb-0"><?php echo htmlspecialchars($l['nombre_lista']); ?></h6>
                                    </div>
                                    <div class="col-md-9 border-start-md">
                                        <div class="integrantes-list">
                                            <?php if (!empty($l['lider'])): ?>
                                                <p class="mb-1"><strong>Alcaldesa:</strong> <?php echo htmlspecialchars($l['lider']); ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($l['teniente_alcalde'])): ?>
                                                <p class="mb-1"><strong>Teniente Alcaldesa:</strong> <?php echo htmlspecialchars($l['teniente_alcalde']); ?></p>
                                            <?php endif; ?>

                                            <?php if (!empty($l['regidores'])): ?>
                                                <p class="mb-0 text-muted"><strong>Regidoras / Integrantes:</strong> <?php echo htmlspecialchars($l['regidores']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button id="btn-emitir" onclick="emitirVoto()" class="btn btn-success w-100 fw-bold py-2" disabled>
                <i class="fas fa-check-circle me-2"></i>Confirmar y Emitir Voto
            </button>
        </div>

        <!-- PASO 3: COMPROBANTE RESUMEN DEL SUFRAGIO MEJORADO -->
        <div id="paso-exito" class="card card-custom p-4 bg-white d-none text-center">
            <div class="mb-3 pt-2">
                <div class="icono-exito-glow">
                    <i class="fas fa-check fa-2x"></i>
                </div>
            </div>
            <h4 class="fw-bold text-success mb-1">¡Voto Registrado con Éxito!</h4>
            <p class="text-muted small mb-4">Su participación ha sido procesada correctamente en el sistema.</p>
            
            <div class="ticket-comprobante p-3 text-start mb-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <span class="fw-bold text-success small text-uppercase tracking-wider"><i class="fas fa-ticket-alt me-1"></i> Comprobante de Sufragio</span>
                    <span class="badge bg-success text-light font-monospace">OFICIAL</span>
                </div>
                <div class="row g-2">
                    <div class="col-12 mb-1">
                        <span class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Nombres y Apellidos</span>
                        <span class="fw-bold text-dark text-capitalize" id="comp-nombre" style="font-size: 1.05rem;">---</span>
                    </div>
                    <div class="col-6 mb-1">
                        <span class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">N° de Documento</span>
                        <span class="fw-semibold text-dark" id="comp-dni">---</span>
                    </div>
                    <div class="col-6 mb-1">
                        <span class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Grado y Sección</span>
                        <span class="fw-semibold text-dark" id="comp-aula">---</span>
                    </div>
                    <div class="col-12 mt-2 pt-2 border-top">
                        <span class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Opción / Lista Elegida</span>
                        <span class="text-primary fw-bold fs-5" id="comp-opcion">---</span>
                    </div>
                </div>
            </div>

            <button onclick="window.location.reload()" class="btn btn-success w-100 fw-bold py-2 shadow-sm">
                <i class="fas fa-user-plus me-2"></i>Finalizar / Siguiente Votante
            </button>
        </div>

    </div>
</div>

<script>
    let alumnaIdSeleccionada = null;
    let listaSeleccionada = null;
    let nombreListaSeleccionada = "";
    let dniGlobal = "";
    let aulaGlobal = "";
    let nombreGlobal = "";

    function verificarAlumna() {
        const grado = document.getElementById('select-grado').value;
        const seccion = document.getElementById('select-seccion').value;
        const dni = document.getElementById('input-dni').value.trim();
        const alertError = document.getElementById('error-dni');

        alertError.classList.add('d-none');

        if (!grado || !seccion || !dni) {
            alertError.innerText = "Por favor complete todos los campos.";
            alertError.classList.remove('d-none');
            return;
        }

        const formData = new FormData();
        formData.append('grado', grado);
        formData.append('seccion', seccion);
        formData.append('documento', dni);

        fetch('api/validar_alumna.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.exito) {
                alumnaIdSeleccionada = data.id;
                dniGlobal = dni;
                nombreGlobal = data.nombres_apellidos || data.nombre || data.nombres || "Alumna Registrada";
                aulaGlobal = `${grado}° "${seccion}"`;

                // Pintamos los datos en la cabecera llamativa
                document.getElementById('lbl-nombre').innerText = nombreGlobal;
                document.getElementById('lbl-dni').innerText = dniGlobal;
                document.getElementById('lbl-aula').innerText = aulaGlobal;

                document.getElementById('paso-dni').classList.add('d-none');
                document.getElementById('paso-votacion').classList.remove('d-none');
            } else {
                alertError.innerText = data.mensaje;
                alertError.classList.remove('d-none');
            }
        })
        .catch(err => {
            alertError.innerText = "Error de comunicación con el servidor.";
            alertError.classList.remove('d-none');
        });
    }

    function volverAIdentificacion() {
        document.querySelectorAll('.lista-card').forEach(c => c.classList.remove('seleccionada'));
        listaSeleccionada = null;
        nombreListaSeleccionada = "";
        document.getElementById('btn-emitir').setAttribute('disabled', 'true');

        document.getElementById('paso-votacion').classList.add('d-none');
        document.getElementById('paso-dni').classList.remove('d-none');
    }

    function seleccionarLista(elementoCard, idLista, nombreLista) {
        document.querySelectorAll('.lista-card').forEach(c => c.classList.remove('seleccionada'));
        elementoCard.classList.add('seleccionada');
        
        listaSeleccionada = idLista;
        nombreListaSeleccionada = nombreLista;
        document.getElementById('btn-emitir').removeAttribute('disabled');
    }

    function emitirVoto() {
        if (!listaSeleccionada || !alumnaIdSeleccionada) return;

        if (!confirm("¿Está segura de confirmar su voto? Esta acción no se puede deshacer.")) return;

        const formData = new FormData();
        formData.append('alumna_id', alumnaIdSeleccionada);
        formData.append('lista_id', listaSeleccionada);

        fetch('api/registrar_voto.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.exito) {
                document.getElementById('comp-nombre').innerText = nombreGlobal;
                document.getElementById('comp-dni').innerText = dniGlobal;
                document.getElementById('comp-aula').innerText = aulaGlobal;
                document.getElementById('comp-opcion').innerText = nombreListaSeleccionada;

                document.getElementById('paso-votacion').classList.add('d-none');
                document.getElementById('paso-exito').classList.remove('d-none');
            } else {
                alert(data.mensaje);
            }
        });
    }
</script>
</body>
</html>
