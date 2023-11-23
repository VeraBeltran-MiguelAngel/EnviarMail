<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE, PATCH");
    header("Access-Control-Allow-Headers: Content-Type");
    exit;
}

function conectarBaseDatos() {
    $servidor = "localhost:3306";
    $usuario = "root";
    $contrasenia = "Kindred1222.";
    $nombreBaseDatos = "olympusv5";

    $conexionBD = new mysqli($servidor, $usuario, $contrasenia, $nombreBaseDatos);

    if ($conexionBD->connect_error) {
        throw new Exception("Error de conexión a la base de datos: " . $conexionBD->connect_error);
    }

    return $conexionBD;
}
?>