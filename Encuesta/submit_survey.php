<?php
session_start();
include '../Conexion/conexion.php';

if (!isset($_SESSION['aprendiz'])) {
    header("Location: ../index.php");
    exit();
}

// Verifica que se reciban todos los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Comprueba si se han recibido los datos necesarios
    if (!isset($_POST['ficha'], $_POST['nombre'], $_POST['apellidos'], $_POST['instructor'], $_POST['feedback'])) {
        die('Error: Se requieren todos los datos del formulario.');
    }

    // Recolectar respuestas
    $respuestas = [];
    for ($i = 0; $i < 12; $i++) {
        if (isset($_POST["question-$i"])) {
            $respuestas[] = $_POST["question-$i"];
        }
    }

    // Verifica que todas las respuestas estén definidas
    if (count($respuestas) < 12) {
        die('Error: Se requieren al menos 12 respuestas.');
    }

    // Datos del aprendiz
    $ficha = $_POST['ficha'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $nom_instructor = $_POST['instructor'];
    $feedback = $_POST['feedback'];

    // Prepare statement para insertar datos en la base de datos
    $sql = "INSERT INTO encuestas (ficha, nombre, apellidos, nom_instructor, feedback, respuesta1, respuesta2, respuesta3, respuesta4, respuesta5, respuesta6, respuesta7, respuesta8, respuesta9, respuesta10, respuesta11, respuesta12, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        die('Error: ' . $conexion->error);
    }

    // Vincula los parámetros
    $stmt->bind_param(
        'sssssssssssssssss',
        $ficha,
        $nombre,
        $apellidos,
        $nom_instructor,
        $feedback,
        ...$respuestas // Usa el operador de expansión para pasar las respuestas
    );

    // Ejecuta la consulta
    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Encuesta enviada correctamente.';
        header("Location: ../Homepage/NavBar.php");
        exit();
    } else {
        die('Error al enviar la encuesta: ' . $stmt->error);
    }

    $stmt->close();
}

$conexion->close();
?>
