<?php
session_start();

// Función para verificar si la ubicación del archivo es válida
function esUbicacionValida($ubicacion) {
    return file_exists($ubicacion);
}

// Clase que representa una canción
class Cancion {
    public $titulo;
    public $interprete;
    public $ubicacion;

    public function __construct($t, $i, $u) {
        $this->titulo = $t;
        $this->interprete = $i;
        $this->ubicacion = $u;
    }

    public function obtenerTitulo() {
        return $this->titulo;
    }

    public function obtenerInterprete() {
        return $this->interprete;
    }

    public function obtenerUbicacion() {
        return $this->ubicacion;
    }

    public function toString() {
        return $this->titulo . " - " . $this->interprete;
    }
}

// Clase que gestiona el reproductor de música
class Reproductor {
    private $actual;

    public function cargarCancion($c) {
        $this->actual = $c;
    }

    public function reproducir() {
        if ($this->actual) {
            echo "<script>reproducirCancion('".$this->actual->obtenerUbicacion()."');</script>";
        }
    }

    public function parar() {
        echo "<script>pararCancion();</script>";
    }

    public function pausa() {
        echo "<script>pausarCancion();</script>";
    }

    public function estaTocandoCancion() {
        echo "<script>return !audioPlayer.paused;</script>";
    }
}

// Clase que gestiona la lista de canciones como un árbol binario de búsqueda
class Nodo {
    public $cancion;
    public $izq;
    public $der;
    public $padre;

    public function __construct($cancion) {
        $this->cancion = $cancion;
        $this->izq = NULL;
        $this->der = NULL;
        $this->padre = NULL;
    }
}

class ArbolDeCanciones {
    private $raiz;

    public function __construct() {
        $this->raiz = NULL;
    }

    public function agregarCancion($c) {
        $nuevoNodo = new Nodo($c);
        if ($this->raiz === NULL) {
            $this->raiz = $nuevoNodo;
        } else {
            $this->insertar($this->raiz, $nuevoNodo);
        }
    }

    private function insertar($nodo, $nuevoNodo) {
        if ($nuevoNodo->cancion->obtenerInterprete() < $nodo->cancion->obtenerInterprete() ||
            ($nuevoNodo->cancion->obtenerInterprete() == $nodo->cancion->obtenerInterprete() && $nuevoNodo->cancion->obtenerTitulo() < $nodo->cancion->obtenerTitulo())) {
            if ($nodo->izq === NULL) {
                $nodo->izq = $nuevoNodo;
                $nuevoNodo->padre = $nodo;
            } else {
                $this->insertar($nodo->izq, $nuevoNodo);
            }
        } else {
            if ($nodo->der === NULL) {
                $nodo->der = $nuevoNodo;
                $nuevoNodo->padre = $nodo;
            } else {
                $this->insertar($nodo->der, $nuevoNodo);
            }
        }
    }

    public function mostrarLR() {
        $this->inOrder($this->raiz);
    }

    private function inOrder($nodo) {
        if ($nodo !== NULL) {
            $this->inOrder($nodo->izq);
            echo "<li data-src='{$nodo->cancion->obtenerUbicacion()}' onclick='playSong(this)'>{$nodo->cancion->toString()}</li>";
            $this->inOrder($nodo->der);
        }
    }
}

// Crear una instancia de la clase ArbolDeCanciones
if (!isset($_SESSION['lista'])) {
    $_SESSION['lista'] = new ArbolDeCanciones();
}
$lista = $_SESSION['lista'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['archivo'])) {
        $titulo = $_POST['titulo'];
        $interprete = $_POST['interprete'];
        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $ubicacion = $uploadDir . basename($_FILES['archivo']['name']);
        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $ubicacion)) {
            if (esUbicacionValida($ubicacion)) {
                $lista->agregarCancion(new Cancion($titulo, $interprete, $ubicacion));
                $_SESSION['lista'] = $lista;  // Guardar el estado del árbol en la sesión
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reproductor de Música MP3</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Reproductor de Música MP3</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" required>
        <label for="interprete">Intérprete:</label>
        <input type="text" id="interprete" name="interprete" required>
        <label for="archivo">Archivo:</label>
        <input type="file" id="archivo" name="archivo" accept=".mp3" required>
        <button type="submit">Cargar</button>
    </form>

    <h2>Lista de Reproducción</h2>
    <ul class="playlist" id="playlist">
        <?php $lista->mostrarLR(); ?>
    </ul>

    <div class="controls">
        <button id="shuffle">&#x1F500;</button>
        <button id="prev">&#x23EE;</button>
        <button id="play">&#x23EF;</button>
        <button id="next">&#x23ED;</button>
        <button id="repeat">&#x1F501;</button>
    </div>

    <audio id="audioPlayer" controls></audio>

    <script src="scripts.js"></script>
</body>
</html>
