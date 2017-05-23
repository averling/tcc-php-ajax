<?php
//chamando a função pelo ajax
     $action = $_GET['action'];
     if($action == 'calcular'){
         calcular();
         return false;
     } 

     if($action == 'PesoDeLaje'){
         PesoDeLaje();
         return false;
     }

     if($action == 'Flecha'){
         Flecha();
         return false;
     }

     if($action = 'CalcularVao'){
        CalcularVao();
        return false;
     }

     if($action = 'CalcularVaoPerfilSecundario'){
        CalcularVaoPerfilSecundario();
        return false;
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
        // var_dump($_POST);return false;
        $ID_compensado = isset($_POST['ID_compensado']) ? $_POST['ID_compensado'] : null;
        $Chapa = isset($_POST['tipo_comp']) ? $_POST['tipo_comp'] : null;
        $Espessura = isset($_POST['Espessura']) ? $_POST['Espessura'] : null;
        $Espessura = (float) str_replace(',' , '.', $Espessura);
        // echo $ID_compensado;
        // $ID_compensado = 4;
        // $Espessura = 0.15;
        // $Chapa = 1;
        

        if($Espessura == null || $Espessura <= 0){
            $resposta = array(
                'Erro' => "Valor da espessura inválido."
            );
            header('Content-Type: application/json');
            echo json_encode($resposta);
            return false;
        }

        if($Chapa == null || $Chapa == 0){
            $resposta = array('Erro' => 'Selecione um tipo de compensado.');
            header('Content-Type: application/json');
            echo json_encode($resposta);
            return false;
        }

        if($ID_compensado == null || $ID_compensado ==0){
            $resposta = array('Erro'=>'Compensado não encontrado ou invalido. Selecione novamente');
            header('Content-Type: application/json');
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

            

            // echo 'Q: ' . $q;
            // echo '<br>Espessura: ' . $Espessura;
            // // echo '<br>Q: ' . CalcularMomento($Espessura);
            // echo '<br>Compensado: ' . $compensado['momento_adm'];
            // echo '<br>peso proprio compensado:' . $compensado['peso_proprio'];
            // echo '<br>Momento: ' . $momento;
            // echo '<br>Carga: ' . $carga;
            // echo '<br>Flecha Adm: ' . $flecha_adm;
            // echo '<br>Flecha: ' . $flecha; 

            $auxiliar;
            $menor = array('ref', 'valor_chapa');
            $menor['ref'] = 100;
            $menor['valor_chapa'] = 0;
            if ($momento > $flecha){

                $resposta['Vao']=$flecha;
                $tmp = $resposta['Vao'] / 100;
                for($i = 0; $i < count($valoresChapa['valor']); $i++){
                    
                    $auxiliar = $tmp - $valoresChapa['valor'][$i];
                    // echo '<br><br>ref: ' . $menor['ref'];
                    // echo '<br>diferenca: ' . $auxiliar;
                    // echo '<br>valor chapa: ' . $menor['valor_chapa'];
                    
                    if($menor['ref'] > $auxiliar && $auxiliar > 0){
                        $menor['valor_chapa'] = $valoresChapa['valor'][$i];
                        $menor['ref'] = $auxiliar;
                    }
                }
            }else{
                $resposta['Vao']=$momento;
                $tmp = $resposta['Vao'] / 100;
                
                for($i = 0; $i < count($valoresChapa['valor']); $i++){
                    
                    $auxiliar = $tmp - $valoresChapa['valor'][$i];
                    // echo '<br><br>ref: ' . $menor['ref'];
                    // echo '<br>diferenca: ' . $auxiliar;
                    // echo '<br>valor chapa: ' . $menor['valor_chapa'];
                    
                    if($menor['ref'] > $auxiliar && $auxiliar > 0){
                        $menor['valor_chapa'] = $valoresChapa['valor'][$i];
                        $menor['ref'] = $auxiliar;
                    }
                }
            }
            $resposta['Vao'] = $menor['valor_chapa'];
            header('Content-Type: application/json');
            echo json_encode($resposta);
            // echo '<br><br>Vao: ' . $resposta['Vao'];
            // echo '<br>Tmp: ' . $tmp;
            // echo '<br>Menor: ' . $menor['valor_chapa'];
            
            // echo json_encode($resposta);
        }
    }

    //calculo do vão do perfil secundario pelo momento
     function MomentoPerfilSecundario($momento, $q, $peso_proprio, $vao_atuante ){
        $peso = (($q + $peso_proprio/1000)*10) * ($vao_atuante/100); //mudança de tf para kgf
        $tmp = 8 * $momento;
        // echo $tmp;
        $tmp = $tmp/$peso;
        return sqrt($tmp);
        // return sqrt((8*$momento)/$q);

     }

 //Calculo do vão do perfil secundario pela Flecha 
    function FlechaPerfilSecundario($l,$carga,$e,$j, $peso_proprio, $vao_atuante){
        $vao = $l/10; //mudança de mm para cm
        $peso = (($carga + $peso_proprio/1000) *10) *($vao_atuante/100); //mudança de tf para kgf
        //echo pow((0.217*384*70000*28.125)/(5*4.955),1/4);
        return pow(($vao*384*$e*$j)/(5*$peso),1/4);

    }

//Validação e Respostas do vão do perfil Secundario
 function CalcularVaoPerfilSecundario(){
        
        $ID_perfil = isset($_POST['ID_perfil']) ? $_POST['ID_perfil'] : null;
        $Espessura = isset($_POST['Espessura']) ? $_POST['Espessura'] : null;
        $Espessura = (float) str_replace(',' , '.', $Espessura);
        $vao_atuante = isset($_POST['vao_atuante']) ? $_POST['vao_atuante'] : null;
                
        if($Espessura == null || $Espessura <= 0){
            $resposta = array(
                'Erro' => "Valor da espessura inválido."
            );
            header('Content-Type: application/json');
            echo json_encode($resposta);
            return false;
        }

        if($vao_atuante == null || $vao_atuante <= 0){
            $resposta = array(
                'Erro' => "Vão atuante inválido."
            );
            header('Content-Type: application/json');
            echo json_encode($resposta);
            return false;
        }

        if($ID_perfil== null || $ID_perfil ==0){
            $resposta = array('Erro'=>'Perfil não encontrado ou invalido. Selecione novamente');
            header('Content-Type: application/json');
            echo json_encode($resposta);
            return false;
        }else{

            require_once('connect.php');
            $con = new Connect();

            $perfil = $con->RetornarDadosPerfil($ID_perfil); 

            $q = CalcularMomento($Espessura);
            $momento = MomentoPerfilSecundario($perfil['momento_perfil'],$q, $perfil['peso_perfil'], $vao_atuante);
            $carga = CalcularFlecha($Espessura);
            $flecha_adm = FlechaAdm($momento);
            $flecha = FlechaPerfilSecundario($flecha_adm,$carga,$perfil['e_perfil'],$perfil['j_perfil'], $perfil['peso_perfil'], $vao_atuante);
            $resposta = array('VaoSecundario');

           if($momento > $flecha){

            $resposta['VaoSecundario']=$flecha;
            echo json_encode($resposta);

           } else{

            $resposta['VaoSecundario']=$momento;
            echo json_encode($resposta);
           }        
            
        }
    }