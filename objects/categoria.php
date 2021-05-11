<?php

class Categoria extends Objeto {
  
  function __construct($connection){
    parent::__construct($connection);
    $this->table_name = "Categoria";
    $this->connection = $connection;
  }

  public $idCategoria;
  public $dsCategoria;

  function createCategory() {
    
  
  }

  public function showCountProducts() {
  
    $query = "SELECT c.idCategoria, c.dsCategoria, COUNT(p.idCategoria) as quantidade FROM Categoria c LEFT JOIN Produto p ON c.idCategoria = p.idCategoria GROUP BY
c.dsCategoria, c.idCategoria";
    $stmt = $this->connection->prepare($query);
    $stmt->execute();
    $results = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
      $results[] = $row;
    }
    return $results;
  
  }

  public function createForm($errors, $objeto, $database_err, $enctype = NULL) 
  {
    # $obj é um array que guarda as informações necessárias para cada campo
    # cada campo é um array
    # [Label, input type,valor para manter preenchido, mensagem de erro, HTML ATTRIBUTES]
    $obj = array(
      "dsCategoria" => ["Descrição da Categoria","text",$objeto[0],$errors[0], array("required" => "required")],
    );

    return parent::createForm("create",$obj,$database_err);
  }

  public function updateForm($errors, $objeto, $database_err) 
  {
    # $obj é um array que guarda as informações necessárias para cada campo
    # cada campo é um array
    # [Label, input type, mensagem de erro, valor para manter preenchido, flag de erro, HTML ATTRIBUTES]
    $obj = array(
      "idCategoria" => ["Código da Categoria","text",$objeto[0],$errors[0],array("required"=>"required","readonly"=>"readonly")],
      "dsCategoria" => ["Descrição da Categoria","text","",$errors[1], array("required" => "required","placeholder"=>$objeto[1])],
    );

    return parent::createForm("update",$obj,$database_err);
  }

}
