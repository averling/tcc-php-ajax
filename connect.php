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
  }

?>