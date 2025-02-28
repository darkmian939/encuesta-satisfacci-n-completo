<?php
session_start();
include 'Conexion/conexion.php'; // Asegúrate de que la ruta es correcta

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $coordinacion = $_POST['coordinacion'];

    // Cifrar la contraseña
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Consulta SQL para insertar el administrador
    $sql = "INSERT INTO administrativos (nombre, correo, contrasena, coordinacion)
            VALUES ('$nombre', '$correo', '$contrasena_hash', '$coordinacion')";

    if ($conexion->query($sql) === TRUE) {
        echo "Administrador agregado exitosamente.";
    } else {
        echo "Error: " . $conexion->error;
    }

    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Administrador</title>
</head>
<body>
    <h1>Agregar Administrador</h1>
    <form action="creaciondeadmin.php" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" required><br><br>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required><br><br>

        <label for="coordinacion">Coordinación:</label>
        <input type="text" id="coordinacion" name="coordinacion" required><br><br>

        <button type="submit">Agregar Administrador</button>
    </form>
</body>
</html>
