<?php
//chamando a função pelo ajax
     $action = $_GET['action'];
     if($action == 'calcular'){
         calcular();
     } 

     if($action == 'PesoDeLaje'){
         PesoDeLaje();
     }

     function CalcularMomento($Espessura){
         $peso_concreto = 2.550;
         $sobrecarga_momento = 0.204;
        //  $sobrecarga_flecha = 0.102;

        return (float)$Espessura * $peso_concreto + $sobrecarga_momento;

     }

     function CalcularFlecha($Espessura){
        $peso_concreto = 2.550;
        $sobrecarga_flecha = 0.102;

        return (float) $Espessura * $peso_concreto + $sobrecarga_flecha;
     }

     function PesoDeLaje(){
        $Espessura = isset($_POST['Espessura']) ? $_POST['Espessura'] : null;
        $Espessura = (float) str_replace(',' , '.', $Espessura);
        if($Espessura == null || $Espessura <= 0){
            $resposta = array(
                'Erro' => "Valor da espessura inválido."
            );
        }else{
            $resposta = array(
                'Momento' => CalcularMomento($Espessura),
                'Flecha' => CalcularFlecha($Espessura)
            );
        }
        header('Content-Type: application/json');
        echo json_encode($resposta);
     }

    

 