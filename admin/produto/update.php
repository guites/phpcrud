<?php
require "../../functions.php";
require "../../header.php";
if(!$usuario->isLoggedIn()) {
  $_SESSION["unauthorized"] = true;
  header("location: /");
  exit();
}
// Define variáveis a serem utilizadas no cadastro
$dsProduto = $nmProduto = $idCategoria = "";
$ds_produto_err = $nm_produto_err = $id_categoria_err = $database_err = "";
$produto = new Produto($connection);
if($_SERVER['REQUEST_METHOD'] == 'GET') {
  if ($_GET['id']) {
    $id = trim($_GET['id']);
    if($result = $produto->checkId($id)) {
      if(!empty($result["error"])){
        header("location: /admin");
        exit();
      } else {
        $objeto = $result['objeto'];
      }
    }
  } else {
    header("location: /admin");
    exit();
  }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {

  if ($_POST['action'] == 'update') {
    $idProduto = trim($_POST['idProduto']);
    $nmProduto = trim($_POST['nmProduto']);
    $dsProduto = trim($_POST['dsProduto']);
    $idCategoria = trim($_POST['idCategoria']);
    $objeto[0] = $idProduto;
    $objeto[1] = $nmProduto;
    $objeto[2] = $dsProduto;
    $objeto[3] = $idCategoria;
    if (empty($idProduto)) {
      $id_produto_err = 'Requisição inválida.';
    }
    if (empty($nmProduto)){
      $nm_produto_err = 'Digite um nome para seu produto.';
    }
    if (empty($dsProduto)){
      $ds_produto_err = 'Digite uma descrição para seu produto.';
    }
    if (empty($idCategoria)){
      $id_categoria_err = 'Digite um código de categoria para seu produto.';
    }
    if (empty($id_produto_err) && empty($nm_produto_err) && empty($ds_produto_err) && empty($id_categoria_err)) {
      $result = $produto->update(
        array(
          "id" => $idProduto,
          "campos" => array(
            "nmProduto" => $nmProduto,
            "dsProduto" => $dsProduto,
            "idCategoria" => $idCategoria
          )
        )
      );
      if (empty($result['error'])) {
        $_SESSION['produto_update'] = true;
        $ds_produto_err = $nm_produto_err = $id_categoria_err = $database_err = "";
        $objeto = $result['objeto'];
      } else {
        $_SESSION['produto_update_erro'] = true;
      }
    }
  }
}

$produto->flashMessage("produto_update", "success", "Produto atualizado com sucesso!");
$produto->flashMessage("produto_update_erro", "danger", "Erro ao atualizar produto!");

echo $produto->updateForm(
  array($ds_produto_err,$nm_produto_err,$id_categoria_err,$database_err),
  $objeto,
  $database_err,
);

require "../../footer.php";
