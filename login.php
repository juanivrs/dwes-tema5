<?php

/**********************************************************************************************************************
 * Este programa, a través del formulario que tienes que hacer debajo, en el área de la vista, realiza el inicio de
 * sesión del usuario verificando que ese usuario con esa contraseña existe en la base de datos.
 * 
 * Para mantener iniciada la sesión dentrás que usar la $_SESSION de PHP.
 * 
 * En el formulario se deben indicar los errores ("Usuario y/o contraseña no válido") cuando corresponda.
 * 
 * Dicho formulario enviará los datos por POST.
 * 
 * Cuando el usuario se haya logeado correctamente y hayas iniciado la sesión, redirige al usuario a la
 * página principal.
 * 
 * UN USUARIO LOGEADO NO PUEDE ACCEDER A ESTE SCRIPT.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */


/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar en la vista:
 * - TODO: añadir el menú.
 * - TODO: formulario con nombre de usuario y contraseña.
 */
session_start();

if (isset($_SESSION['usuario'])) {
    header('location:index.php');
    exit();
}

require 'utils/db.php';

$errorlogin = "";

if ($_POST) {
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);

    if ($mysqli->connect_errno) {
        echo "No ha sido posible conectarse a la base de datos.";
        exit();
    } else {
        $usuario = htmlentities(trim($_POST['usuario']));
        $clave = htmlentities(trim($_POST['clave']));
        if (logUser($usuario, $clave)) {
            $_SESSION["usuario"] = $usuario;
            header('location:index.php');
            exit();
        } else {
            $errorlogin = "Usuario y/o contraseña incorrectos";
        }
    }
}



?>

<h1>Inicia sesión</h1>
<ul>
    <li><a href='index.php'>Home</a></li>
    <li><a href="filter.php">Filtrar imágenes</a></li>
    <li><a href="signup.php">Regístrate</a></li>
    <li><strong>Iniciar Sesión</strong></li>
</ul>
<form action="login.php" method="post">
    <p>
        <label for="usuario">Nombre de usuario</label><br>
        <input type="text" name="usuario" id="usuario">
    </p>
    <p>
        <label for="clave">Contraseña</label><br>
        <input type="password" name="clave" id="clave">
    </p>
    <?php
    echo "<p style='color:red'>$errorlogin </p>";
    ?>
    <p>
        <input type="submit" value="Inicia sesión">
    </p>
</form>
</main>