<?php
if(!$usuario->isLoggedIn()) {
  $_SESSION["unauthorized"] = true;
  header("location: /");
  exit();
}
// Define variáveis a serem utilizadas no cadastro
$dsImagem = $nomeDoArquivo = $idProduto = "";
$ds_imagem_err = $nome_do_arquivo_err = $id_produto_err = $database_err = "";
$imagem = new Imagem($connection);

if($_SERVER['REQUEST_METHOD'] == 'GET') {
  if ($_GET['id'] && $_GET['object']) {
    $id = trim($_GET['id']);
    $object = trim($_GET['object']);

    # argumentos obrigatórios
    if (empty($id) || empty($object)) {
      header("location: /admin");
      exit();
    } 

    # define à quem está ligada a imagem
    switch ($object) {
      case "Categoria":
        $parent = new Categoria($connection);
        break;
      default:
        $parent = new Produto($connection);
    }

    if($result = $parent->checkId($id)) {
      if(!empty($result["error"])){
        header("location: /admin");
        exit();
      } else {
        $objeto = $result['objeto'];
        # aqui seria uma boa ideia abstrair essa associação com um loop
        $parent->idProduto = $objeto[0];
        $parent->nmProduto = $objeto[1];
        $parent->dsProduto = $objeto[2];
        $parent->idCategoria = $objeto[3];
      }
    }
  } else {
    header("location: /admin");
    exit();
  }
}

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
  } else if ($_POST['action'] == 'update') {
    $dsCategoria = trim($_POST['dsCategoria']);
    $idCategoria = trim($_POST['idCategoria']);
    if (empty($dsCategoria)){
      $categoria_err = 'Digite uma descrição para sua categoria.';
    }
    if (empty($idCategoria)) {
      $categoria_err = 'Requisição inválida.';
    }
    if (empty($categoria_err)) {
      $result = $categoria->update(
        array(
          "idCategoria" => $idCategoria,
          "dsCategoria" => $dsCategoria
        )
      );
      if (empty($result['error'])) {
        $_SESSION['categoria_update'] = true;
        $categoria_err = $database_err = "";
      } else {
        $_SESSION['categoria_update_erro'] = true;
      }
    }
  }
}

echo "<h1 class='mt-4'>Imagens</h1>";
echo "<p>Gerencie aqui as imagens associadas a este produto.</p>";
echo "<h2>" . $parent->nmProduto . "</h2>";
echo "<div class='bd-content'>";
$parent->flashMessage("categoria_criada", "success", "Categoria cadastrada com sucesso!");
$parent->flashMessage("categoria_deletada", "success", "Categoria deletada com sucesso!");
$parent->flashMessage("categoria_update", "success", "Categoria atualizada com sucesso!");
$parent->flashMessage("categoria_criada_erro", "danger", "Erro ao criar categoria! Verifique os campos e tente novamente.");
$parent->flashMessage("categoria_deletada_erro", "danger", "Erro ao deletar categoria!");
$parent->flashMessage("categoria_update_erro", "danger", "Erro ao atualizar categoria!");
echo $imagem->createForm(
  array($ds_imagem_err, $nome_do_arquivo_err, $id_produto_err, $database_err),
  array($dsImagem, $nomeDoArquivo, $parent->idProduto),
  $database_err
);
echo "<h2 class='mt-4' >Imagens existentes</h2>";
$imagens = $imagem->getAll(NULL,NULL,array("column" => "idProduto", "value" => $parent->idProduto));
if (count($imagens) == 0 ) {
  echo "<small>Nenhuma imagem cadastrada! Preencha o formulário acima para começar.</small>";
} else {
  foreach($imagens as $img) {
    echo "<img src='" . $img['nomeDoArquivo'] . "'>";
  }
}
#echo $imagem->adminTableList($imagens);
echo "</div>";
