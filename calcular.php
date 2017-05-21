<?php
//chamando a função pelo ajax
     $action = $_GET['action'];
     if($action == 'calcular'){
         calcular();
     } 

     if($action == 'PesoDeLaje'){
         PesoDeLaje();
     }

     if($action == 'Flecha'){
         Flecha();
     }

     if($action = 'CalcularVao'){
        CalcularVao();
     }

     //Calculo do peso da laje pelo momento
     function CalcularMomento($Espessura){
         $peso_concreto = 2.550;
         $sobrecarga_momento = 0.204;
        //  $sobrecarga_flecha = 0.102;

        return (float)$Espessura * $peso_concreto + $sobrecarga_momento;

     }

     //Calculo do peso da laje pela flecha
     function CalcularFlecha($Espessura){
        $peso_concreto = 2.550;
        $sobrecarga_flecha = 0.102;

        return (float) $Espessura * $peso_concreto + $sobrecarga_flecha;
     }

     //Validação da espessura e resposta das funções de calculo de peso
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

     //calculo do vão pelo momento
     function Momento($momento, $q, $peso_proprio_compensado){
        $peso = ($q + $peso_proprio_compensado/1000)*10; //mudança de tf para kgf
        $tmp = 8 * $momento;
        // echo $tmp;
        $tmp = $tmp/$peso;
        return sqrt($tmp);
        // return sqrt((8*$momento)/$q);

     }

     //Calculo da flecha admissivel
     function FlechaAdm($l){
        $vao = $l*10;//mudança de cm para mm
        return (($vao/500)+1);
     }
    //Calculo do vão pela Flecha 
    function Flecha($l,$carga,$e,$j, $peso_proprio_compensado){
        $vao = $l/10; //mudança de mm para cm
        $peso = ($carga + $peso_proprio_compensado/1000) *10; //mudança de tf para kgf
        //echo pow((0.217*384*70000*28.125)/(5*4.955),1/4);
        return pow(($vao*384*$e*$j)/(5*$peso),1/4);

    }

    //Validação dos parametos e resultados dos calculos de vão
    function CalcularVao(){
        // $ID_compensado = isset($_POST['ID_compensado']) ? $_POST['ID_compensado'] : null;
        // $Chapa = isset($_POST['tipo_comp']) ? $_POST['tipo_com'] : null;
        // $Espessura = isset($_POST['Espessura']) ? $_POST['Espessura'] : null;
        // $Espessura = (float) str_replace(',' , '.', $Espessura);
        
        $ID_compensado = 4;
        $Espessura = 0.15;
        $Chapa = 1;
        

        if($Espessura == null || $Espessura <= 0){
            $resposta = array(
                'Erro' => "Valor da espessura inválido."
            );
            echo json_encode($resposta);
            return false;
        }

        if($Chapa == null || $Chapa == 0){
            $resposta = array('Erro' => 'Selecione um tipo de compensado.');
            echo json_encode($resposta);
            return false;
        }

        if($ID_compensado == null || $ID_compensado ==0){
            $resposta = array('Erro'=>'Compensado não encontrado ou invalido. Selecione novamente');
            echo json_encode($resposta);
            return false;
        }else{

            require_once('connect.php');
            $con = new Connect();

            $compensado = $con->RetornarDadosCompensado($ID_compensado); 

            $q = CalcularMomento($Espessura);
            $momento = Momento($compensado['momento_adm'],$q, $compensado['peso_proprio']);
            $carga = CalcularFlecha($Espessura);
            $flecha_adm = FlechaAdm($momento);
            $flecha = Flecha($flecha_adm,$carga,$compensado['e_comp'],$compensado['j_comp'], $compensado['peso_proprio']);
            $resposta = array('Vao');

            $valoresChapa = $con->RetornarDadosChapas($Chapa);

            $aproximado = array('indice', 'diferenca');

            echo 'Q: ' . $q;
            echo '<br>Espessura: ' . $Espessura;
            // echo '<br>Q: ' . CalcularMomento($Espessura);
            echo '<br>Compensado: ' . $compensado['momento_adm'];
            echo '<br>peso proprio compensado:' . $compensado['peso_proprio'];
            echo '<br>Momento: ' . $momento;
            echo '<br>Carga: ' . $carga;
            echo '<br>Flecha Adm: ' . $flecha_adm;
            echo '<br>Flecha: ' . $flecha; 
            
            if ($momento > $flecha){

                $resposta['Vao']=$flecha;
                for($i = 0; $i < count($valoresChapa); $i++){
                    $aproximado['indice'] = $i;
                    $aproximado['diferenca'] = $resposta['Vao'] - $valoresChapa['valor'][$i];
                }
            }else{
                $resposta['Vao']=$momento;
                for($i = 0; $i < count($valoresChapa); $i++){
                    $aproximado['indice'] = $i;
                    $aproximado['diferenca'] = $resposta['Vao'] - $valoresChapa['valor'][$i];
                }
            }
            // echo json_encode($resposta);
        }
    }