<?php

class core {

    private $server;
    private $user;
    private $password;
    private $database;
    public $conexion;

    public function __construct() {
        $this->setConect();
        $this->Conect();
    }

    private function setConect() {
        require "configuracion.php";

        $this->server = $server;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;

    }

    public function Conect() {
        $this->conexion = new mysqli($this->server, $this->user, $this->password, $this->database);
        if (mysqli_connect_errno()) {
            echo "No se ha podido establecer conexión con el servidor de bases de datos.<br>", mysqli_connect_error();
            exit();
        } else {
            $this->conexion->set_charset("utf8");
        }
    }

    public function getConect() {
        return $this->conexion;
    }

    public function closeConect() {
        mysqli_close($this->conexion);
    }

    public function execute($sql) {
        $conexion = $this->Conect();
        if ($this->conexion) {
            $result = mysqli_query($this->conexion, $sql);
            return $result;
        } else {
            echo mysqli_errno();
        }
    }

    function uuId($serverID = 1) {
        $t = explode(" ", microtime());
        return sprintf('%04x-%08s-%08s-%04s-%04x%04x',
            $serverID,
            uniqid(),
            substr("00000000" . dechex($t[1]), -8), // get 8HEX of unixtime
            substr("0000" . dechex(round($t[0] * 65536)), -4), // get 4HEX of microtime
            mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
