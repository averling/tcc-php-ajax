<?php
	
	$espessura = 0.15;
	$peso_concreto = 2550;
	$sobrecarga_flecha =  102;




	$peso_da_laje = $espessura * $peso_concreto + $sobrecarga_flecha;

	$peso_compensado = 9;//compensado de 15 da tabela compensado. id = 4
	$peso_perfil = 2 * 6.5; //tabela perfil perfil c-7.5 id = 2
	echo 'Peso da laje: ' . $peso_da_laje . '<br />';
	echo 'Peso do compensado: ' . $peso_compensado . '<br />';
	echo 'Peso do perfil: ' . $peso_perfil . '<br />';


	$resultado = $peso_da_laje + $peso_compensado + $peso_perfil;
	echo 'pela flecha: ' . $resultado;

?>