<?php
/**********************************************************************************************************************
 * Lógica del programa
 */
session_start();

$usuario = $_SESSION && isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : null;

$mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
if ($mysqli->connect_errno) {
    echo "Error fatal: No ha sido posible conectarse a la base de datos.";
    exit();
}

$resultado = $mysqli->query("select i.id id, i.nombre nombre, i.ruta ruta, u.nombre usuario from imagen i, usuario u where i.usuario=u.id");
if (!$resultado) {
    echo "<p>Error fatal: " . $mysqli->error . "</p>";
    exit();
}

/*********************************************************************************************************************
 * Salida HTML
 */
echo "<h1>Galería de imágenes</h1>";
if ($usuario == null) {
    echo <<<END
        <ul>
            <li><strong>Home</strong></li>
            <li><a href="filter.php">Filtrar imágenes</a></li>
            <li><a href="signup.php">Regístrate</a></li>
            <li><a href="login.php">Iniciar Sesión</a></li>
        </ul>
    END;
} else {
    return <<<END
        <ul>
            <li><strong>Home</strong></li>
            <li><a href="add.php">Añadir imagen</a></li>
            <li><a href="filter.php">Filtrar imágenes</a></li>
            <li><a href="signup.php">Cerrar sesión ($usuario)</a></li>
        </ul>
    END;
}

if ($resultado->num_rows == 0) {
    echo "<h2>No hay imágenes.</h2>";
} else {
    echo "<h2>Imágenes totales: $resultado->num_rows</h2>";
}

while (($fila = $resultado->fetch_assoc()) != null) {
    echo <<<END
        <figure>
            <div>{$fila['nombre']} (subida por {$fila['usuario']})</div>
            <img src="{$fila['ruta']}" width="200px">
            <a href="delete.php?id={$fila['id']}">Borrar</a>
        </figure>
    END;
}
