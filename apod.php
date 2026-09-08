<?php
/**
 * Obtiene la imagen astronómica del día desde la API de la nasa.
 *
 * @param string $fecha Fecha en formato YYYY-MM-DD. Si está vacía se usa la de hoy
 * @return object|null Objeto con los datos del APOD, o null si no se encuentra.
 */

if (file_exists(__DIR__ . '/config.php')) {
    require_once 'config.php';
    $apiKey = NASA_API_KEY;
} else {
    $apiKey = 'DEMO_KEY';
}

function obtenerApod($fecha, $apiKey) {
    $url  = 'https://api.nasa.gov/planetary/apod?api_key=' . $apiKey;

    if (!empty($fecha)) {
        $url .= '&date=' . $fecha;
    }

    $json = file_get_contents($url);

    if ($json === false) {
        return null;
    }

    return json_decode($json);
}

if (isset($_POST['fecha'])) {
    $fecha = $_POST['fecha'];
    $datos  = obtenerApod($fecha, $apiKey);

    if ($datos && isset($datos->title)) {
        $titulo = $datos->title;
        $explicacion = $datos->explanation;
        $tipoMedia = $datos->media_type;
        $urlMedia = $datos->url;
        $fechaMostrada = $datos->date;
    } else {
        $error = "No se ha podido encontrar una imagen para esa fecha.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NASA - Imagen Astronómica del Día</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<header>
    <h1>Imagen astronómica del día</h1>
    <p>Elige una fecha y descubre qué imagen o vídeo destacó la NASA ese día.</p>
</header>

<nav>
    <a href="index.php">← Volver al inicio</a>
</nav>

<form method="POST" action="apod.php">
    <div class="search-box">
        <input type="date" name="fecha" min="1995-06-16" max="<?php echo date('Y-m-d'); ?>">
        <button type="submit">Buscar</button>
    </div>
</form>

<?php
if (isset($error)) {
    echo '<p class="error">' . $error . '</p>';
}

if (isset($datos) && isset($datos->title)) {
    echo '<div class="card">';

    if ($tipoMedia === 'image') {
        echo '<img class="avatar" src="' . $urlMedia . '" alt="' . $titulo . '">';
    } else {
        echo '<iframe src="' . $urlMedia . '" allowfullscreen></iframe>';
    }

    echo '<h2>' . $titulo . '</h2>';
    echo '<p class="subtitulo">' . $fechaMostrada . '</p>';
    echo '<p>' . $explicacion . '</p>';
    echo '</div>';
}
?>

</body>
</html>