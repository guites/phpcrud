<?php
require "functions.php";
if ($usuario->isLoggedIn()) {
  header("location: index.php");
  exit;
}

$dbcon = new DBClass();
$connection = $dbcon->getConnection();

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = $login_err = $database_err = "";
if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = trim($_POST['emailUsuario']);
  if(empty($email)) {
    $email_err = "Por favor, digite seu e-mail.";
  }
  $password = trim($_POST['pwdUsuario']);
  if (empty($password)) {
    $password_err = "Por favor, digite sua senha.";
  }
  if (empty($email_err) && empty($password_err)){
    $query = "SELECT idUsuario, nmUsuario, emailUsuario, pwdUsuario FROM Usuario WHERE emailUsuario = :emailUsuario";
    if($stmt = $connection->prepare($query)) {
      $stmt->bindParam(":emailUsuario", $email, PDO::PARAM_STR);
      if($stmt->execute()){
        if ($stmt->rowCount() == 1) {
          if ($row = $stmt->fetch()) {
            $id = $row['idUsuario'];
            $username = $row['nmUsuario'];
            $email = $row['emailUsuario'];
            $hashed_password = $row['pwdUsuario'];
            if (password_verify($password, $hashed_password)) {
              session_start();
              $_SESSION["loggedin"] = true;
              $_SESSION["id"] = $id;
              $_SESSION["username"] = $username;
              $_SESION["email"] = $email;

              $_SESSION["logado"] = true;

              header("location: index.php");
            } else {
              $login_err = $email_err = $password_err = "E-mail ou senha inválidos.";
            }
          } else {
            $login_err = $email_err = $password_err = "E-mail ou senha inválidos.";
          }
        } else {
          $login_err = $email_err = $password_err = "E-mail ou senha inválidos.";
        }
      } else {
        $database_err = "Ocorreu um erro ao consultar seu e-mail. Por favor entre em contato com o suporte técnico.";
      }
    } else {
      $database_err = "Erro ao preparar seu login. Por favor entre em contato com o suporte técnico.";
    }
  } else {
    $login_err = $email_err = $password_err = "Preencha o E-mail e a senha.";
  }
}

require "header.php";
echo "<h1>Login</h1>";
$usuario->flashMessage("logout","info","Você foi deslogado de sua conta.");
$usuario->flashMessage("cadastro", "success", "Cadastro realizado com sucesso! Preencha o formulário abaixo para entrar na sua conta.");
# $usuario->cadastradoComSucessoMessage();
# $usuario->deslogadoComSucessoMessage();
echo $usuario->createLogin(
  array($email_err,$password_err),
  array($email,""),
  $database_err
);
require "footer.php";
