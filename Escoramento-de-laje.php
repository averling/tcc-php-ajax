<?php
    require_once('connect.php');
    $con = new Connect();

    $var = array(
    	'compensado' => $con->RetornarCompensados(),
    	'perfil' => $con->RetornarPerfis(),
    	'chapas' => $con->RetornarChapas()
    	);
    // var_dump($var); return false;
    extract((array)$var, EXTR_PREFIX_SAME, 'data');
    require_once('escoramento-de-laje.html');