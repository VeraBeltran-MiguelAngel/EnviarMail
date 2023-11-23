<?php

/**
 * 1° Funcion para validar si el email enviado desde el formulario resetPassword
 * existe en la BD
 * @return boolean si encuentra el mail 
 */
function emailRegistrado($conexionBD, $username)
{
    $consulta = "SELECT idUsuarios FROM Usuarios WHERE email = ?";
    $stmtE = mysqli_prepare($conexionBD, $consulta);

    if (!$stmtE) {
        throw new Exception("Error al preparar la consulta: " . mysqli_error($conexionBD));
    }

    // Vincular parámetros
    mysqli_stmt_bind_param($stmtE, "s", $username);
    // Ejecutar la consulta
    $resultE = mysqli_stmt_execute($stmtE);
    if (!$resultE) {
        throw new Exception("Error al ejecutar la consulta: " . mysqli_error($conexionBD));
    }

    // Almacenar el resultado de la consulta
    mysqli_stmt_store_result($stmtE);
    $emailExiste = mysqli_stmt_num_rows($stmtE) > 0;
    mysqli_stmt_close($stmtE);
    return $emailExiste;
}

/**
 * Funcion que crea un token
 */
function generateToken($length = 32)
{
    // Genera bytes aleatorios
    $randomBytes = random_bytes($length);

    // Convierte los bytes en una cadena hexadecimal
    $token = bin2hex($randomBytes);

    return $token;
}

/**
 * 2° Funcion para solicitar cambio de contraseña a x usuario
 * al solicitar cambio se asigna un token y el valor de resetPassword cambia a 1
 * @return token 
 */
function solicitaPass($conexionBD, $userId)
{
    $token = generateToken();
    $updatePass = "UPDATE Usuarios SET token = ?, resetPassword = 1 WHERE idUsuarios = ?";

    $stmt = mysqli_prepare($conexionBD, $updatePass);

    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . mysqli_error($conexionBD));
    }
    // Vincular parámetros
    mysqli_stmt_bind_param($stmt, "si", $token, $userId);
    // Ejecutar la consulta
    $result = mysqli_stmt_execute($stmt);

    if (!$result) {
        throw new Exception("Error al ejecutar la consulta: " . mysqli_error($conexionBD));
    }

    // Verificar si se afectaron filas (si el usuario existe)
    if (mysqli_affected_rows($conexionBD) > 0) {
        return $token;
    } else {
        return null;
    }

    mysqli_stmt_close($stmt);
}

/**
 * 3° Funcion que valida el token generado en la base de datos y comprueba que realmente exista,
 * para evitar que el usuario quiera usar un token diferente al registrado y cambie contraseña
 * @return boolean si ecnuentra un idUsuario que coincida con el token dado
 */
function validarToken($userId, $token, $conexionBD)
{

    // consultar id Usuario que coincida con el token dado y que la solicitud de reset este en 1
    $consultaToken = "SELECT idUsuarios FROM Usuarios 
    WHERE idUsuarios = ? 
    AND token = ?
    AND resetPassword = 1";

    $stmt = mysqli_prepare($conexionBD, $consultaToken);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . mysqli_error($conexionBD));
    }

    // Vincular parámetros
    mysqli_stmt_bind_param($stmt, "is", $userId, $token);

    // Ejecutar la consulta
    $resultE = mysqli_stmt_execute($stmt);
    if (!$resultE) {
        throw new Exception("Error al ejecutar la consulta: " . mysqli_error($conexionBD));
    }

    // Almacenar el resultado de la consulta
    mysqli_stmt_store_result($stmt);
    $idExiste = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $idExiste;
}

function actualizaClave($user_ID, $token, $passwordHash, $conexionBD)
{
    $consultaActualiza = "UPDATE Usuarios 
    SET pass = ?, 
    token='',
    resetPassword=0     
    WHERE idUsuarios = ? AND token = ?";

    $stmt = mysqli_prepare($conexionBD, $consultaActualiza);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . mysqli_error($conexionBD));
    }

    // Vincular parámetros
    mysqli_stmt_bind_param($stmt, "sis", $passwordHash, $user_ID, $token);

    // Ejecutar la consulta
    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        throw new Exception("Error al ejecutar la consulta: " . mysqli_error($conexionBD));
    }

    // Verificar si se afectaron filas (si el id y token son validos)
    if (mysqli_affected_rows($conexionBD) > 0) {
        return true;
    } else {
        return false;
    }

    mysqli_stmt_close($stmt);
}


