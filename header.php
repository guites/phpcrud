<!doctype html>
<html lang="pt-BR">
  <head>
    <title>The CRUD Store</title>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta encoding="UTF-8">
    <link rel="stylesheet" type="text/css" href="/assets/bootstrap.min.css">
  </head>
  <body>
    <div class="container">
      <header>
        <p>Bem-vindo à loja. Compre nossas coisas!</p>
        <div class="bs-component">
          <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
              <a href="/" class="navbar-brand">CRUD STORE</a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarColor01" aria-controls="navbarColor01"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="navbar-collapse collapse" id="navbarColor01" style="">
                <ul class="navbar-nav me-auto">
                <?php if(!$usuario->isLoggedIn()){ ?>
                  <li class="nav-item">
                    <a class="nav-link" href="login.php">Entrar</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="register.php">Crie uma conta</a>
                  </li>
                <?php } else { ?>
                  <li class="nav-item">
                    <a class="nav-link" href="/admin">Painel</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="/logout.php">Sair</a>
                  </li>
                <?php } ?>
                </ul>
            </div>
          </nav>
        </div>
      <hr/>
      </header>
