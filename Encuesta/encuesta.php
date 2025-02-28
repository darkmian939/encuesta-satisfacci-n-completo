<?php
session_start();
include '../Conexion/conexion.php';

// Verificar si el aprendiz está autenticado
if (!isset($_SESSION['aprendiz'])) {
    header("Location: ../index.php");
    exit();
}

$aprendiz = $_SESSION['aprendiz'];
$fichaAprendiz = $aprendiz['Ficha'];

// Guardar el instructor seleccionado en la sesión
if (isset($_POST['instructor']) && !empty($_POST['instructor'])) {
    $_SESSION['selected_instructor'] = $_POST['instructor'];
}

// Obtener el nombre del aprendiz actual
$nombre_aprendiz = $aprendiz['Nombre']; // Asegúrate de que 'Nombre' corresponde al nombre real del aprendiz

// Obtener instructores que ya han sido calificados por el aprendiz
$sql_calificaciones = "SELECT nom_instructor FROM respuestas WHERE nombre = ?";
$stmt_calificaciones = $conexion->prepare($sql_calificaciones);
$stmt_calificaciones->bind_param("s", $nombre_aprendiz);
$stmt_calificaciones->execute();
$result_calificaciones = $stmt_calificaciones->get_result();

$instructores_calificados = [];
if ($result_calificaciones->num_rows > 0) {
    while ($row = $result_calificaciones->fetch_assoc()) {
        $instructores_calificados[] = $row['nom_instructor'];
    }
}

// Obtener instructores disponibles para calificación
$sql_instructores = "SELECT nom_instructor FROM instructores WHERE ficha = ?";
$stmt = $conexion->prepare($sql_instructores);
$stmt->bind_param("s", $fichaAprendiz);
$stmt->execute();
$result_instructores = $stmt->get_result();

$instructores = [];
if ($result_instructores->num_rows > 0) {
    while ($row = $result_instructores->fetch_assoc()) {
        $instructores[] = $row['nom_instructor'];
    }
} else {
    $alertMessage = "No se encontraron instructores asociados a la ficha.";
}

// Procesar el formulario de respuestas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ficha'])) {
    // Aquí va la lógica para guardar respuestas en la tabla `respuestas`
}

// Cerrar las conexiones
$stmt_calificaciones->close();
$stmt->close();

