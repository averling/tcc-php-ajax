<?php
    require_once('connect.php');
    $con = new Connect();

    $var = array(
    	'compensado' => $con->RetornarCompensados(),
    	'perfil' => $con->RetornarPerfis()
    	);
    
    extract((array)$var, EXTR_PREFIX_SAME, 'data');
    require_once('escoramento-de-laje.html');