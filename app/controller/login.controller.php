<?php
@session_start();
include_once "../../config/configuracion.php";
include_once "../../config/core.php";

$obj = new core($server, $user, $password, $database);

if (!empty($_POST)) {
    if (!empty($_POST['funcion'])) {
        switch ($_POST['funcion']) {

            //======================================================================================\\
            //================================== CASE VALIDAR SESION =====================================\\
            //========================================================================================\\

            case "validar_sesion":

                if (!empty($_POST['id']) && !empty($_POST['cedula'])) {

                    $id = $_POST['id'];
                    $cedula = $_POST['cedula'];
                    $estado = "'A'";
                    $respuesta = array();

                    $validar_sesion = $obj->execute("SELECT DISTINCT Id_Estudiante, CONCAT(Nombres, ' ', Apellido1, ' ', Apellido2) AS Nombre_Completo, Cedula FROM estudiantes WHERE Id_Estudiante='$id' AND Cedula='$cedula' AND Estado_Estudiante = " . $estado . "");                    
                    $resultado = mysqli_fetch_assoc($validar_sesion);

                    if (mysqli_num_rows($validar_sesion) != 0) {
                        $_SESSION['nombreUsuario'] = str_replace("*","", $resultado["Nombre_Completo"]);
                        $_SESSION["Id_Estudiante"] = $resultado["Id_Estudiante"];
                        $respuesta["tipoRespuesta"] = "success";
                    } else {
                        $respuesta["tipoRespuesta"] = "error";
                    }
                    echo json_encode ($respuesta);
                }
                break;

            //============================================================================================\\
            //================================== CASE CERRAR SESIÓN =============================================\\
            //=============================================================================================\\

            case "cerrarSesion":

                @session_start();
                @session_unset();
                @session_destroy();

                break;
        }
    }
}