// Procesar el formulario de respuestas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ficha'])) {
    $ficha = $_POST['ficha'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $nomInstructor = $_SESSION['selected_instructor'];
    $feedback = $_POST['feedback'];

    // Recolectar las respuestas de las preguntas
    $respuestas = [];
    $questions = [
        "El Instructor establece relaciones interpersonales cordiales, armoniosas, respetuosas",
        "El Instructor socializa, desarrolla y evalúa la totalidad de los resultados de aprendizaje programados para el semestre",
        "El instructor aplica estrategias participativas de trabajo en equipo que le permiten estar activo permanentemente en su proceso de aprendizaje",
        "El Instructor le orienta su formación mediante un proyecto formativo",
        "El Instructor incentiva al aprendiz a utilizar la plataforma Zajuna en el desarrollo de las actividades de aprendizaje",
        "El instructor orienta la formación por medio de guías teniendo en cuenta el proyecto formativo",
        "El Instructor es puntual al iniciar las sesiones",
        "El Instructor demuestra dominio técnico",
        "El Instructor le propone fuentes de consulta (bibliografía, webgrafía…) y ayudas que facilitan su proceso de aprendizaje",
        "El instructor brinda apoyo sobre temáticas del FPI (Formación Profesional Integral) cuando el aprendiz lo requiere y es comprensivo frente a dificultades",
        "El Instructor revisa y asesora los planes de mejoramiento",
        "El instructor contribuye al mejoramiento actitudinal del aprendiz en su proceso de formación"
    ];

    foreach ($questions as $index => $question) {
        $respuestaKey = "question-" . $index;
        $respuestas[] = isset($_POST[$respuestaKey]) ? $_POST[$respuestaKey] : null;
    }

    // Preparar la consulta SQL
    $sql_insert = "INSERT INTO respuestas (ficha, nombre, apellidos, nom_instructor, relaciones_interpersonales, socializa_evaluar, estrategias_participativas, orientacion_proyecto, incentivo_utilizacion_plataforma, orientacion_guías, puntualidad, dominio_tecnico, propuestas_fuentes_consulta, apoyo_tematicas_fpi, asesoramiento_plan_mejoramiento, contribucion_mejoramiento, feedback) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_insert = $conexion->prepare($sql_insert);
    
    // Vincular los parámetros
    $stmt_insert->bind_param("sssssssssssssssss",
        $ficha,
        $nombre,
        $apellidos,
        $nomInstructor,
        $respuestas[0],
        $respuestas[1],
        $respuestas[2],
        $respuestas[3],
        $respuestas[4],
        $respuestas[5],
        $respuestas[6],
        $respuestas[7],
        $respuestas[8],
        $respuestas[9],
        $respuestas[10],
        $respuestas[11],
        $feedback
    );

    // Ejecutar la consulta
if ($stmt_insert->execute()) {
    $_SESSION['success_message'] = "Respuestas guardadas exitosamente.";
    $_SESSION['instructores_calificados'][] = $nomInstructor; // Añadir el instructor calificado
    unset($_SESSION['selected_instructor']);
    
    // Redireccionar para refrescar la página
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
} else {
    $_SESSION['success_message'] = "Error al guardar las respuestas: " . $stmt_insert->error;
}

    // Cerrar la declaración
    $stmt_insert->close();
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/encuesta/encuesta.css">
    <title>Encuesta</title>
</head>
<body>
    <?php if (isset($_SESSION['success_message'])): ?>
        <script>
            alert("<?php echo $_SESSION['success_message']; ?>");
        </script>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <nav class="navbar">
        <div class="navbar-container">
            <a href="#home" class="navbar-logo">
                <!-- Logo -->
            </a>
        </div>
    </nav>

    <br><br><br>
    <br><br><br>

    <div class="main-container">
        <div class="encuesta-container" style="margin-top: 20px; padding: 20px;">
            <div class="content">
                <h1>Bienvenido Aprendiz</h1>
                <p class="survey-instruction">
                    Por favor desarrolla la totalidad de la encuesta con la mayor sinceridad posible. Le recordamos que sus respuestas serán manejadas bajo el principio de confidencialidad.
                </p>

                <form method="POST" action="">
    <div class="input-container">
        <label for="instructor-select">Seleccione su instructor:</label>
        <select id="instructor-select" name="instructor" required onchange="this.form.submit()">
            <option value="">Seleccione un instructor</option>
            <?php foreach ($instructores as $instructor): ?>
                <option value="<?php echo htmlspecialchars($instructor); ?>"
                    <?php echo (in_array($instructor, $instructores_calificados)) ? 'disabled' : ''; ?>
                    <?php echo isset($_SESSION['selected_instructor']) && $_SESSION['selected_instructor'] == $instructor ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($instructor); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</form>


                <?php if (!empty($_SESSION['selected_instructor'])): ?>
                <form method="POST">
                    <input type="hidden" name="ficha" value="<?php echo htmlspecialchars($aprendiz['Ficha']); ?>">
                    <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($aprendiz['Nombre']); ?>">
                    <input type="hidden" name="apellidos" value="<?php echo htmlspecialchars($aprendiz['Apellido']); ?>">
                    <h3>Valoración para <?php echo htmlspecialchars($_SESSION['selected_instructor']); ?></h3>
                    <?php
                    // Definir las preguntas aquí para que estén disponibles en el formulario
                    $questions = [
                        "El Instructor establece relaciones interpersonales cordiales, armoniosas, respetuosas",
                        "El Instructor socializa, desarrolla y evalúa la totalidad de los resultados de aprendizaje programados para el semestre",
                        "El instructor aplica estrategias participativas de trabajo en equipo que le permiten estar activo permanentemente en su proceso de aprendizaje",
                        "El Instructor le orienta su formación mediante un proyecto formativo",
                        "El Instructor incentiva al aprendiz a utilizar la plataforma Zajuna en el desarrollo de las actividades de aprendizaje",
                        "El instructor orienta la formación por medio de guías teniendo en cuenta el proyecto formativo",
                        "El Instructor es puntual al iniciar las sesiones",
                        "El Instructor demuestra dominio técnico",
                        "El Instructor le propone fuentes de consulta (bibliografía, webgrafía…) y ayudas que facilitan su proceso de aprendizaje",
                        "El instructor brinda apoyo sobre temáticas del FPI (Formación Profesional Integral) cuando el aprendiz lo requiere y es comprensivo frente a dificultades",
                        "El Instructor revisa y asesora los planes de mejoramiento",
                        "El instructor contribuye al mejoramiento actitudinal del aprendiz en su proceso de formación"
                    ];

                    // Generar las preguntas dinámicamente
                    foreach ($questions as $index => $question) {
                        echo "<div class='form-group'>";
                        echo "<label for='question-$index'>$question</label>";
                        echo "<select id='question-$index' name='question-$index' required>";
                        echo "<option value=''>Seleccione una opción</option>";
                        echo "<option value='Muy Satisfecho'>Muy Satisfecho</option>";
                        echo "<option value='Satisfecho'>Satisfecho</option>";
                        echo "<option value='Neutro'>Neutro</option>";
                        echo "<option value='Insatisfecho'>Insatisfecho</option>";
                        echo "<option value='Muy Insatisfecho'>Muy Insatisfecho</option>";
                        echo "</select>";
                        echo "</div>";
                    }
                    ?>
                    <div class="form-group">
                        <label for="feedback">Comentarios adicionales:</label>
                        <textarea id="feedback" name="feedback" rows="4" cols="50" placeholder="Escribe tus comentarios aquí..."></textarea>
                    </div>
                    <button type="submit">Enviar Respuestas</button>
                </form>
                <?php endif; ?>
            </div>
            <button onclick="window.location.href='../Homepage/NavBar.php'">Volver a la Página Principal</button>
            </div>
    </div>
</body>
</html>
