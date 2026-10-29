<?php
$directorio = __DIR__;
$archivos = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directorio));
$contador = 0;

foreach ($archivos as $archivo) {
    if ($archivo->getExtension() == 'php') {
        $contenido = file_get_contents($archivo->getPathname());
        if (strpos($contenido, 'class RespuestaControlador') !== false) {
            $contador++;
            echo "<p style='color: red;'>📄 " . str_replace(__DIR__, '', $archivo->getPathname()) . "</p>";
        }
    }
}

echo "<hr>";
if ($contador > 1) {
    echo "<h2 style='color: red'>❌ ERROR: Se encontraron $contador archivos con la clase RespuestaControlador</h2>";
    echo "<p>Debes eliminar los duplicados y mantener SOLO UNO en: <strong>controlador/RespuestaControlador.php</strong></p>";
} else {
    echo "<h2 style='color: green'>✅ OK: Solo hay un archivo con la clase RespuestaControlador</h2>";
}
?>