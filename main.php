<main>
  <div class="row">
    <h1>THE CRUD STORE</h1>
    <p>Sem ads, sem inchaço. Apenas produtos.</p>
    <?php $usuario->flashMessage("logado", "success", "Você foi logado com sucesso. Aproveite nossas ofertas!"); ?>
    <?php $usuario->flashMessage("unauthorized", "danger", "Você precisa estar logado para acessar esta página. Crie uma conta!"); ?>
  </div>

  <?php
  showProductsHome($categorias, $produto);
  ?>
</main>
