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

/**
 * Función de loggerase (sin crear sesion).
 * Devuelve true si el ususuario y contraseña coinciden con el de la base de datos
 */

function logUser($nombre, $password): bool
{
    $cond = false;
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    // 1. Preparación
    $sentencia = $mysqli->prepare("select nombre,clave from usuario where nombre = ?");
    if (!$sentencia) {
        echo "Falló la preparación: (" . $mysqli->errno . ") " . $mysqli->error;
        $mysqli->close();
        exit();
    }

    // 2. Vinculación (bind): dos strings y dos números enteros
    $vinculacion = $sentencia->bind_param("s", $nombre);
    if (!$vinculacion) {
        echo "Falló la vinculación de parámetros: (" . $sentencia->errno . ") " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        exit();
    }

    // 3. Ejecución
    $resultado = $sentencia->execute();
    if (!$resultado) {
        echo "Falló en la ejecución: (" . $sentencia->errno . ") " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
    }

    // 4. Vinculación de resultado
    $resultado = $sentencia->get_result();
    if (!$resultado) {
        echo "Falló la obtención de resultados: (" . $sentencia->errno . ") " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        exit();
    }
    // 5.Recogida de datos.
    $fila = $resultado->fetch_assoc();
    if (!is_null($fila)) {
        $nombredb = $fila['nombre'];
        $passdb = $fila['clave'];
        if (password_verify($password, $passdb)) {
            $cond = true;
        }
        $resultado->free();
    }
    // 4. Cerramos la sentencia y liberamos recurso
    $sentencia->close();

    // También se cierra la conexión con la base de datos a través del objeto mysqli
    $mysqli->close();
    return $cond;
}

/**
 * Se le pasa un string por parámetros y si en la base de datos hay alguna imágen que contenga el nombre la devuelve en un array
 * Devuelve array.
 */
function filter(string $texto): array
{
    //Conectamos a mariadb
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->errno) {
        echo "No hay conexión con la base de datos";
        return [];
    }

    //Preparamos la consulta
    $sentencia = $mysqli->prepare(
        "select id,nombre,ruta,usuario from imagen where nombre like ?"
    );
    if (!$sentencia) {
        echo "Error: " . $mysqli->error;
        $mysqli->close();
        return [];
    }

    //Bind
    $valor = '%' . $texto . '%';
    $vinculo = $sentencia->bind_param("s", $valor);
    if (!$vinculo) {
        echo "Error al vincular: " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return [];
    }

    //Ejecutamos
    $ejecucion = $sentencia->execute();
    if (!$ejecucion) {
        echo "Error al ejecutar la sentencia: " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return [];
    }

    //Recuperamos las filas obtenidas como resultado
    $resultado = $sentencia->get_result();
    if (!$resultado) {
        echo "Error al ejecutar la sentencia: " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return [];
    }

    $searchres = [];
    while (($fila = $resultado->fetch_assoc()) != null) {
        $searchres[] = $fila;
    }
    return $searchres;
}


function insertUser($user, $pass): bool
{
    $cond = false;
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    // 1. Preparación
    $sentencia = $mysqli->prepare("insert into usuario (nombre, clave) value (?, ?)");
    if (!$sentencia) {
        echo "Falló la preparación: (" . $mysqli->errno . ") " . $mysqli->error;
        return $cond;
    }

    // 2. Vinculación (bind): dos strings y dos números enteros
    $vinculacion = $sentencia->bind_param("ss", $user, $pass);
    if (!$vinculacion) {
        echo "Falló la vinculación de parámetros: (" . $sentencia->errno . ") " . $mysqli->error;
        $sentencia->close();
        return $cond;
    }

    // 3. Ejecución
    $resultado = $sentencia->execute();
    if (!$resultado) {
        echo "Falló al ejecutar la sentencia: " . $mysqli->error;
        $sentencia->close();
        return $cond;
    } else {
        $cond = true;
    }

    // 4. Cerramos la sentencia y liberamos recurso
    $sentencia->close();

    // También se cierra la conexión con la base de datos a través del objeto mysqli
    $mysqli->close();

    return $cond;
}

function insertImg($name, $path, $user): bool
{
    $cond = false;
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    // 1. Preparación
    $sentencia = $mysqli->prepare("insert into imagen (nombre,ruta,usuario) value (?, ?, ?)");
    if (!$sentencia) {
        echo "Falló la preparación: (" . $mysqli->errno . ") " . $mysqli->error;
        return $cond;
    }

    // 2. Vinculación (bind): dos strings y dos números enteros
    $vinculacion = $sentencia->bind_param("ssi", $name, $path, $user);
    if (!$vinculacion) {
        echo "Falló la vinculación de parámetros: (" . $sentencia->errno . ") " . $mysqli->error;
        $sentencia->close();
        return $cond;
    }

    // 3. Ejecución
    $resultado = $sentencia->execute();
    if (!$resultado) {
        echo "Falló al ejecutar la sentencia: " . $mysqli->error;
        $sentencia->close();
        return $cond;
    } else {
        $cond = true;
    }

    // 4. Cerramos la sentencia y liberamos recurso
    $sentencia->close();

    // También se cierra la conexión con la base de datos a través del objeto mysqli
    $mysqli->close();

    return $cond;
}

function getUserId($name)
{
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->errno) {
        echo "No hay conexión con la base de datos";
        return false;
    }

    //Preparamos la consulta
    $sentencia = $mysqli->prepare(
        "select id from usuario where nombre like ?"
    );
    if (!$sentencia) {
        echo "Error: " . $mysqli->error;
        $mysqli->close();
        return false;
    }

    //Bind
    $vinculo = $sentencia->bind_param("s", $name);
    if (!$vinculo) {
        echo "Error al vincular: " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return false;
    }

    //Ejecutamos
    $ejecucion = $sentencia->execute();
    if (!$ejecucion) {
        echo "Error al ejecutar la sentencia: " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return false;
    }

    //Recuperamos las filas obtenidas como resultado
    $resultado = $sentencia->get_result();
    if (!$resultado) {
        echo "Error al ejecutar la sentencia: " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return false;
    }

    $fila = $resultado->fetch_assoc();
    return $fila['id'];
}


function deleteImg($id): bool
{
    $cond = false;
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    // 1. Preparación
    $sentencia = $mysqli->prepare("delete from imagen where id =?");
    if (!$sentencia) {
        echo "Falló la preparación: (" . $mysqli->errno . ") " . $mysqli->error;
        return $cond;
    }

    // 2. Vinculación (bind): dos strings y dos números enteros
    $vinculacion = $sentencia->bind_param("i", $id);
    if (!$vinculacion) {
        echo "Falló la vinculación de parámetros: (" . $sentencia->errno . ") " . $mysqli->error;
        $sentencia->close();
        return $cond;
    }

    // 3. Ejecución
    $resultado = $sentencia->execute();
    if (!$resultado) {
        echo "Falló al ejecutar la sentencia: " . $mysqli->error;
        $sentencia->close();
        return $cond;
    } else {
        $cond = true;
    }

    // 4. Cerramos la sentencia y liberamos recurso
    $sentencia->close();

    // También se cierra la conexión con la base de datos a través del objeto mysqli
    $mysqli->close();

    return $cond;
}
