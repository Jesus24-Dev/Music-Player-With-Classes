<?php
// Especificar el directorio que quieres listar
$directorio = "./uploads";

// Comprobar si el directorio existe
if (is_dir($directorio)) {
    // Abrir el directorio
    if ($dh = opendir($directorio)) {
        // Inicializar un array para almacenar los archivos
        $archivos = [];

        // Leer el contenido del directorio
        while (($archivo = readdir($dh)) !== false) {
            // Excluir los directorios '.' y '..'
            if ($archivo != "." && $archivo != "..") {
                $archivos[] = $archivo;
            }
        }

        // Cerrar el directorio
        closedir($dh);
    } else {
        echo "No se pudo abrir el directorio.";
    }
} else {
    echo "El directorio no existe.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Archivos</title>
</head>
<body>
<h1>
          Reproduciendo
          <span class="cancion-actual"> </span>
        </h1>
    <?php if (!empty($archivos)): ?>
        <ul>
            <?php foreach ($archivos as $archivo): ?>
                <?php
                // Separar el nombre del archivo de su extensión
                $info = pathinfo($archivo);
                $nombreArchivo = $info['filename'];
                $extension = isset($info['extension']) ? $info['extension'] : '';
                ?>
                <li>
                    <strong>Nombre:</strong> <a href="#" data-src="<?php echo htmlspecialchars($nombreArchivo)?>"><?php echo htmlspecialchars($nombreArchivo); ?></a>
                    <br>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No se encontraron archivos en el directorio especificado.</p>
    <?php endif; ?>
        <audio controls></audio>
    <script>
      $canciones = document.querySelectorAll("a");
      $audio = document.querySelector("audio");
      $cancionActual = document.querySelector(".cancion-actual");

      $canciones.forEach((cancion) => {
        cancion.addEventListener("click", (c) => {
          c.preventDefault();
          const nombre = cancion.getAttribute("data-src");
          alert(nombre);
          $cancionActual.innerText = nombre;
          $audio.src = `./uploads/${nombre}.mp3`;
          $audio.play();
        });
      });
    </script>
</body>
</html>
