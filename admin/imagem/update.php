<?php
require "../../functions.php";
require "../../header.php";
if(!$usuario->isLoggedIn()) {
  $_SESSION["unauthorized"] = true;
  header("location: /");
  exit();
}
// Define variáveis a serem utilizadas no cadastro
$dsCategoria = $idCateogira = "";
$id_categoria_err = $ds_categoria_err = $database_err = "";
$categoria = new Categoria($connection);
if($_SERVER['REQUEST_METHOD'] == 'GET') {
  if ($_GET['id']) {
    $id = trim($_GET['id']);
    if($result = $categoria->checkId($id)) {
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
    $dsCategoria = trim($_POST['dsCategoria']);
    $idCategoria = trim($_POST['idCategoria']);
    $objeto[0] = $idCategoria;
    $objeto[1] = $dsCategoria;
    if (empty($dsCategoria)){
      $ds_categoria_err = 'Digite uma descrição para sua categoria.';
    }
    if (empty($idCategoria)) {
      $id_categoria_err = 'Requisição inválida.';
    }
    if (empty($ds_categoria_err) && empty($id_categoria_err)) {
      $result = $categoria->update(
        array(
          "id" => $idCategoria,
          "campos" => array(
            "dsCategoria" => $dsCategoria
          )
        )
      );
      if (empty($result['error'])) {
        $_SESSION['categoria_update'] = true;
        $ds_categoria_err = $id_categoria_err = $database_err = "";
        $objeto = $result['objeto'];
      } else {
        $_SESSION['categoria_update_erro'] = true;
      }
    }
  }

}

$categoria->flashMessage("categoria_update", "success", "Categoria atualizada com sucesso!");
$categoria->flashMessage("categoria_update_erro", "danger", "Erro ao atualizar categoria!");

echo $categoria->updateForm(
  array($id_categoria_err, $ds_categoria_err),
  $objeto,
  $database_err,
);

require "../../footer.php";
