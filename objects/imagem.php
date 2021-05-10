<?php

class Imagem extends Objeto {

  function __construct($connection){
    parent::__construct($connection);
    $this->table_name = "Imagem";
  }

  public $idImagem;
  public $dsImagem;
  public $nomeDoArquivo;
  public $idProduto;

}
