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
    if (isset($_FILES['nomeDoArquivo']) && $_FILES['nomeDoArquivo']['error'] == UPLOAD_ERR_OK){
      # verifica se a imagem está atrelada a algum produto.
      $idProduto = trim($_POST['idProduto']);
      if (empty($idProduto)){
        $id_produto_err = "Erro de requisição. A imagem precisa estar associada a um produto.";
      } else {
        # se estiver, verifica se é um produto válido
        $parent = new Produto($connection);

        if($checkId = $produto->checkId($idProduto)) {
          if(!empty($checkId["error"])){
            header("location: /admin");
            exit();
          } else {
            $objeto = $checkId['objeto'];
            # atribui os dados do produto encontrado
            $parent->idProduto = $objeto[0];
            $parent->nmProduto = $objeto[1];
            $parent->dsProduto = $objeto[2];
            $parent->idCategoria = $objeto[3];
          }
        }

      }
      // get details of the uploaded file
      $fileTmpPath = $_FILES['nomeDoArquivo']['tmp_name'];
      $fileName = $_FILES['nomeDoArquivo']['name'];
      $fileSize = $_FILES['nomeDoArquivo']['size'];
      $fileType = $_FILES['nomeDoArquivo']['type'];
      $fileNameCmps = explode(".", $fileName);
      $fileExtension = strtolower(end($fileNameCmps));
      // sanitize file-name
      $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
      // check if file has one of the following extensions
      $allowedfileExtensions = array('jpg', 'png');
      if (in_array($fileExtension, $allowedfileExtensions)) {
        // directory in which the uploaded file will be moved
        $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
        $dest_path = $uploadFileDir . $newFileName;

        # se a extensão do arquivo estiver ok,
        # cria o objeto Imagem no banco antes de 
        # prosseguir com o upload
        
        $dsImagem = trim($_POST['dsImagem']);
        # input "file" não é enviado dentro de $_POST
        # $nomeDoArquivo = trim($_POST['nomeDoArquivo']);

        if (empty($dsImagem)){
          $ds_imagem_err = "Digite uma descrição para sua imagem.";
        }
        # if (empty($nomeDoArquivo)){
        #   $nome_do_arquivo_err = "Faça upload de uma imagem!";
        # }
        
        if(empty($ds_imagem_err) && empty($id_produto_err)) {
          $result = $imagem->insert(
            array(
              "dsImagem" => $dsImagem,
              "nomeDoArquivo" => $dest_path,
              "idProduto" => $idProduto
            )
          );
          if (empty($result['error'])) {

            $dsCategoria = "";
            $categoria_err = $database_err = "";

            # objeto criado no banco com sucesso, prosseguir
            # com o upload da imagem
            
            if(move_uploaded_file($fileTmpPath, $dest_path))
            {
              $_SESSION['imagem_create'] = true;

              # se tudo ocorreu bem, não há nenhum error handling
              # a ser feito, e eu posso direcionar com um get
              
              header("location: ");
              exit();
            } else {
              $_SESSION['imagem_create_error'] = true;
            }

            
          } else {
            # erro de inserção do objeto no banco
            $_SESSION['imagem_create_error'] = true;
          }
        }
        
      } else {

        # tipo de arquivo não aceito. mostrar ao cliente
        # quais são aceitos
        # $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions); 
        
        $_SESSION['imagem_filetype_error'] = true;
      }
    } else {

      # não foi enviado um arquivo ou ocorreu algum erro no upload
      # $message = 'There is some error in the file upload. Please check the following error.<br>';
      # $message .= 'Error:' . $_FILES['nomeDoArquivo']['error'];
      
      $_SESSION['imagem_create_error'] = true;
    }
    ## fazer uma validação melhor dessa caralha
  } else if ($_POST['action'] == 'delete') {
    $result = $imagem->deleteRecord($_POST['idImagem']);
    if (empty($result['error'])) {
      $_SESSION['imagem_delete'] = true;
    } else {
      $_SESSION['imagem_delete_error'] = true;
    }
    header("location: ");
    exit();
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
$imagem->flashMessage("imagem_create", "success", "Imagem cadastrada com sucesso!");
$imagem->flashMessage("imagem_delete","success", "Imagem deletada com sucesso!");
$imagem->flashMessage("imagem_create_error", "danger", "Erro ao cadastrar imagem!");
$imagem->flashMessage("imagem_delete_error","danger", "Erro ao deletar imagem.");
$imagem->flashMessage('imagem_filetype_error', "danger", "Tipo de arquivo não aceito!");
echo $imagem->createForm(
  array($ds_imagem_err, $nome_do_arquivo_err, $id_produto_err, $database_err),
  array($dsImagem, $nomeDoArquivo, $parent->idProduto),
  $database_err
);
echo "<h2 class='mt-4' >Imagens existentes</h2>";
$imagens = $imagem->getAll(NULL,NULL,array("column" => "idProduto", "value" => $parent->idProduto));
if($imagens['error']) {
  echo "<p>Ocorreu um erro ao carregar as imagens!</p>";
} else {

  if (count($imagens) == 0) {
    echo "<small>Nenhuma imagem cadastrada! Preencha o formulário acima para começar.</small>";
  } else {
    echo "<div class='row'>";
    foreach($imagens as $img) {
     echo
    "
      <div class='card mb-3' style='max-width:10rem;'>
        <div class='card-body'>
          <h5 class='card-title'>#".$img['idImagem']."</h5>
        </div>
        <img loading='lazy' src='".$imagem->pathToURL($img['nomeDoArquivo'])."'>
        <div class='card-body'>
          <p class='card-text'>".$img['dsImagem']."</p>
        </div>
        <div class='card-body'>
          <button type='button' class='btn btn-link' data-bs-toggle='modal' data-bs-target='#delete_".$img['idImagem']."'>
            Deletar
          </button>
          
          <div class='modal fade' id='delete_".$img['idImagem']."' tabindex='-1' role='dialog' aria-hidden='true' aria-labelledby='myModalLabel'>
            <div class='modal-dialog' role='document'>
              <div class='modal-content'>
                <div class='modal-header'>
                  <button type='button' class='close' data-bs-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                  <h4 class='modal-title' id='myModalLabel'>Deletar Imagem ".$img['idImagem']."</h4>
                </div>
                <div class='modal-body'>
                  <form method='post'>
                    <fieldset>
                      <legend>Esta operação não pode ser desfeita.</legend>
                      <input type='hidden' name='idImagem' value='".$img['idImagem']."'>
                      <input type='hidden' name='action' value='delete'>
                    </fieldset>
                    <div class='modal-footer'>
                      <button type='button' class='btn btn-default' data-bs-dismiss='modal'>Cancelar</button>
                      <button type='submit' class='btn btn-primary'>Deletar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          </div>
        </div>
    ";
    }
    echo "</div>";
  }

}
#echo $imagem->adminTableList($imagens);
echo "</div>";
