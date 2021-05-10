<?php
if(!$usuario->isLoggedIn()) {
  $_SESSION["unauthorized"] = true;
  header("location: /");
  exit();
}
?>

<h1 class='mt-4'>Painel administrativo</h1>
<p>Navegue:</p>
<ul class="nav nav-pills">
  <li class="nav-item">
    <a class="nav-link" href="categoria">Categorias</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="produto">Produtos</a>
  </li>
</ul>


