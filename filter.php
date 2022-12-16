<?php

/*********************************************************************************************************************
 * Este script muestra un formulario a través del cual se pueden buscar imágenes por el nombre y mostrarlas. Utiliza
 * el operador LIKE de SQL para buscar en el nombre de la imagen lo que llegue por $_GET['nombre'].
 * 
 * Evidentemente, tienes que controlar si viene o no por GET el valor a buscar. Si no viene nada, muestra el formulario
 * de búsqueda. Si viene en el GET el valor a buscar (en $_GET['nombre']) entonces hay que preparar y ejecutar una 
 * sentencia SQL.
 * 
 * El valor a buscar se tiene que mantener en el formulario.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */
session_start();
require 'utils/db.php';
$usuario = $_SESSION && isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : null;

$results = [];
$textosan = $_GET && isset($_GET['nombre']) ? htmlentities(trim($_GET['nombre'])) : "";
if (mb_strlen($textosan) > 0) {
    $results = filter($textosan);
}
?>

<?php
/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar:
 * - TODO: completa el código de la vista añadiendo el menú de navegación.
 * - TODO: en el formulario falta añadir el nombre que se puso cuando se envió el formulario.
 * - TODO: debajo del formulario tienen que aparecer las imágenes que se han encontrado en la base de datos.
 */
?>
<h1>Galería de imágenes</h1>
<?php
if ($usuario == null) {
    echo <<<END
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><strong>Filtrar imágenes</strong></li>
            <li><a href="signup.php">Regístrate</a></li>
            <li><a href="login.php">Iniciar Sesión</a></li>
        </ul>
    END;
} else {
    echo <<<END
        <ul>
            <li><a href='index.php'>Home</a></li>
            <li><a href="add.php">Añadir imagen</a></li>
            <li><strong>Filtrar imágenes</strong></li>
            <li><a href="logout.php">Cerrar sesión ($usuario)</a></li>
        </ul>
    END;
}
?>
<h2>Busca imágenes por filtro</h2>

<form method="get">
    <p>
        <label for="nombre">Busca por nombre</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo $textosan ?>">
    </p>
    <p>
        <input type="submit" value="Buscar">
    </p>
</form>

<?php
foreach ($results as $result) {
    echo <<<END
        <h1>{$result['nombre']}</h1>
        <div style='width:400px;height:400px;border:1px solid black;'>
        <img style='width:100%; height:100%' src='{$result['ruta']}'/>
        </div>
    END;
}
?>