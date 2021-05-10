<?php

class Produto extends Objeto {
  
  function __construct($connection){
    parent::__construct($connection);
    $this->connection = $connection;
    $this->table_name = "Produto";
  }

  public $idProduto;
  public $nmProduto;
  public $dsProduto;
  public $idCategoria;

  public function getFromCategory($catId){
    $query = "
    SELECT p.idProduto, p.nmProduto, p.dsProduto,
      GROUP_CONCAT(DISTINCT nomeDoArquivo ORDER BY nomeDoArquivo) AS nomeDoArquivo,
      GROUP_CONCAT(DISTINCT dsImagem ORDER BY nomeDoArquivo) as dsImagem
      FROM Produto p
      LEFT JOIN Imagem i ON p.idProduto = i.idProduto
      WHERE p.idCategoria = $catId
      GROUP BY p.idProduto, p.nmProduto, p.dsProduto;
      ";
    $results = [];
    $stmt = $this->connection->prepare($query);
    $stmt->execute();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $results[] = $row;
    }
    return $results;
  }

  public function produtoHomeHtml($prod) {
    $imgs = $prod['nomeDoArquivo'];
    $dsImgs = $prod['dsImagem'];
    $id = $prod['idProduto'];
    $nome = $prod['nmProduto'];
    $descr = $prod['dsProduto'];
    $html = "
    <div class='col-lg-4'>
    ";
    if (!empty($imgs) && !empty($dsImgs)) {
      $img_arr = explode(',',$imgs);
      $dsImgs_arr = explode(',',$dsImgs);
      foreach($img_arr as $key => $i) {
        $html .= "<img src='$i' title='$dsImgs_arr[$key]' alt='$dsImgs_arr[$key]'/>";
      }
    }
    $html .= "
      <h4 class='card-title'>$nome</h4>
      <small>código $id</small>
      <p>$descr</p>
    </div>
    ";
    echo $html;
  }

  public function createForm($errors, $objeto, $database_err) 
  {
    # $obj é um array que guarda as informações necessárias para cada campo
    # cada campo é um array
    # [Label, input type,valor para manter preenchido, mensagem de erro, HTML ATTRIBUTES]
    $obj = array(
      "nmProduto" => ["Nome do Produto","text",$objeto[0],$errors[0], array("required" => "required")],
      "dsProduto" => ["Descrição do Produto","text",$objeto[1],$errors[1], array("required" => "required")],
      "idCategoria" => ["Código da Categoria","text",$objeto[2],$errors[2], array("required" => "required")],
    );

    return parent::createForm("create",$obj,$database_err);
  }

  public function updateForm($errors, $objeto, $database_err) 
  {
    $obj = array(
      "idProduto" => ["Código do Produto", "text", $objeto[0],$errors[0], array("required" => "required", "readonly" => "readonly")],
      "nmProduto" => ["Nome do Produto","text",$objeto[1],$errors[1], array("required" => "required")],
      "dsProduto" => ["Descrição do Produto","text",$objeto[2],$errors[2], array("required" => "required")],
      "idCategoria" => ["Código da Categoria","text",$objeto[3],$errors[3], array("required" => "required")],
    );

    return parent::createForm("update",$obj,$database_err);
  }

  public function createPost() {
    foreach($this as $property => $value) {
      if ($property == 'connection' || $property == 'table_name') continue;
      ${$property} = "";
      ${"err_".$property} = "";
    }
  }

}
