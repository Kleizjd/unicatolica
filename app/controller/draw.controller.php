<?php @session_start();
include_once "../../config/configuracion.php";
include_once "../../config/core.php";

$obj = new core($server, $user, $password, $database);

if (!empty($_POST)) {
    if (!empty($_POST['funcion'])) {
        switch ($_POST['funcion']) {
            //============================================================================================\\
            //================================== CASE SORTEO SEMANA =====================================\\
            //========================================================================================\\

            case "sorteoSemana":

                $respuesta = array();
                $query = $obj->execute("SELECT COUNT(*) No_Reg_A FROM asistencia");
                $resultado = mysqli_fetch_row($query);
                $respuesta["max"] = $resultado[0];

                echo json_encode($respuesta);

                break;

            //======================================================================================\\
            //================================== CASE GANADOR =========================================\\
            //========================================================================================\\

            case "ganador":

                $respuesta = array ();
                $No_Reg_A = $_POST["No_Reg_A"];
                $estado = "'A'";
                $query = $obj->execute("SELECT a.No_Reg_A, a.Id_Estudiante, CONCAT (b.Nombres, ' ', b.Apellido1, ' ', b.Apellido2) AS Nombre_Completo FROM asistencia a, estudiantes b WHERE a.Id_Estudiante=b.Id_Estudiante AND a.No_Reg_A = " . $No_Reg_A . " AND b.Estado_Estudiante = " . $estado . " ORDER BY a.No_Reg_A ASC");
                $resultado = mysqli_fetch_row($query);
                $respuesta["ganador"] = trim(str_replace("*", "", $resultado[2]));

                echo json_encode($respuesta);

                break;
        }
    }
}
