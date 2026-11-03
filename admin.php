<?php
session_start();
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Votación Escolar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .card-custom { border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table-responsive { max-height: 380px; overflow-y: auto; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1"><i class="fas fa-user-shield me-2"></i>Panel Docente - Sistema Electoral</span>
        <div>
            <a href="index.php" class="btn btn-outline-light btn-sm me-2" target="_blank"><i class="fas fa-external-link-alt me-1"></i> Ir a Vista Votante</a>
            <a href="api/logout.php" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Salir</a>
        </div>
    </div>
</nav>

<div class="container pb-5">

    <!-- MÉTRICAS PRINCIPALES -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-custom p-3 bg-primary text-white">
                <h6>Total Registradas en Padrón</h6>
                <h2 id="metric-total" class="fw-bold mb-0">0</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-3 bg-success text-white">
                <h6>Votos Emitidos</h6>
                <h2 id="metric-votaron" class="fw-bold mb-0">0</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-3 bg-warning text-dark">
                <h6>Faltan Votar</h6>
                <h2 id="metric-pendientes" class="fw-bold mb-0">0</h2>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-lg-5">
            <!-- CARGA DE EXCEL -->
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-file-excel text-success me-2"></i>Cargar Padrón Escolar (Excel)</h5>
                <p class="text-muted small">Seleccione el aula correspondiente al archivo que va a subir. Lee la Columna B (DNI) y Columna C (Nombres).</p>
                
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Grado</label>
                        <select class="form-select form-select-sm" id="excel-grado">
                            <option value="1">1° de Secundaria</option>
                            <option value="2">2° de Secundaria</option>
                            <option value="3">3° de Secundaria</option>
                            <option value="4">4° de Secundaria</option>
                            <option value="5" selected>5° de Secundaria</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Sección</label>
                        <select class="form-select form-select-sm" id="excel-seccion">
                            <script>
                                const secs = ["A","B","C","D","E","F","G","H","I","J","K","L"];
                                secs.forEach(s => document.write(`<option value="${s}">${s}</option>`));
                            </script>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <input class="form-control" type="file" id="excel-input" accept=".xlsx, .xls, .csv">
                </div>

                <button class="btn btn-success w-100 fw-bold" onclick="procesarExcelConGrado()">
                    <i class="fas fa-upload me-2"></i>Procesar y Guardar Padrón
                </button>

                <div id="resultado-excel" class="alert mt-3 d-none" role="alert"></div>
            </div>

            <!-- GESTIÓN DE ELECCIONES (NUEVAS LISTAS Y ELIMINACIÓN POR AULA / TOTAL) -->
            <div class="card card-custom p-4 mb-4 border-warning">
                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-cogs text-warning me-2"></i>Gestión de Elecciones</h5>
                
                <button class="btn btn-outline-primary w-100 mb-2 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#formNuevaLista">
                    <i class="fas fa-plus-circle me-1"></i> Registrar Nueva Lista / Candidata
                </button>
                
                <div class="collapse mb-3" id="formNuevaLista">
                    <div class="card card-body bg-light">
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Nombre de la Lista</label>
                            <input type="text" id="nueva-lista-nombre" class="form-control form-control-sm" placeholder="Ej. Lista 4 - Innovación">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Representante / Alcaldesa</label>
                            <input type="text" id="nueva-lista-lider" class="form-control form-control-sm" placeholder="Nombre completo">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Foto o Logo Oficial</label>
                            <input type="file" id="nueva-lista-logo" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <button class="btn btn-primary btn-sm w-100 mt-1" onclick="guardarNuevaLista()">Guardar Lista con Foto</button>
                    </div>
                </div>

                <!-- ELIMINAR UN EXCEL / AULA ESPECÍFICA -->
                <div class="p-2 border rounded bg-light mb-2">
                    <label class="form-label small fw-bold text-danger mb-1"><i class="fas fa-trash me-1"></i>Eliminar Excel de un Aula Específica</label>
                    <div class="row g-1 mb-2">
                        <div class="col-6">
                            <select class="form-select form-select-sm" id="del-grado">
                                <option value="1">1° Grado</option>
                                <option value="2">2° Grado</option>
                                <option value="3">3° Grado</option>
                                <option value="4">4° Grado</option>
                                <option value="5">5° Grado</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <select class="form-select form-select-sm" id="del-seccion">
                                <script>
                                    secs.forEach(s => document.write(`<option value="${s}">Sección ${s}</option>`));
                                </script>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-outline-danger btn-sm w-100 fw-bold" onclick="eliminarAulaEspecifica()">
                        Eliminar Aula Seleccionada
                    </button>
                </div>

                <button class="btn btn-danger w-100 fw-bold btn-sm" onclick="vaciarPadronGeneral()">
                    <i class="fas fa-exclamation-triangle me-1"></i>Vaciar Todo el Padrón General
                </button>
            </div>

            <!-- RESULTADOS EN TIEMPO REAL -->
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-chart-pie text-primary me-2"></i>Resultados Actuales</h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="cargarResultados()"><i class="fas fa-sync-alt"></i> Actualizar</button>
                </div>
                <div id="contenedor-resultados"></div>
            </div>
        </div>

        <div class="col-lg-7">
            <!-- PADRÓN CON FILTROS POR GRADO Y SECCIÓN -->
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-users me-2"></i>Padrón de Alumnas</h5>
                </div>

                <!-- FILTROS DE CLASIFICACIÓN POR AULA -->
                <div class="row g-2 mb-3 bg-light p-2 rounded border">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold mb-1">Filtrar por Grado</label>
                        <select class="form-select form-select-sm" id="filtro-grado" onchange="aplicarFiltrosTabla()">
                            <option value="todos">Todos los Grados</option>
                            <option value="1">1° de Secundaria</option>
                            <option value="2">2° de Secundaria</option>
                            <option value="3">3° de Secundaria</option>
                            <option value="4">4° de Secundaria</option>
                            <option value="5">5° de Secundaria</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold mb-1">Sección</label>
                        <select class="form-select form-select-sm" id="filtro-seccion" onchange="aplicarFiltrosTabla()">
                            <option value="todas">Todas</option>
                            <script>
                                secs.forEach(s => document.write(`<option value="${s}">${s}</option>`));
                            </script>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold mb-1">Buscar por DNI o Nombre</label>
                        <input type="text" id="filtro-texto" class="form-control form-control-sm" placeholder="Escriba para buscar..." onkeyup="aplicarFiltrosTabla()">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tabla-padron">
                        <thead class="table-light">
                            <tr>
                                <th>N° Doc / DNI</th>
                                <th>Nombres y Apellidos</th>
                                <th>Aula</th>
                                <th>Estado Voto</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-padron">
                            <tr><td colspan="4" class="text-center text-muted">Cargando padrón...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let datosPadronGlobal = [];

    document.addEventListener("DOMContentLoaded", function() {
        cargarResultados();
    });

    function cargarResultados() {
        fetch('api/obtener_resultados.php')
        .then(res => res.json())
        .then(data => {
            if (data.exito) {
                document.getElementById('metric-total').innerText = data.total_alumnas;
                document.getElementById('metric-votaron').innerText = data.total_votaron;
                document.getElementById('metric-pendientes').innerText = data.pendientes;

                let htmlVotos = '';
                if (data.resultados.length === 0) {
                    htmlVotos = `<p class="text-muted text-center small">No hay listas registradas.</p>`;
                } else {
                    data.resultados.forEach(res => {
                        let porcentaje = data.total_votaron > 0 ? ((res.total_votos / data.total_votaron) * 100).toFixed(1) : 0;
                        htmlVotos += `
                            <div class="mb-3 border-bottom pb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">${res.nombre_lista}</span>
                                    <div>
                                        <span class="fw-bold me-2">${res.total_votos} votos (${porcentaje}%)</span>
                                        <button class="btn btn-outline-danger btn-sm py-0 px-2" onclick="eliminarLista(${res.id}, '${res.nombre_lista}')" title="Eliminar lista">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="progress" style="height: 15px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: ${porcentaje}%"></div>
                                </div>
                            </div>
                        `;
                    });
                }
                document.getElementById('contenedor-resultados').innerHTML = htmlVotos;

                datosPadronGlobal = data.padron;
                aplicarFiltrosTabla();
            }
        });
    }

    function eliminarLista(id, nombre) {
        if (confirm(`¿Estás seguro de eliminar la lista "${nombre}"? Se borrarán también sus votos.`)) {
            const formData = new FormData();
            formData.append('id', id);

            fetch('api/eliminar_lista.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.mensaje);
                if (data.exito) {
                    cargarResultados();
                }
            });
        }
    }

    function aplicarFiltrosTabla() {
        let gradoFiltro = document.getElementById('filtro-grado').value;
        let seccionFiltro = document.getElementById('filtro-seccion').value;
        let textoBusqueda = document.getElementById('filtro-texto').value.toLowerCase();

        let filtrados = datosPadronGlobal.filter(a => {
            let coincideGrado = (gradoFiltro === 'todos' || String(a.grado) === gradoFiltro);
            let coincideSeccion = (seccionFiltro === 'todas' || a.seccion.toUpperCase() === seccionFiltro.toUpperCase());
            let coincideTexto = (a.doc_identidad.toLowerCase().includes(textoBusqueda) || a.nombres_apellidos.toLowerCase().includes(textoBusqueda));

            return coincideGrado && coincideSeccion && coincideTexto;
        });

        renderizarTabla(filtrados);
    }

    function renderizarTabla(lista) {
        let htmlTabla = '';
        if (lista.length === 0) {
            htmlTabla = `<tr><td colspan="4" class="text-center text-muted">No se encontraron alumnas con los filtros seleccionados.</td></tr>`;
        } else {
            lista.forEach(a => {
                let badgeEstado = a.ya_voto == 1 
                    ? `<span class="badge bg-success"><i class="fas fa-check me-1"></i>Ya Votó</span>`
                    : `<span class="badge bg-secondary">Pendiente</span>`;
                
                htmlTabla += `
                    <tr>
                        <td><strong>${a.doc_identidad}</strong> <small class="text-muted">(${a.tipo_doc})</small></td>
                        <td>${a.nombres_apellidos}</td>
                        <td>${a.grado}° "${a.seccion}"</td>
                        <td>${badgeEstado}</td>
                    </tr>
                `;
            });
        }
        document.getElementById('tbody-padron').innerHTML = htmlTabla;
    }

    function procesarExcelConGrado() {
        const fileInput = document.getElementById('excel-input');
        const alertRes = document.getElementById('resultado-excel');
        const gradoSeleccionado = document.getElementById('excel-grado').value;
        const seccionSeleccionada = document.getElementById('excel-seccion').value;

        if (fileInput.files.length === 0) {
            alertRes.className = "alert alert-warning mt-3";
            alertRes.innerText = "Por favor seleccione un archivo Excel.";
            alertRes.classList.remove('d-none');
            return;
        }

        const file = fileInput.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });

            const firstSheet = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[firstSheet];
            const json = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

            let alumnasParaEnviar = [];
            
            // Recorrido dinámico completo desde la primera hasta la última fila real sin cortes
            for (let i = 0; i < json.length; i++) {
                let fila = json[i];
                if (!fila || fila.length === 0) continue;

                let dniStr = fila[1] !== undefined && fila[1] !== null ? String(fila[1]).trim() : "";
                let nombreStr = fila[2] !== undefined && fila[2] !== null ? String(fila[2]).trim() : "";

                // Ignorar filas de títulos o cabeceras dentro del Excel
                let lowerDni = dniStr.toLowerCase();
                let lowerNombre = nombreStr.toLowerCase();
                if (lowerDni.includes('dni') || lowerDni.includes('doc') || lowerNombre.includes('nombres') || lowerNombre.includes('apellidos')) {
                    continue;
                }

                if (dniStr !== "" && nombreStr !== "") {
                    alumnasParaEnviar.push({
                        documento: dniStr,
                        tipo_doc: dniStr.length > 8 ? 'EXTRANJERIA' : 'DNI',
                        nombres_apellidos: nombreStr,
                        grado: parseInt(gradoSeleccionado),
                        seccion: seccionSeleccionada
                    });
                }
            }

            if (alumnasParaEnviar.length === 0) {
                alertRes.className = "alert alert-danger mt-3";
                alertRes.innerText = "No se encontraron datos válidos en las columnas B (DNI) y C (Nombres). Verifique el archivo.";
                alertRes.classList.remove('d-none');
                return;
            }

            fetch('api/subir_excel.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ alumnas: alumnasParaEnviar })
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.exito) {
                    alertRes.className = "alert alert-success mt-3";
                    alertRes.innerText = `${resp.mensaje} (Aula: ${gradoSeleccionado}° "${seccionSeleccionada}")`;
                    alertRes.classList.remove('d-none');
                    fileInput.value = '';
                    cargarResultados();
                } else {
                    alertRes.className = "alert alert-danger mt-3";
                    alertRes.innerText = resp.mensaje;
                    alertRes.classList.remove('d-none');
                }
            });
        };

        reader.readAsArrayBuffer(file);
    }

    function eliminarAulaEspecifica() {
        const grado = document.getElementById('del-grado').value;
        const seccion = document.getElementById('del-seccion').value;

        if (confirm(`⚠️ ¿Deseas eliminar todo el Excel y los votos del ${grado}° "${seccion}"?`)) {
            const formData = new FormData();
            formData.append('grado', grado);
            formData.append('seccion', seccion);

            fetch('api/eliminar_aula.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.mensaje);
                if (data.exito) {
                    cargarResultados();
                }
            });
        }
    }

    function vaciarPadronGeneral() {
        if (confirm("⚠️ ¿Deseas BORRAR TODO el padrón de alumnas y reiniciar todos los votos del colegio?")) {
            fetch('api/vaciar_padron.php', { method: 'POST' })
            .then(res => res.json())
            .then(data => {
                alert(data.mensaje);
                if (data.exito) {
                    datosPadronGlobal = [];
                    renderizarTabla([]);
                    cargarResultados();
                }
            });
        }
    }

    function guardarNuevaLista() {
        const nombre = document.getElementById('nueva-lista-nombre').value.trim();
        const lider = document.getElementById('nueva-lista-lider').value.trim();
        const logoInput = document.getElementById('nueva-lista-logo');

        if (!nombre || !lider) {
            alert("Por favor ingrese el nombre de la lista y su representante.");
            return;
        }

        const formData = new FormData();
        formData.append('nombre_lista', nombre);
        formData.append('lider', lider);
        if (logoInput.files.length > 0) {
            formData.append('logo', logoInput.files[0]);
        }

        fetch('api/guardar_lista.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert(data.mensaje);
            if (data.exito) {
                document.getElementById('nueva-lista-nombre').value = '';
                document.getElementById('nueva-lista-lider').value = '';
                logoInput.value = '';
                cargarResultados();
            }
        });
    }
</script>
</body>
</html>