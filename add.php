<?php

/**********************************************************************************************************************
 * Este es el script que añade imágenes en la base de datos. En la tabla "imagen" de la base de datos hay que guardar
 * el nombre que viene vía POST, la ruta de la imagen como se indica más abajo, la fecha de la inserción (función
 * UNIX_TIMESTAMP()) y el identificador del usuario que inserta la imagen (el usuario que está logeado en estos
 * momentos).
 * 
 * ¿Cuál es la ruta de la imagen? ¿De dónde sacamos esta ruta? Te lo explico a continuación:
 * - Busca una forma de asignar un nombre que sea único.
 * - La extensión será la de la imagen original, que viene en $_FILES['imagne']['name'].
 * - Las imágenes se subirán a la carpeta llamada "imagenes/" que ves en el proyecto.
 * - En la base de datos guardaremos la ruta relativa en el campo "ruta" de la tabla "imagen".
 * 
 * Así, si llega por POST una imagen PNG y le asignamosel nombre "imagen1", entonces en el campo "ruta" de la tabla
 * "imagen" de la base de datos se guardará el valor "imagenes/imagen1.png".
 * 
 * Como siempre:
 * 
 * - Si no hay POST, entonces tan solo se muestra el formulario.
 * - Si hay POST con errores se muestra el formulario con los errores y manteniendo el nombre en el campo nombre.
 * - Si hay POST y todo es correcto entonces se guarda la imagen en la base de datos para el usuario logeado.
 * 
 * Esta son las validaciones que hay que hacer sobre los datos POST y FILES que llega por el formulario:
 * - En el nombre debe tener algo (mb_strlen > 0).
 * - La imagen tiene que ser o PNG o JPEG (JPG). Usa FileInfo para verificarlo.
 * 
 * NO VAMOS A CONTROLAR SI YA EXISTE UNA IMAGEN CON ESE NOMBRE. SI EXISTE, SE SOBREESCRIBIRÁ Y YA ESTÁ.
 * 
 * A ESTE SCRIPT SOLO SE PUEDE ACCEDER SI HAY UN USARIO LOGEADO.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que desarrollar toda la lógica de este script.
 */


/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar:
 * - TODO: añadir el menú de navegación.
 * - TODO: añadir en el campo del nombre el valor del mismo cuando haya errores en el envío para mantener el nombre
 *         que el usuario introdujo.
 * - TODO: añadir los errores que se produzcan cuando se envíe el formulario debajo de los campos.
 */
session_start();
$usuario = $_SESSION && isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : null;
if ($usuario == null) {
    header('location:index.php');
    exit();
}
require 'utils/db.php';

$errores = true;
$errarr = ['emptyn' => '', 'mimem' => '', 'datab' => '', 'upload' => ''];
$nameuser = "";
if ($_POST) {
    $nombreval = htmlspecialchars(trim($_POST['nombre']));
    $nameuser = $nombreval;
    if (mb_strlen($nombreval) > 0) {
        if ($_FILES && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK && $_FILES['imagen']['size'] > 0) {
            $allowedmime = array('image/png', 'image/jpg', 'image/jpeg');
            $filemime = $_FILES['imagen']['tmp_name'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_fichero = finfo_file($finfo, $filemime);
            if (in_array($mime_fichero, $allowedmime)) {
                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $_FILES['imagen']['name'] = time() . "." . $ext;
                $rutaFicheroDestino = './imagenes/' . basename($_FILES['imagen']['name']);
                $seHaSubido = move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaFicheroDestino);
                if (!$seHaSubido) {
                    $errarr['upload'] = 'Error en la subida de archivo';
                } else {
                    $userid = getUserId($usuario);
                    if ($userid == false) {
                        $errarr['datab'] = 'Error con la base de datos';
                    } else {
                        $result = insertImg($nombreval, $rutaFicheroDestino, $userid);
                        if ($result == false) {
                            $errarr['datab'] = 'Error con la base de datos';
                        } else {
                            $errores = false;
                        }
                    }
                }
            } else {
                $errarr['mimem'] = 'Extensión o tipo mime no permitido.';
            }
        } else {
            $errarr['upload'] = 'Error en la subida de archivo';
        }
    } else {
        $errarr['emptyn'] = 'El Nombre no puede estar vacío.';
    }
}
?>
<?php if (!$_POST || $errores == true) { ?>
    <h1>Galería de imágenes</h1>
    <ul>
        <li><a href='index.php'>Home</a></li>
        <li><strong>Añadir imagen</strong></li>
        <li><a href="filter.php">Filtrar imágenes</a></li>
        <li><a href="logout.php">Cerrar sesión (<?php echo $usuario ?>)</a></li>
    </ul>
    <form method="post" enctype="multipart/form-data">
        <p>
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo $nameuser ?>">
        </p>
        <?php
        echo "<p style='color:red'>{$errarr['emptyn']}</p>";
        ?>
        <p>
            <label for="imagen">Imagen</label>
            <input type="file" name="imagen" id="imagen">
        </p>
        <?php
        echo "<p style='color:red'>{$errarr['mimem']}</p>";
        echo "<p style='color:red'>{$errarr['upload']}</p>";
        echo "<p style='color:red'>{$errarr['datab']}</p>";
        ?>
        <p>
            <input type="submit" value="Añadir">
        </p>
    </form>

<?php } else {
    header('location:index.php');
} ?>