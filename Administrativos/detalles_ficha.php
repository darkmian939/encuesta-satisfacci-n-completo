<?php
session_start();
include '../Conexion/conexion.php';
require '../vendor/autoload.php'; // Asegúrate de instalar PHPSpreadsheet usando Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Verificar si se recibieron los parámetros necesarios
if (isset($_GET['instructor_name']) && isset($_GET['ficha'])) {
    $instructorName = $_GET['instructor_name'];
    $ficha = $_GET['ficha'];

    // Obtener la información del instructor y la ficha
    $stmt = $conexion->prepare("
        SELECT ficha, nombre, apellidos, nom_instructor, relaciones_interpersonales, socializa_evaluar, 
               estrategias_participativas, orientacion_proyecto, incentivo_utilizacion_plataforma, 
               orientacion_guías, puntualidad, dominio_tecnico, propuestas_fuentes_consulta, 
               apoyo_tematicas_fpi, asesoramiento_plan_mejoramiento, contribucion_mejoramiento
        FROM respuestas 
        WHERE nom_instructor = ? AND ficha = ?
    ");
    $stmt->bind_param("ss", $instructorName, $ficha);
    $stmt->execute();
    $result = $stmt->get_result();

    $respuestas = [];
    while ($row = $result->fetch_assoc()) {
        $respuestas[] = $row;
    }
} else {
    echo "No se encontraron los parámetros necesarios.";
    exit();
}

// Función para exportar a Excel
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Encabezados
    $headers = [
        'Ficha', 'Nombre', 'Apellidos', 'Instructor', 'Relaciones Interpersonales',
        'Socializa Resultados', 'Estrategias Participativas', 'Orientación mediante Proyecto',
        'Uso de Territorium', 'Orientación por Guías', 'Puntualidad', 'Dominio Técnico',
        'Fuentes de Consulta', 'Apoyo Temáticas FPI', 'Planes de Mejoramiento', 'Mejoramiento Actitudinal'
    ];

    $sheet->fromArray($headers, NULL, 'A1');

    // Datos
    $rowNumber = 2; // Comienza después del encabezado
    foreach ($respuestas as $respuesta) {
        $sheet->fromArray(array_values($respuesta), NULL, 'A' . $rowNumber);
        $rowNumber++;
    }

    // Configurar archivo para descarga
    $filename = "respuestas_ficha_{$ficha}_instructor_{$instructorName}.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta de Satisfacción</title>
    <link rel="stylesheet" href="../css/Administrativos/Detalles.css">
</head>
<body>
<div class="main-container">
    <nav class="navbar">
        <div class="navbar-container"></div>
    </nav>
    <br><br><br>
    <div class="encuesta-container">
        <h1>Detalles de Ficha</h1>

        <p>Has seleccionado al instructor: <?php echo htmlspecialchars($instructorName); ?> y la ficha: <?php echo htmlspecialchars($ficha); ?></p>

        <?php if (count($respuestas) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Ficha</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Instructor</th>
                            <th>Relaciones interpersonales</th>
                            <th>Socializa resultados</th>
                            <th>Estrategias participativas</th>
                            <th>Orientación mediante proyecto</th>
                            <th>Uso de Territorium</th>
                            <th>Orientación por guías</th>
                            <th>Puntualidad</th>
                            <th>Dominio técnico</th>
                            <th>Fuentes de consulta</th>
                            <th>Apoyo temáticas FPI</th>
                            <th>Planes de mejoramiento</th>
                            <th>Mejoramiento actitudinal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($respuestas as $respuesta): ?>
                            <tr>
                                <td><?php echo $respuesta['ficha']; ?></td>
                                <td><?php echo $respuesta['nombre']; ?></td>
                                <td><?php echo $respuesta['apellidos']; ?></td>
                                <td><?php echo $respuesta['nom_instructor']; ?></td>
                                <td><?php echo $respuesta['relaciones_interpersonales']; ?></td>
                                <td><?php echo $respuesta['socializa_evaluar']; ?></td>
                                <td><?php echo $respuesta['estrategias_participativas']; ?></td>
                                <td><?php echo $respuesta['orientacion_proyecto']; ?></td>
                                <td><?php echo $respuesta['incentivo_utilizacion_plataforma']; ?></td>
                                <td><?php echo $respuesta['orientacion_guías']; ?></td>
                                <td><?php echo $respuesta['puntualidad']; ?></td>
                                <td><?php echo $respuesta['dominio_tecnico']; ?></td>
                                <td><?php echo $respuesta['propuestas_fuentes_consulta']; ?></td>
                                <td><?php echo $respuesta['apoyo_tematicas_fpi']; ?></td>
                                <td><?php echo $respuesta['asesoramiento_plan_mejoramiento']; ?></td>
                                <td><?php echo $respuesta['contribucion_mejoramiento']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Botón para exportar a Excel -->
            <form method="GET" action="">
                <input type="hidden" name="instructor_name" value="<?php echo htmlspecialchars($instructorName); ?>">
                <input type="hidden" name="ficha" value="<?php echo htmlspecialchars($ficha); ?>">
                <input type="hidden" name="export" value="excel">
                <br><br>
                <button type="submit" class="export-button">Exportar a Excel</button>
            </form>
        <?php else: ?>
            <p>No se encontraron respuestas para esta ficha e instructor.</p>
        <?php endif; ?>

        <!-- Botón para ir a gráficas -->
        <br><br>
        </div>
</div>

<style>
    .graph-button {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        text-align: center;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    .graph-button:hover {
        background-color: #0056b3;
    }
</style>
</body>
</html>

