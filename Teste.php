<?php
    class Teste{
        public function teste(){
            $sql = "select * from compensado";
            return $this->objDB->query($sql)->fetchAll();
        }
    }