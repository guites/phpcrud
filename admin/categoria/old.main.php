<?php
if(!$usuario->isLoggedIn()) {
  $_SESSION["unauthorized"] = true;
  header("location: /");
  exit();
}
// Define variáveis a serem utilizadas no cadastro
$dsCategoria = "";
$categoria_err = $database_err = "";
$categoria = new Categoria($connection);
if($_SERVER['REQUEST_METHOD'] == 'POST') {
  if($_POST['action'] == 'create') {
    $dsCategoria = trim($_POST['dsCategoria']);
    if (empty($dsCategoria)){
      $categoria_err = "Digite uma descrição para sua categoria.";
    }
    ## fazer uma validação melhor dessa caralha
    if(empty($categoria_err)) {
      $result = $categoria->insert(
        array(
          "dsCategoria" => $dsCategoria
        )
      );
      if (empty($result['error'])) {
        $_SESSION['categoria_criada'] = true;
        $dsCategoria = "";
        $categoria_err = $database_err = "";
      } else {
        $_SESSION['categoria_criada_erro'] = true;
      }
    }
  } else if ($_POST['action'] == 'delete') {
    $result = $categoria->deleteRecord($_POST['idCategoria']);
    if (empty($result['error'])) {
      $_SESSION['categoria_deletada'] = true;
    } else {
      $_SESSION['categoria_deletada_erro'] = true;
    }
  }
}

echo "<h1 class='mt-4'>Categorias</h1>";
echo "<div class='bd-content'>";
$categoria->flashMessage("categoria_criada", "success", "Categoria cadastrada com sucesso!");
$categoria->flashMessage("categoria_deletada", "success", "Categoria deletada com sucesso!");
$categoria->flashMessage("categoria_criada_erro", "danger", "Erro ao criar categoria! Verifique os campos e tente novamente.");
$categoria->flashMessage("categoria_deletada_erro", "danger", "Erro ao deletar categoria!");
echo $categoria->createForm(
  "create",
  array($categoria_err),
  array($dsCategoria),
  $database_err
);
echo "<h2 class='mt-4' >Categorias Existentes</h2>";
$contagemProdutos = $categoria->showCountProducts();
echo $categoria->adminTableList($contagemProdutos);
echo "</div>";
