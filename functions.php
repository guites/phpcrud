<?php
require __DIR__ . "/objects/objeto.php";
require __DIR__ . "/objects/produto.php";
require __DIR__ . "/objects/categoria.php";
require __DIR__ . "/objects/imagem.php";
require __DIR__ . "/objects/usuario.php";
$dbcon = new DBClass();
$connection = $dbcon->getConnection();
$usuario = new Usuario($connection);
$produto = new Produto($connection);
$categorias = (new Categoria($connection))->getAll();
function showProductsHome($categorias, $produto) {
  foreach($categorias as $cat) {
    $cat_desc = $cat['dsCategoria'];
    echo "<div class='row'>";
    echo "<h2>$cat_desc</h2>";
    $cat_produtos = $produto->getFromCategory($cat['idCategoria']);
    foreach($cat_produtos as $cp) {
      $produto->produtoHomeHtml($cp);
    }
    echo "</div>";
  }
}

