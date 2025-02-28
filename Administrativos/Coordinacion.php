<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinación</title>
    <link rel="stylesheet" href="../css/Administrativos/Coordinacion.css"> <!-- Asegúrate de que la ruta sea correcta -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Incluye SweetAlert2 -->
</head>
<body>
    <div class="main-container">
        <nav class="navbar">
            <div class="navbar-container">
                <!-- Aquí podrías agregar contenido adicional en el navbar -->
            </div>
        </nav>
        <br />
        <div class="coordinacion-container">
            <h1>Seleccione una Coordinación</h1>
            <div class="coordinacion-images">
                <div class="coordinacion-item" onclick="window.location.href='../Administrativos/Artes_Escenicas.php'">
                    <img src="../img/Artes_Escenicas.jpeg" alt="Artes Escénicas" />
                    <h2>Artes Escénicas</h2>
                </div>
                <div class="coordinacion-item" onclick="window.location.href='../Administrativos/Musica_Audiovisuales.php'">
                    <img src="../img/Musica.jpeg" alt="Música" />
                    <h2>Música</h2>
                </div>
                <div class="coordinacion-item" onclick="window.location.href='../Administrativos/Deportes.php'">
                    <img src="../img/Deportes.png" alt="Danzas" />
                    <h2>Deportes</h2>
                </div>
            </div>
        </div>
    </div>

    <script>
        function handleClick(coord) {
            console.log(`Navegando a ${coord}`);
            // Redirige a la ruta correspondiente
            window.location.href = `/${coord}`;
        }
    </script>
</body>
</html>
