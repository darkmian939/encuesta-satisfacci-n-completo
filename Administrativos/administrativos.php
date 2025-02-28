<?php
session_start();
include '../Conexion/conexion.php';

function authenticateUser($email, $password, $conexion) {
    $sql = "SELECT * FROM administrativos WHERE correo = ?";
    if ($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['contrasena'])) {
                return $user;
            }
        }
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['correo'];
    $password = $_POST['contrasena'];
    $user = authenticateUser($email, $password, $conexion);

    if ($user) {
        // Guarda los datos en la sesión
        $_SESSION['user_id'] = $user['_id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_correo'] = $user['correo'];
        $_SESSION['user_coordinacion'] = $user['coordinacion'];
        
        // Redirige según la coordinación del usuario
        switch ($user['coordinacion']) {
            case 'DEPORTES':
                header("Location: deportes.php");
                break;
            case 'ARTES ESCÉNICAS':
                header("Location: artes_escenicas.php");
                break;
            case 'MUSICA Y AUDIOVISUALES':
                header("Location: musica_audiovisuales.php");
                break;
            default:
                // Si no coincide con ninguna coordinación, redirige a Coordinacion.php
                header("Location: Coordinacion.php");
                break;
        }
        exit();
    } else {
        $error_message = 'Credenciales incorrectas';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../css/Administrativos/administrativos.css">
</head>
<body>
    <div class="main-container">
        <nav class="navbar">
            <div class="navbar-container">
                <!-- Contenido adicional del navbar -->
            </div>
        </nav>
        <br /><br />
        <div class="login-container">
            <div class="login-form">
                <h2>Iniciar Sesión</h2>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="correo" placeholder="Ingrese su email" required />
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="contrasena" placeholder="Ingrese su contraseña" required />
                    </div>
                    <button type="submit" class="Button-submit">Iniciar Sesión</button>
                </form>
                <?php if (!empty($error_message)): ?>
                    <p style="color: red;"><?php echo $error_message; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
