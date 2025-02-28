<?php
session_start();
include '../Conexion/conexion.php';

require_once '../vendor/autoload.php'; // Asegúrate de incluir la librería PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Función para obtener instructores desde la base de datos para la coordinación "Música y Audiovisuales"
function obtenerInstructores($conexion) {
    $query = "SELECT DISTINCT nom_instructor FROM instructores WHERE coordinacion = 'MUSICA Y AUDIOVISUALES'";
    $result = $conexion->query($query);
    $instructores = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $instructores[] = $row;
        }
    }
    return $instructores;
}

// Función para contar instructores en la coordinación "Música y Audiovisuales"
function contarInstructores($conexion) {
    $query = "SELECT COUNT(DISTINCT nom_instructor) AS total_instructores FROM instructores WHERE coordinacion = 'MUSICA Y AUDIOVISUALES'";
    $result = $conexion->query($query);
    $row = $result->fetch_assoc();
    return $row['total_instructores'];
}

// Función para exportar respuestas a Excel
if (isset($_GET['exportar_excel']) && isset($_GET['instructor_name'])) {
    $instructorName = $_GET['instructor_name'];

    $query = "
        SELECT ficha, nombre, apellidos, nom_instructor, relaciones_interpersonales, socializa_evaluar, 
               estrategias_participativas, orientacion_proyecto, incentivo_utilizacion_plataforma, 
               orientacion_guías, puntualidad, dominio_tecnico, propuestas_fuentes_consulta, 
               apoyo_tematicas_fpi, asesoramiento_plan_mejoramiento, contribucion_mejoramiento
        FROM respuestas 
        WHERE nom_instructor = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("s", $instructorName);
    $stmt->execute();
    $result = $stmt->get_result();

    // Crear un archivo Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Configurar encabezados
    $headers = [
        "Ficha", "Nombre", "Apellidos", "Instructor", "Relaciones Interpersonales", 
        "Socializa Resultados", "Estrategias Participativas", "Orientación Proyecto", 
        "Uso Territorium", "Orientación Guías", "Puntualidad", "Dominio Técnico", 
        "Fuentes Consulta", "Apoyo Temáticas FPI", "Planes Mejoramiento", "Mejoramiento Actitudinal"
    ];

    $sheet->fromArray($headers, NULL, 'A1');

    // Insertar datos
    $rowNumber = 2; // Comenzar después de los encabezados
    while ($row = $result->fetch_assoc()) {
        $sheet->fromArray(array_values($row), NULL, "A$rowNumber");
        $rowNumber++;
    }

    // Generar y descargar el archivo Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Respuestas_' . $instructorName . '.xlsx"');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}

// Si se envía el parámetro 'instructor_name', obtener las fichas de ese instructor
if (isset($_GET['instructor_name'])) {
    $instructorName = $_GET['instructor_name'];
    $stmt = $conexion->prepare("SELECT ficha FROM instructores WHERE nom_instructor = ?");
    $stmt->bind_param("s", $instructorName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $fichas = [];
    while ($row = $result->fetch_assoc()) {
        $fichas[] = $row;
    }
    echo json_encode($fichas);
    exit();
}

// Obtener el número total de instructores y la lista de instructores
$totalInstructores = contarInstructores($conexion);
$instructores = obtenerInstructores($conexion);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta de Satisfacción</title>
    <link rel="stylesheet" href="../css/Administrativos/Busqueda.css">
    <script>
    function exportarExcel() {
        const instructorSelect = document.getElementById('instructor-select');
        const instructorName = instructorSelect.value;

        if (instructorName) {
            const url = `?exportar_excel=true&instructor_name=${encodeURIComponent(instructorName)}`;
            window.location.href = url;
        } else {
            alert('Por favor, selecciona un instructor antes de exportar.');
        }
    }

    function handleInstructorChange(event) {
        const instructorName = event.target.value;
        const graphButton = document.getElementById('graph-button');

        if (instructorName) {
            // Mostrar el botón de gráficas y actualizar el enlace con los parámetros
            graphButton.style.display = 'inline-block';
            graphButton.href = `Graficas.php?instructor_name=${encodeURIComponent(instructorName)}`;
            
            fetch(`?instructor_name=${encodeURIComponent(instructorName)}`)
                .then(response => response.json())
                .then(fichas => {
                    const fichasContainer = document.querySelector('.fichas-list');
                    fichasContainer.innerHTML = ''; // Limpiar fichas anteriores

                    fichas.forEach(ficha => {
                        const fichaItem = document.createElement('div');
                        fichaItem.className = 'ficha-item';
                        fichaItem.innerHTML =  
                            `<button type="button" onclick="window.location.href='detalles_ficha.php?instructor_name=${encodeURIComponent(instructorName)}&ficha=${encodeURIComponent(ficha.ficha)}'">
                                Ficha: ${ficha.ficha}
                            </button>`;
                        fichasContainer.appendChild(fichaItem);
                    });
                })
                .catch(error => console.error('Error al cargar las fichas:', error));
        } else {
            // Ocultar el botón de gráficas si no hay un instructor seleccionado
            graphButton.style.display = 'none';
            document.querySelector('.fichas-list').innerHTML = '';
        }
    }
    </script>
</head>
<body>
    <div class="main-container">
        <nav class="navbar">
            <div class="navbar-container"></div>
        </nav>
        <div class="encuesta-container">
            <div class="encuesta-box">
                <h1>Apartado Administrativo</h1>
                <div class="total-instructores">
                    <p>Total de Instructores en Música y Audiovisuales: <?php echo $totalInstructores; ?></p>
                </div>

                <form id="instructor-form">
                    <div class="dropdown-container">
                        <label for="instructor-select">Seleccionar Instructor</label>
                        <select id="instructor-select" onchange="handleInstructorChange(event)">
                            <option value="">Seleccione un instructor</option>
                            <?php foreach ($instructores as $instructor): ?>
                                <option value="<?php echo $instructor['nom_instructor']; ?>"><?php echo $instructor['nom_instructor']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>

                <button type="button" onclick="exportarExcel()">Exportar Respuestas a Excel</button>

                <!-- Botón de gráficas -->
                <a href="#" id="graph-button" class="graph-button" style="display: none;">Ver Gráficas</a>

                <div class="fichas-list"></div>
            </div>
        </div>
        <style>
        /* Estilos para posicionar el botón de gráficas */
        .encuesta-box {
            position: relative; /* Necesario para el posicionamiento absoluto dentro de este contenedor */
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .graph-button {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .graph-button:hover {
            background-color: #0056b3;
        }
        </style>
    </div>
</body>
</html>