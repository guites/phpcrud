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

  public function createForm($errors, $objeto, $database_err) {
    $obj = array(
      "dsImagem" => ["Descrição da Imagem","text",$objeto[0],$errors[0],array("required" => "required")],
      "nomeDoArquivo" => ["Imagem [PNG e JPEG]", "file", $objeto[1], $errors[1], array("required" => "required")],
      "idProduto" => ["Código do Produto", "text", $objeto[2], $errors[2], array("required"=>"required","readonly"=>"readonly")]
    );
    return parent::createForm("create",$obj,$database_err);
  }

}
