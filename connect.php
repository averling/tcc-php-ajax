<?php
  # Conexão
  
  # Escolhendo o banco de dados
  
  # Realiza a consulta na tabela
  // $query = "SELECT * from compensado";
  
  # Filtra através das linhas de consulta
  // var_dump($result);
  // while ($row = mysql_fetch_object($result)) {
  //     echo $row->ID;
  // }  

  class Connect{
    public function __construct(){
      mysql_connect('localhost', 'root', '12345678') or die('Não foi possível conectar ao banco de dados: ' . mysql_error());    
      mysql_select_db('tcc') or die('Não foi possível selecionar o banco de dados');
    }

    public function RetornarCompensados(){
      $query = "select * from compensado";
      $resultado = $result = mysql_query($query) or die('Falha na instrução SQL: ' . mysql_error());
      $array = array('id' => array(),'esp_comp' => array());
      while ($row = mysql_fetch_object($result)) {
        // var_dump($row);
          array_push($array['id'], $row->ID);
          array_push($array['esp_comp'], $row->esp_comp);
      }
      return $array;
    }

    public function RetornarPerfis(){
      $query = "select * from perfil";
      $resultado = $result = mysql_query($query) or die('Falha na instrução SQL: ' . mysql_error());
      $array = array('id' => array(),'nome_perfil' => array());
      while ($row = mysql_fetch_object($result)) {
        // var_dump($row);
          array_push($array['id'], $row->ID);
          array_push($array['nome_perfil'], $row->nome_perfil);
      }
      return $array;
    }

    public function RetornarDadosCompensado($ID){
      $query = "select * from compensado where ID=$ID";
      $resultado = $result = mysql_query($query) or die('Falha na instrução SQL: ' . mysql_error());
      $array = array('id','esp_comp','momento_adm','j_comp', 'e_comp', 'peso_proprio');
      while ($row = mysql_fetch_object($result)) {
        // var_dump($row);
          $array['id']=$row->ID;
          $array['esp_comp']=$row->esp_comp;
          $array['momento_adm']=$row->momento_adm;
          $array['e_comp']=$row->e_comp;
          $array['j_comp']=$row->j_comp;
          $array['peso_proprio']=$row->peso_proprio;
      }
      return $array;
    }

    public function RetornarChapas(){
      $query = "select * from chapa_comp";
      $resultado = $result = mysql_query($query) or die('Falha na instrução SQL: ' . mysql_error());
      $array = array('id' => array(),'tipo_chapa' => array());
      while ($row = mysql_fetch_object($result)) {
        $array['id'][] = $row->ID;
        $array['tipo_chapa'][] = $row->tipo_chapa;
          
      }
      return $array;
    }

    public function RetornarDadosChapas($id){
      $query = "select * from valor_chapa where id_chapa = $id order by valor";
      $resultado = $result = mysql_query($query) or die('Falha na instrução SQL: ' . mysql_error());
      $array = array('valor' => array());
      while ($row = mysql_fetch_object($result)) {
        $array['valor'][] = $row->VALOR;
          
      }
      return $array;
    }




  }

?>