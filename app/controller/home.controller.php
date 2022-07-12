<?php
@session_start();
include_once "../../config/configuracion.php";
include_once "../../config/core.php";
include_once "../../vendor/phpqrcode/qrlib.php";

$obj = new core($server, $user, $password, $database);

if (!empty($_POST)) {
    if (!empty($_POST['funcion'])) {
        switch ($_POST['funcion']) {

            //======================================================================================\\
            //================================== CASE SELECT ACTIVIDADES =================================\\
            //========================================================================================\\

            case "selectActividades":

                $estado = "'A'";
                $query = $obj->execute("SELECT Id_Conferencia, Titulo FROM conferencias WHERE Estado_Conferencia =" . $estado . " ");

                $select = "";
                $select .= "<option value=''>Seleccione ...</option>";
                while ($resultado = mysqli_fetch_row($query)) {
                    $select .= "<option value=" . $resultado[0] . ">" . $resultado[1] . "</option>";
                }

                echo $select;

                break;

            //======================================================================================\\
            //================================== CASE SELECT ESTUDIANTES =================================\\
            //========================================================================================\\

            case "selectEstudiantes":

                $estado = "'A'";
                $query = $obj->execute("SELECT Id_Estudiante, CONCAT (Nombres, ' ', Apellido1, ' ', Apellido2) AS Nombre_Completo FROM estudiantes WHERE NOT EXISTS (SELECT Id_Estudiante FROM registromaratonprogramacion WHERE registromaratonprogramacion.Id_Estudiante = estudiantes.Id_Estudiante AND registromaratonprogramacion.Estado_Registro = " . $estado . ") AND Estado_Estudiante = " . $estado . " ");
                $select = "";
                $select .= "<option value=''>Seleccione ...</option>";
                while ($resultado = mysqli_fetch_row($query)) {
                    $select .= "<option value=" . $resultado[0] . ">" . trim(str_replace("*", "", $resultado[1])) . "</option>";
                }

                echo $select;

                break;

            //======================================================================================\\
            //================================== CASE LLENAR DATOS =====================================\\
            //=======================================================================================\\

            case "llenarDatos":

                $opcionSelect = $_POST["opcionSelect"];
                $estado = "'A'";

                if ($opcionSelect != 0) {
                    $query = $obj->execute("SELECT * FROM conferencias WHERE Id_Conferencia=" . $opcionSelect . " AND Estado_Conferencia =" . $estado . " ORDER by Id_Conferencia ASC");
                    while ($conferencias = mysqli_fetch_assoc($query)) {
                        $conferencias["Hora"] = date("g:i A", strtotime($conferencias["Hora"]));
                        echo json_encode($conferencias);
                    }
                }

                break;

            //============================================================================================\\
            //================================== CASE INSCRIBIR CONFERENCIA =====================================\\
            //=============================================================================================\\

            case "inscribirActividad":

                if (isset($_POST["actividad"])) {
                    $limite = count($_POST["actividad"]);
                    $respuesta = array();
                    $noConferencia = array();
                    $select = "";
                    // Carpeta temporal
                    $tempDir = "../../public/temp/";
                    // Comprobar si la carpeta existe
                    if (!file_exists($tempDir)) {
                        mkdir($tempDir, 0700);
                    }
                    // Nombre QR
                    $u = $obj->uuId();
                    $filename = $u . ".png";
                    $pngAbsoluteFilePath = $tempDir . $filename;
                    $size = 10;
                    $level = 'L';
                    $code = null;
                    for ($i = 0; $i < $limite; $i++) {
                        if (!empty($_POST["actividad"][$i])) {
                            date_default_timezone_set("America/Bogota");
                            $idEstudiante = $_SESSION["Id_Estudiante"];
                            $fechaActual = "'" . date("Y-m-d") . "'";
                            $horaActual = "'" . date('G:i:s') . "'";
                            $titulo = "'" . $_POST["actividad"][$i] . "'";
                            $estado = "'A'";

                            $query = $obj->execute("SELECT Id_Conferencia FROM conferencias WHERE Titulo=" . $titulo . " AND Estado_Conferencia=" . $estado . " ");
                            $idConferencia = mysqli_fetch_row($query);

                            $query2 = $obj->execute("SELECT * FROM inscripciones WHERE Id_Conferencia=" . $idConferencia[0] . " AND Id_Estudiante=" . $idEstudiante . " AND Estado_Inscripcion = " . $estado . "");
                            $resultado = mysqli_fetch_row($query2);

                            $query3 = $obj->execute("SELECT Id_Conferencia, Numero_Maximo FROM conferencias WHERE Id_Conferencia = " . $idConferencia[0] . " AND Estado_Conferencia = " . $estado . "");
                            $numero_maximo = mysqli_fetch_row($query3);

                            $query4 = $obj->execute("SELECT Id_Conferencia, COUNT(Id_Estudiante) AS Cantidad FROM inscripciones WHERE Id_Conferencia = " . $numero_maximo[0] . " ");
                            $personas = mysqli_fetch_row($query4);

                            if ($personas[1] >= $numero_maximo[1]) {
                                $inactivo = "'I'";
                                $query5 = $obj->execute("UPDATE conferencias SET Estado_Conferencia = " . $inactivo . " WHERE Id_Conferencia = " . $capacidad[0] . "");
                            }

                            if (mysqli_num_rows($query2) == 0) {
                                $insert = $obj->execute("INSERT INTO inscripciones (No_Reg_I, Id_Estudiante, Id_Conferencia, Fecha_Inscripcion, Hora_Inscripcion, Estado_Inscripcion) VALUES (NULL, " . $idEstudiante . ", " . $idConferencia[0] . ", " . $fechaActual . ", " . $horaActual . ", " . $estado . ")");
                                $code = $idEstudiante;
                                $respuesta["tipoRespuesta"] = "success";
                            } else {
                                array_push($noConferencia, $i + 1);
                                $respuesta["tipoRespuesta"] = "error";
                                $respuesta["noConferencia"] = $noConferencia;
                            }
                        }
                    }
                    if (!empty($code)) {
                        // Crea un código QR con este texto y lo muestra
                        QRcode::png($code, $pngAbsoluteFilePath, $level, $size, 3);
                        $file = file_get_contents($pngAbsoluteFilePath, FILE_USE_INCLUDE_PATH);
                        $imageData = base64_encode(($file));
                        $src = 'data:' . mime_content_type($pngAbsoluteFilePath) . ';base64,' . $imageData;
                        $respuesta["codigoQR"] = $src;
                    }
                    echo json_encode($respuesta);
                }
                break;

            //============================================================================================\\
            //================================== CASE INSCRIBIR ESTUDIANTE =====================================\\
            //=============================================================================================\\

            case "inscribirEstudiante":

                if (isset($_POST["estudiante"])) {
                    $limite = count($_POST["estudiante"]);
                    $respuesta = array();
                    $noEstudiante = array();
                    $idRegistro = "'" . $_POST["idRegistro"] . "'";

                    for ($i = 0; $i < $limite; $i++) {
                        if ($limite >= 2) {
                            date_default_timezone_set("America/Bogota");
                            $idSesionEstudiante = "'" . $_SESSION["Id_Estudiante"] . "'";
                            $idEstudiante = "'" . $_POST["idEstudiante"][$i] . "'";
                            $nombreCompleto = "'" . $_POST["estudiante"][$i] . "'";
                            $fechaActual = "'" . date("Y-m-d") . "'";
                            $horaActual = "'" . date('G:i:s') . "'";
                            $estado = "'A'";

                            $query = $obj->execute("SELECT Id_Estudiante FROM registromaratonprogramacion WHERE Id_Estudiante = " . $idEstudiante . " AND Estado_Registro = " . $estado . "");
                            $resultado = mysqli_fetch_row($query);

                            if (mysqli_num_rows($query) == 0) {
                                $insert = $obj->execute("INSERT INTO registromaratonprogramacion
                                (Id_Registro, Id_Estudiante, Fecha_Inscripcion, Hora_Inscripcion, Id_Estudiante_Realiza_Inscripcion, Estado_Registro)
                                VALUES (" . $idRegistro . ", " . $idEstudiante . ", " . $fechaActual . ", " . $horaActual . ", " . $idSesionEstudiante . ", " . $estado . ")");
                                $respuesta["tipoRespuesta"] = "success";
                            } else {
                                array_push($noEstudiante, $i + 1);
                                $respuesta["tipoRespuesta"] = "error";
                                $respuesta["noEstudiante"] = $noEstudiante;
                            }
                        }
                    }
                    echo json_encode($respuesta);
                }
                break;

            //==============================================================================================\\
            //================================== CASE VALIDAR REGISTRO MARATÓN ===================================\\
            //==============================================================================================\\

            case "validarRegistroMaraton":
                $idEstudiante = $_SESSION['Id_Estudiante'];
                $estado = "'A'";
                $query = $obj->execute("SELECT Id_Registro FROM registromaratonprogramacion WHERE Id_Estudiante = " . $idEstudiante . " AND Estado_Registro = " . $estado . "");
                $idRegistro = mysqli_fetch_array($query);
                $query2 = $obj->execute("SELECT DISTINCT a.Id_Registro, CONCAT (b.Nombres, ' ', b.Apellido1, ' ', b.Apellido2) AS Nombre_Completo FROM registromaratonprogramacion a, estudiantes b WHERE a.Id_Estudiante = b.Id_Estudiante AND a.Id_Registro = " . "'" . $idRegistro[0] . "'" . " AND Estado_Registro = " . $estado . "");

                if (mysqli_num_rows($query2) != 0) {
                    $verEquipo = '<span style="color: LimeGreen;">Ya estás inscrito</span>
                                            <button style="color: LimeGreen;" class="href" id="botonVerEquipoMaraton" data-toggle="modal" data-target="#equipoMaraton">(Ver equipo)</button>';

                    $respuesta["verEquipo"] = $verEquipo;
                    $respuesta["registroMaraton"] = true;
                } else {
                    $verEquipo = '<span style="color: Red;">No estás inscrito</span>';

                    $respuesta["verEquipo"] = $verEquipo;
                    $respuesta["registroMaraton"] = false;
                }

                echo json_encode($respuesta);

                break;

            //============================================================================================\\
            //================================== CASE TABLA CARGAR ESTUDIANTE MARATÓN ===========================\\
            //=============================================================================================\\

            case 'cargarEstudianteMaraton':

                if (!empty($_SESSION['Id_Estudiante'])) {
                    $idEstudiante = $_SESSION['Id_Estudiante'];
                } else {
                    $idEstudiante = 0;
                }

                $estado = "'A'";
                $listar = $obj->execute("SELECT Id_Estudiante, CONCAT (Nombres, ' ', Apellido1, ' ', Apellido2) AS Nombre_Completo FROM estudiantes WHERE Id_Estudiante = " . $idEstudiante . " AND Estado_Estudiante = " . $estado . "");
                $validarRegistro = $obj->execute("SELECT * FROM registromaratonprogramacion WHERE Id_Estudiante = " . $idEstudiante . " AND Estado_Registro = " . $estado . "");

                if (mysqli_num_rows($validarRegistro) == 0) {
                    while ($row = mysqli_fetch_array($listar)) {
                        $datos =
                        array(
                            "idEstudiante" => $row["Id_Estudiante"],
                            "nombres" => trim(str_replace("*", "", $row["Nombre_Completo"])),
                            "idRegistro" => strtoupper(substr($row["Nombre_Completo"], 0, 2)),
                            "tipoRespuesta" => "success",
                        );
                    }
                } else {
                    $datos["tipoRespuesta"] = "error";
                }

                echo json_encode($datos);

                break;

            //============================================================================================\\
            //================================== CASE TABLA EQUIPO MARATÓN =====================================\\
            //=============================================================================================\\

            case 'tablaEquipoMaraton':

                if (!empty($_SESSION['Id_Estudiante'])) {
                    $idEstudiante = $_SESSION['Id_Estudiante'];
                } else {
                    $idEstudiante = 0;
                }

                $estado = "'A'";
                $query = $obj->execute("SELECT Id_Registro FROM registromaratonprogramacion WHERE Id_Estudiante = " . $idEstudiante . " AND Estado_Registro = " . $estado . "");
                $idRegistro = mysqli_fetch_array($query);
                $listar = $obj->execute("SELECT DISTINCT a.Id_Registro, a.Id_Estudiante, a.Id_Estudiante_Realiza_Inscripcion, CONCAT (b.Nombres, ' ', b.Apellido1, ' ', b.Apellido2) AS Nombre_Completo FROM registromaratonprogramacion a, estudiantes b WHERE a.Id_Estudiante = b.Id_Estudiante AND a.Id_Registro = " . "'" . $idRegistro[0] . "'" . " AND Estado_Registro = " . $estado . "");
                $datos = array();

                while ($row = mysqli_fetch_array($listar)) {
                    array_push($datos,
                        array(
                            "idSessionEstudiante" => $_SESSION["Id_Estudiante"],
                            "idEstudiante" => $row["Id_Estudiante"],
                            "idEstudianteRealizaInscripcion" => $row["Id_Estudiante_Realiza_Inscripcion"],
                            "idRegistro" => $row["Id_Registro"],
                            "nombres" => trim(str_replace("*", "", $row["Nombre_Completo"])),
                            "eliminarParticipante" => '<button type="button" class="text-white btn fa fa-trash" style="background: #428BCA;"></button>',
                        ));
                }

                $tabla = array("data" => $datos);

                echo json_encode($tabla);

                break;

            //============================================================================================\\
            //================================== CASE CARGAR QR =============================================\\
            //=============================================================================================\\

            case "cargarQR":

                if (!empty($_SESSION['Id_Estudiante'])) {
                    $idEstudiante = $_SESSION['Id_Estudiante'];
                } else {
                    $idEstudiante = 0;
                }

                // Carpeta temporal
                $tempDir = "../../public/temp/";
                // Comprobar si la carpeta existe
                if (!file_exists($tempDir)) {
                    mkdir($tempDir, 0700);
                }
                // Nombre QR
                $u = $obj->uuId();
                $filename = $u . ".png";
                $pngAbsoluteFilePath = $tempDir . $filename;
                $size = 10;
                $level = 'L';
                $code = $_SESSION["Id_Estudiante"];
                $respuesta = array();

                if (!empty($code)) {
                    // Crea un código QR con este texto y lo muestra
                    QRcode::png($code, $pngAbsoluteFilePath, $level, $size, 3);
                    $file = file_get_contents($pngAbsoluteFilePath, FILE_USE_INCLUDE_PATH);
                    $imageData = base64_encode(($file));
                    $src = 'data:' . mime_content_type($pngAbsoluteFilePath) . ';base64,' . $imageData;
                    $respuesta['codigoQR'] = $src;
                }

                $sql = $obj->execute("SELECT COUNT(No_Reg_I) AS Cantidad FROM inscripciones WHERE Id_Estudiante=" . $idEstudiante . " AND Estado_Inscripcion = 'A'");

                $row = mysqli_fetch_array($sql);

                $respuesta["Cantidad"] = $row["Cantidad"];
                $respuesta["codigoQR"] = $src;

                echo json_encode($respuesta);

                break;

            //============================================================================================\\
            //================================== CASE TABLA MODAL ACTIVIDADES ===================================\\
            //=============================================================================================\\

            case 'tablaModalActividades':

                if (!empty($_SESSION['Id_Estudiante'])) {
                    $idEstudiante = $_SESSION['Id_Estudiante'];
                } else {
                    $idEstudiante = 0;
                }

                $listar = $obj->execute("SELECT a.No_Reg_I, a.Id_Conferencia, b.Titulo, b.Fecha, DATE_FORMAT(b.Hora,'%h:%i %p') AS Hora, b.Sede, b.Nombre_Expositor
                FROM inscripciones a, conferencias b
                WHERE a.Id_Conferencia=b.Id_Conferencia
                AND a.Id_Estudiante=" . $idEstudiante . " AND a.Estado_Inscripcion = 'A' ORDER by a.Id_Conferencia ASC");

                $cantidad = $obj->execute("SELECT COUNT(No_Reg_I) AS Cantidad FROM inscripciones WHERE Id_Estudiante=" . $idEstudiante . " AND Estado_Inscripcion = 'A'");
                $row2 = mysqli_fetch_array($cantidad);
                $datos = array();

                while ($row = mysqli_fetch_array($listar)) {
                    array_push($datos,
                        array(
                            "no_reg_i" => $row["No_Reg_I"],
                            "cantidad" => $row2["Cantidad"],
                            "nombre" => $row["Titulo"],
                            "fecha" => $row["Fecha"],
                            "hora" => $row["Hora"],
                            "sede" => $row["Sede"],
                            "expositor" => $row["Nombre_Expositor"],
                            "estado" => '<i class="fa fa-check"></i>',
                            "eliminar" => '<button type="button" class="text-white btn fa fa-trash" style="background: #428BCA;"></button>',
                        ));
                }

                $tabla = array("data" => $datos);

                echo json_encode($tabla);

                break;

            //============================================================================================\\
            //================================== CASE ELIMINAR INSCRIPCIÓN =============================================\\
            //=============================================================================================\\

            case "eliminarInscripcion":

                $No_Reg_I = $_POST["No_Reg_I"];
                $estado = "'I'";

                if (!empty($No_Reg_I)) {
                    $query = $obj->execute("UPDATE inscripciones SET Estado_Inscripcion = " . $estado . " WHERE No_Reg_I = " . $No_Reg_I . "");
                }
                break;

            //============================================================================================\\
            //================================== CASE ELIMINAR PARTICIPANTE =============================================\\
            //=============================================================================================\\

            case "eliminarParticipante":

                $Id_Estudiante = $_POST["Id_Estudiante"];
                $estado = "'I'";

                if (!empty($Id_Estudiante)) {
                    $query = $obj->execute("UPDATE registromaratonprogramacion SET Estado_Registro = " . $estado . " WHERE Id_Estudiante = " . $Id_Estudiante . "");
                }
                break;
        }
    }
}