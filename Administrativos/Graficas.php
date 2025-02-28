<?php
session_start();
include '../Conexion/conexion.php';

// Verificar si se recibió el parámetro del nombre del instructor
if (isset($_GET['instructor_name'])) {
    $instructorName = $_GET['instructor_name'];

    // Preparar y ejecutar la consulta
    $stmt = $conexion->prepare("
        SELECT relaciones_interpersonales, socializa_evaluar, estrategias_participativas, 
               orientacion_proyecto, incentivo_utilizacion_plataforma, orientacion_guías, 
               puntualidad, dominio_tecnico, propuestas_fuentes_consulta, apoyo_tematicas_fpi, 
               asesoramiento_plan_mejoramiento, contribucion_mejoramiento
        FROM respuestas 
        WHERE nom_instructor = ?
    ");
    $stmt->bind_param("s", $instructorName);
    $stmt->execute();
    $result = $stmt->get_result();

    // Procesar los resultados
    if ($result->num_rows > 0) {
        $respuestas = [];
        while ($row = $result->fetch_assoc()) {
            $respuestas[] = $row;
        }
    } else {
        $respuestas = []; // No hay respuestas
    }
} else {
    echo "No se proporcionó el nombre del instructor.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficas de Satisfacción</title>
    <link rel="stylesheet" href="../css/Administrativos/Detalles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        canvas {
            margin: 20px auto;
            display: block;
            max-width: 90%;
            height: 500px;
        }

        .chart-container {
            text-align: center;
            padding: 20px;
            background-color: #f3f4f6;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .chart-title {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #333;
            font-weight: bold;
        }

        .questions-container {
            margin-top: 30px;
            text-align: left;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .questions-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #333;
            font-weight: bold;
            text-align: center;
        }

        .questions-container ol li {
            margin-bottom: 10px;
            font-size: 1.2rem;
            color: #555;
            line-height: 1.6;
        }

        .questions-container ol li::before {
            content: "• ";
            color: #007BFF;
            font-weight: bold;
        }

        .instructor-name {
            font-size: 1.8rem;
            color: #007BFF;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="main-container">
    <nav class="navbar">
        <div class="navbar-container"></div>
    </nav>
    <div class="chart-container">
        <h1 class="chart-title">Gráficas de Satisfacción</h1>
        <p class="instructor-name">Instructor: <?php echo htmlspecialchars($instructorName); ?></p>

        <canvas id="chart1"></canvas>
        <div class="questions-container">
            <h2 class="questions-title">Preguntas Evaluadas</h2>
            <ol>
                <li>¿El instructor establece relaciones interpersonales cordiales, armoniosas y respetuosas?</li>
                <li>¿El instructor socializa, desarrolla y evalúa la totalidad de los resultados de aprendizaje programados para el semestre?</li>
                <li>¿El instructor aplica estrategias participativas de trabajo en equipo que le permiten estar activo permanentemente en su proceso de aprendizaje?</li>
                <li>¿El instructor le orienta su formación mediante un proyecto formativo?</li>
                <li>¿El instructor incentiva al aprendiz a utilizar las plataformas institucionales en el desarrollo de las actividades de aprendizaje?</li>
                <li>¿El instructor orienta la formación por medio de guías de aprendizaje teniendo en cuenta el proyecto formativo?</li>
                <li>¿El instructor cumple a cabalidad con la programación establecida por las coordinaciones?</li>
                <li>¿El instructor demuestra dominio sobre la competencia asignada y cumple con la misma?</li>
                <li>¿El instructor le propone fuentes de consulta (bibliografía, webgrafía…) y ayudas que facilitan su proceso de aprendizaje?</li>
                <li>¿El instructor brinda apoyo sobre temáticas de la FPI cuando el aprendiz lo requiere y es comprensivo frente a dificultades personales?</li>
                <li>¿El instructor revisa y asesora los planes de mejoramiento?</li>
                <li>¿El instructor contribuye al mejoramiento actitudinal del aprendiz en su proceso de formación?</li>
            </ol>
        </div>
    </div>
</div>

<script>
    const data = <?php echo json_encode($respuestas); ?>;
    const ratingScale = {
        "Muy Satisfecho": 5,
        "Satisfecho": 4,
        "Neutro": 3,
        "Insatisfecho": 2,
        "Muy Insatisfecho": 1
    };

    const labels = Array.from({ length: 12 }, (_, i) => `Pregunta ${i + 1}`);

    let averages;

    if (data.length === 0) {
        averages = Array(12).fill(0);
    } else {
        averages = labels.map((_, index) => {
            const sum = data.reduce((acc, item) => {
                const keys = Object.keys(item);
                const rating = keys[index];
                return acc + (ratingScale[item[rating]] || 0);
            }, 0);
            return data.length ? (sum / data.length) : 0;
        });
    }

    const ctx = document.getElementById('chart1').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: data.length ? 'Promedio' : 'No hay datos disponibles',
                data: averages,
                backgroundColor: data.length ? "#36A2EB" : "#E0E0E0",
                borderColor: data.length ? "#246AAD" : "#BDBDBD",
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: "Preguntas"
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 5,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return value;
                        }
                    },
                    title: {
                        display: true,
                        text: "Promedio de Respuestas"
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return data.length
                                ? `Promedio: ${context.raw}`
                                : 'No hay datos para mostrar.';
                        }
                    }
                }
            }
        }
    });
</script>
</body>
</html>
