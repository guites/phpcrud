<?php
if(!$usuario->isLoggedIn()) {
  $_SESSION["unauthorized"] = true;
  header("location: /");
  exit();
}
// Define variáveis a serem utilizadas no cadastro
$dsProduto = $nmProduto = $idCategoria = "";
$ds_produto_err = $nm_produto_err = $id_categoria_err = $database_err = "";
$produto = new Produto($connection);
if($_SERVER['REQUEST_METHOD'] == 'POST') {
  if($_POST['action'] == 'create') {
    $nmProduto = trim($_POST['nmProduto']);
    $dsProduto = trim($_POST['dsProduto']);
    $idCategoria = trim($_POST['idCategoria']);
    if (empty($idCategoria)){
      $id_categoria_err = "Escolha uma categoria para seu produto.";
    }
    if (empty($dsProduto)){
      $ds_produto_err = "Digite uma descrição para seu produto.";
    }
    if (empty($nmProduto)){
      $nm_produto_err = "Digite um nome para seu produto.";
    }
    ## fazer uma validação melhor dessa caralha
    if(empty($id_produto_err) && empty($nm_produto_err) && empty($id_categoria_err)) {
      $result = $produto->insert(
        array(
          "nmProduto" => $nmProduto,
          "dsProduto" => $dsProduto,
          "idCategoria" => $idCategoria
        )
      );
      if (!($result['error'])) {
        $_SESSION['produto_create'] = true;
        $dsProduto = $nmProduto = $idCategoria = "";
        $ds_produto_err = $nm_produto_err = $id_categoria_err = $database_err = "";
      } else {
        $_SESSION['produto_create_error'] = true;
      }
    }
  } else if ($_POST['action'] == 'delete') {
    $result = $produto->deleteRecord($_POST['idProduto']);
    if (empty($result['error'])) {
      $_SESSION['produto_delete'] = true;
    } else {
      $_SESSION['produto_delete_error'] = true;
    }
  }
}

echo "<h1 class='mt-4'>Página dos Produtos</h1>";
echo "<div class='bd-content'>";
$produto->flashMessage("produto_create", "success", "Produto cadastrado com sucesso!");
$produto->flashMessage("produto_delete", "success", "Produto deletado com sucesso!");
$produto->flashMessage("produto_create_error", "danger", "Erro ao criar produto! Verifique os campos e tente novamente.");
$produto->flashMessage("produto_delete_error", "danger", "Erro ao deletar produto!");
echo $produto->createForm(
  array($nm_produto_err, $ds_produto_err, $id_categoria_err),
  array($nmProduto, $dsProduto, $idCategoria),
  $database_err
);
echo "<h2 class='mt-4'>Produtos Existentes</h2>";
$produtos = $produto->getAll();
echo $produto->adminTableList($produtos);
echo "</div>";
