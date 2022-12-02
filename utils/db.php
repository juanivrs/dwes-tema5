<?php

/**
 * Función comprobar usuario
 * Devuelve true si el usuario no existe en la base de datos.
 */
function userExist($nombre): bool
{
    $cond = false;
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    // 1. Preparación
    $sentencia = $mysqli->prepare("select nombre from usuario where nombre = ?");
    if (!$sentencia) {
        echo "Falló la preparación: (" . $mysqli->errno . ") " . $mysqli->error;
        exit();
    }

    // 2. Vinculación (bind): dos strings y dos números enteros
    $vinculacion = $sentencia->bind_param("s", $nombre);
    if (!$vinculacion) {
        echo "Falló la vinculación de parámetros: (" . $sentencia->errno . ") " . $mysqli->error;
        $sentencia->close();
        exit();
    }

    // 3. Ejecución
    $resultado = $sentencia->execute();
    if (!$resultado) {
        $sentencia->close();
    }

    // 4. Vinculación de resultado
    $vinres = $sentencia->bind_result($resnombre);
    if (!$vinres) {
        echo "Falló la vinculación de resultados: (" . $sentencia->errno . ") " . $mysqli->error;
        $sentencia->close();
        exit();
    }
    // 5.Recogida de datos.
    $sentencia->fetch();
    if (is_null($resnombre)) {
        $cond = true;
    }
    // 4. Cerramos la sentencia y liberamos recurso
    $sentencia->close();

    // También se cierra la conexión con la base de datos a través del objeto mysqli
    $mysqli->close();
    return $cond;
}
