<?php
//chamando a função pelo ajax
     $action = $_GET['action'];
     if($action == 'calcular'){
         calcular();
     } 

     
     function calcular(){
         $esp = $_POST['espessura'];

         $peso_concreto = 2.550;
         $sobrecarga_momento = 0.204;
         $sobrecarga_flecha = 0.102;

         $resultado = ( $esp * $peso_concreto + $sobrecarga_momento )

         $resposta = 'Resultado: ' . $resultado . '<br>';

         echo json_encode($resposta);
     }

    

 