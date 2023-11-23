<?php

require './conexion.php';

require './funciones.php';

if (isset($_GET["solicitaNuevaPass"])) {

    try {
        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username; //email del usuario

        $conexionBD = conectarBaseDatos();
        $emailExiste = emailRegistrado($conexionBD, $username);

        if ($emailExiste) {

            //obtener el id del usuario para pasarlo a funcion solicitaPass
            $consulta = "SELECT idUsuarios, email FROM Usuarios WHERE email = ?";
            $stmt = mysqli_prepare($conexionBD, $consulta);
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $sqlData = mysqli_stmt_get_result($stmt);
            $userData = mysqli_fetch_all($sqlData, MYSQLI_ASSOC);
            $id = $userData[0]['idUsuarios'];

            //generar token o un null en caso de que no exista el id
            $token = solicitaPass($conexionBD, $id);

            if ($token !== null) {
                //enviamos el correo
                require './Mailer.php'; //importar clase mailer
                $mailer = new Mailer();

                $url = 'https://olympus.arvispace.com/puntoDeVenta/#/reset-password?id=' . $id . '&token=' . $token;
                $asunto = "Recuperar contraseña - Punto de venta Olympus";
                $cuerpo = "Estimado usuario: <br> Si has solicitado el cambio de tu contraseña 
                da clic en el siguiente link: <br>
                <a href='$url'>$url</a>  <br>
                Si no hiciste esta solicitud puedes ignorar este correo.";

                if ($mailer->enviarEmail($username, $asunto, $cuerpo)) {
                    echo json_encode(["success" => true, "message" => "Link enviado al correo:$username"]);
                    exit;
                }
            }
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "No existe una cuenta asociada a esta dirección de correo"]);
        }

        $conexionBD->close();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}


if (isset($_GET["consultaToken"])) {
    try {
        //obtener id y token de url
        $id = $_GET["id"] ?? null;
        $token = $_GET["token"] ?? null;

        $conexionBD = conectarBaseDatos();

        $idUsuario_Token_Existe = validarToken($id, $token, $conexionBD);

        if ($idUsuario_Token_Existe) {
            echo json_encode(["success" => true, "message" => "Link valido"]);
            exit;
        } else {
            //si no existe el id , quiere decir que alguno de los tres filtros estan mal (id,token,resetPassword)
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "No se pudo verificar la informacion"]);
        }
        $conexionBD->close();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}

if (isset($_GET["actualizarPass"])) {
    try {
        //obtener id y token de url
        $id = $_GET["id"] ?? null;
        $token = $_GET["token"] ?? null;

        //obtener los datos del formulario
        $data = json_decode(file_get_contents("php://input"));
        $nuevaContra = $data->confirmaPassword; //nueva contraseña del usuario
        // encriptar la nueva contraseña
        $password_hash = password_hash($nuevaContra, PASSWORD_DEFAULT);

        $conexionBD = conectarBaseDatos();

        $actualizacion = actualizaClave($id, $token, $password_hash, $conexionBD);

        if ($actualizacion) {
            echo json_encode(["success" => true, "message" => "Contraseña actualizada"]);
            exit;
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Actualización fallida"]);
        }

        $conexionBD->close();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
