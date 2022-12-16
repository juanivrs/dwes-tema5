<?php

/*********************************************************************************************************************
 * Este script realiza el registro del usuario vía el POST del formulario que hay debajo, en la vista.
 * 
 * Cuando llegue POST hay que validarlo y si todo fue bien insertar en la base de datos el usuario.
 * 
 * Requisitos del POST:
 * - El nombre de usuario no tiene que estar vacío y NO PUEDE EXISTIR UN USUARIO CON ESE NOMBRE EN LA BASE DE DATOS.
 * - La contraseña tiene que ser, al menos, de 8 caracteres.
 * - Las contraseñas tiene que coincidir.
 * 
 * La contraseña la tienes que guardar en la base de datos cifrada mediante el algoritmo BCRYPT.
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
 * - TODO: los errores que se produzcan tienen que aparecer debajo de los campos.
 * - TODO: cuando hay errores en el formulario se debe mantener el valor del nombre de usuario en el campo
 *         correspondiente.
 */


session_start();

if (isset($_SESSION['usuario'])) {
    header('location:index.php');
    exit();
}

require 'utils/db.php';

$error = false;
$mostrarerror =
    [
        'user0' => "",
        'userregister' => "",
        'pass8' => "",
        'passcoincide' => ""
    ];


if ($_POST && isset($_POST['nombre']) &&  isset($_POST['clave']) && isset($_POST['repite_clave'])) {
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);

    if ($mysqli->connect_errno) {
        echo "No ha sido posible conectarse a la base de datos.";
        exit();
    } else {
        $nameval = htmlentities(trim($_POST['nombre']));
        $passval = htmlentities(trim($_POST['clave']));
        $respassval = htmlentities(trim($_POST['repite_clave']));

        $existeuser = userExist($nameval);
        if (mb_strlen($passval) >= 8 && $passval === $respassval && $existeuser === true) {

            $password = password_hash($passval, PASSWORD_BCRYPT);
            // $resultado = $mysqli->query("insert into usuario (nombre, clave) values ('$nameval', '$password');");
            $resultado = insertUser($nameval, $password);
            if ($resultado === false) {
                echo <<<END
                    <div class='alert alert-danger'>
                    ERROR:Error al registrar usuario.
                    </div>
                    END;
                $error = true;
            }
        } else {
            $mostrarerror['user0'] = mb_strlen($nameval) <= 0 ? "El usuario no puede estar vacío" : "";
            $mostrarerror['userregister'] = $existeuser === false ? "Nombre de usuario ya registrado." : "";
            $mostrarerror['pass8'] = mb_strlen($passval) < 8 ? "La contraseña tiene que tener 8 carácteres" : "";
            $mostrarerror['passcoincide'] = $passval !== $respassval ? "No coinciden las contraseñas" : "";
            $error = true;
        }
    }
    $mysqli->close();
}

$nombrevalue = $_POST && isset($_POST['nombre']) ? htmlentities(trim($_POST['nombre'])) : "";
?>


<?php if (!$_POST || $error == true) { ?>

    <h1>Regístrate</h1>
    <ul>
        <li><a href='index.php'>Home</a></li>
        <li><a href="filter.php">Filtrar imágenes</a></li>
        <li><strong>Regístrate</strong></li>
        <li><a href="login.php">Iniciar Sesión</a></li>
    </ul>
    <form action="signup.php" method="post">
        <p>
            <label for="nombre">Nombre de usuario</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo $nombrevalue ?>">
        </p>
        <?php
        echo "<p style='color:red'> {$mostrarerror['user0']} </p>";
        echo "<p style='color:red'>{$mostrarerror['userregister']} </p>";
        ?>
        <p>
            <label for="clave">Contraseña</label>
            <input type="password" name="clave" id="clave">
        </p>
        <?php
        echo "<p style='color:red'> {$mostrarerror['pass8']} </p>";
        echo "<p style='color:red'>{$mostrarerror['passcoincide']} </p>";
        ?>
        <p>
            <label for="repite_clave">Repite la contraseña</label>
            <input type="password" name="repite_clave" id="repite_clave">
        </p>
        <p>
            <input type="submit" value="Regístrate">
        </p>
    </form>

<?php } else { ?>
    <h1>Usuario Registrado</h1>
    <p> <a href="login.php">Ir a login</a> </p>
<?php } ?>