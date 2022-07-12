<?php

// RUTA ABSOLUTA DE LA APLICACIÓN
//define("APP_PATH", str_replace("\\", "/", __DIR__) . "/../") . chdir(APP_PATH);

// URL ABSOLUTA DE LA APLICACIÓN
define("APP_URL", "http://" . $_SERVER["HTTP_HOST"] . "/" . str_replace("C:/xampp/htdocs/", "", str_replace("\\", "/", getcwd())));

// URL ABSOLUTA DE LA CARPETA PUBLIC
define("PUBLIC_URL", APP_URL . "/public");
