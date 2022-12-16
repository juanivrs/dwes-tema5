<?php

/**********************************************************************************************************************
 * Este script simplemente elimina la imagen de la base de datos y de la carpeta <imagen>
 *
 * La información de la imagen a eliminar viene vía GET. Por GET se tiene que indicar el id de la imagen a eliminar
 * de la base de datos.
 * 
 * Busca en la documentación de PHP cómo borrar un fichero.
 * 
 * Si no existe ninguna imagen con el id indicado en el GET o no se ha inicado GET, este script redirigirá al usuario
 * a la página principal.
 * 
 * En otro caso seguirá la ejecución del script y mostrará la vista de debajo en la que se indica al usuario que
 * la imagen ha sido eliminada.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que desarrollar toda la lógica de este script.
 */


/*********************************************************************************************************************
 * Salida HTML
 */
session_start();
$usuario = $_SESSION && isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : null;
if ($usuario == null) {
    header('location:index.php');
    exit();
}
require 'utils/db.php';

if ($_GET && isset($_GET['id'])) {
    $idval = htmlspecialchars(trim($_GET['id']));
    $resultado = deleteImg($idval);
    if ($resultado === false) {
        header('location:index.php');
        exit();
    }
}
?>
<h1>Galería de imágenes</h1>

<p>Imagen eliminada correctamente</p>
<p>Vuelve a la <a href="index.php">página de inicio</a></p>