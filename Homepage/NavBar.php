<?php
session_start(); 

include '../Conexion/conexion.php';

$alertMessage = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputValue = $_POST['document_input'];

   
    $sql = "SELECT * FROM aprendices WHERE `Número_De_Documento` = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $inputValue);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
       
        $aprendiz = $result->fetch_assoc();
        $_SESSION['aprendiz'] = $aprendiz; 

        header("Location: ../Encuesta/encuesta.php");
        exit();
    } else {
        $alertMessage = "Documento inválido, intenta nuevamente.";
    }

    $stmt->close();
    $conexion->close();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta de Satisfacción</title>
    <link rel="stylesheet" href="../css/home/Nav-register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial, sans-serif';
            background: #39a900;
            color: #fff;
            overflow-x: hidden;
        }

        /* Input Container */
        .input-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        /* Document Input */
        .document-input {
            padding: 0.5rem;
            font-size: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #000000;
            text-align: center;
            width: 250px;
        }

        /* Button Submit Style */
        .Button-submit {
            background-color: #2bd830;
            color: #fff;
            font-size: 1.2rem;
            font-weight: bold;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* Animation for Check Icon */
        .check-icon {
            display: inline-block;
            font-size: 1.5rem;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        /* Animation on Click */
        .Button-submit:active .check-icon {
            transform: rotate(360deg) scale(1.2);
            color: #00FF00;
        }

        /* Hover Effect for Submit Button */
        .Button-submit:hover {
            background-color: #00FF00;
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.3);
        }

            
        .alert {
            display: none;
            margin: 1rem 0;
            padding: 1rem;
            background-color: #f44336; 
            color: white; 
            border-radius: 5px; 
            text-align: center;
        }

        /* Show alert */
        .alert.show {
            display: block; 
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .document-input {
                width: 100%;
                font-size: 1rem;
            }
            .Button-submit {
                font-size: 1rem;
                padding: 0.5rem 1rem;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="#home" class="navbar-logo">
                <!-- Logo -->
            </a>
        </div>
    </nav>

    <!-- Banner -->
    <section class="banner" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="admin-button-container">
                        <button class="admin-button" onclick="window.location.href='../Administrativos/administrativos.php'">
                            <h2>Administrativo</h2>
                        </button>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-7">
                    <h1>Encuesta de Satisfacción</h1>
                    <h1>¡Bienvenidos!</h1>
                    <p>
                        En nuestra comunidad, creemos en el poder de la colaboración, la creatividad y el aprendizaje continuo.
                        Nos esforzamos por crear un entorno donde todos puedan compartir sus conocimientos, desarrollar nuevas habilidades
                        y conectarse con personas de ideas afines.
                    </p>

                            
<!-- Alert for Invalid Document -->
<?php if ($alertMessage): ?>
    <div class="alert show" id="alertMessage">
        <?php echo $alertMessage; ?>
    </div>
<?php endif; ?>

<script>
    // Función para ocultar la alerta después de un tiempo
    document.addEventListener('DOMContentLoaded', function () {
        var alertMessage = document.getElementById('alertMessage');
        if (alertMessage) {
            setTimeout(function() {
                alertMessage.classList.remove('show'); // Remueve la clase para ocultar la alerta
            }, 1000); // 5000 milisegundos = 5 segundos
        }
    });
</script>

                    <form method="POST" action="">
                        <div class="input-container">
                            <label for="document-input">Digite su número de identificación:</label>
                            <input
                                type="text"
                                id="document-input"
                                class="document-input"
                                placeholder="Número de Documento"
                                name="document_input"
                                required
                            />
                            <button type="submit" class="Button-submit">
                                <span class="check-icon">&#x2714;</span> <!-- Ícono de check con animación -->
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3 footer-col">
                    <h5>Contacto</h5>
                    <ul class="contact-list">
                        <li>
                            <span>Correo Electrónico: jdrincon@sena.edu.co</span>
                        </li>
                        <li>
                            <span>Transversal 78J N° 41D - 15 Sur - Kennedy - Bogotá D.C.</span>
                        </li>
                    </ul>
                </div>
                <div class="col-12 col-sm-6 col-md-3 footer-col">
                    <p class="rights-text">Derechos reservados SENA (Centro De Formación en Actividad Física y Cultura)</p>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
