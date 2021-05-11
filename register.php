<?php
require __DIR__ . "/objects/objeto.php";
require  __DIR__ . "/objects/usuario.php";
$dbcon = new DBClass();
$connection = $dbcon->getConnection();
$usuario = new Usuario($connection);
if ($usuario->isLoggedIn()) {
  header("location: index.php");
  exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Define variables and initialize with empty values
  $username = $password = $confirm_password = "";
  $username_err = $password_err = $confirm_password_err = $database_err = $email_err = "";
  $username = trim($_POST['nmUsuario']);
  if(empty($username)) {
    $username_err = "Por favor, digite seu nome de usuário.";
  } else {
    $query = "SELECT idUsuario FROM Usuario WHERE nmUsuario = :nmUsuario";
    if($stmt = $connection->prepare($query)) {
      $stmt->bindParam(":nmUsuario", $username, PDO::PARAM_STR);
      if($stmt->execute()){
        if ($stmt->rowCount() > 0) {
          $username_err = "Nome de usuário já utilizado. Por favor, escolha outro.";
        }
      } else {
        $database_err = "Ocorreu um erro ao consultar seu nome de usuário. Por favor entrar em contato com o suporte técnico.";
      }
      unset($stmt);
    } else {
      $database_err = "Ocorreu um erro ao preparar seu nome de usuário. Por favor entrar em contato com o suporte técnico.";
    }
  }

  $email = trim($_POST['emailUsuario']);
  if(empty($email)) {
    $email_err = "Por favor, digite um endereço de e-mail.";
  } else {
    $mail_rgx = '/[\w\.-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-]+[\.[a-zA-Z0-9-]*/u';
    preg_match($mail_rgx, $email, $output);
    if(count($output) <= 0) {
      $email_err = 'E-mail inválido! Verifique o e-mail digitado e tente novamente.';
    } else if ($output[0] !== $email) {
      $email_err = 'E-mail contém caracteres inválidos! Verifique o e-mail digitado e tente novamente.';
    } else {
      $query = "SELECT idUsuario FROM Usuario WHERE emailUsuario = :emailUsuario";
      if ($stmt = $connection->prepare($query)) {
        $stmt->bindParam(":emailUsuario", $email, PDO::PARAM_STR);
        if ($stmt->execute()) {
          if ($stmt->rowCount() > 0) {
            # ou recupere sua senha clicando aqui...
            $email_err = "E-mail já cadastrado! Por favor, escolha outro.";
          }
        } else {
          $database_err = "Ocorreu um erro ao consultar seu e-mail. Por favor entre em contato com o suporte técnico.";
        }
      } else {
        $database_err = "Ocorreu um erro ao preparar seu e-mail. Por favor entre em contato com o suporte técnico.";
      } 
    }
  }

  $password = trim($_POST['pwdUsuario']);
  if(empty($password)) {
    $password_err = "Por favor, digite escolha uma senha.";
    $confirm_password_err = "Por favor, digite a confirmação de sua senha.";
  } else if (strlen($password) < 8) {
    $password_err = "Sua senha deve ter ao menos 8 caracteres!";
    $confirm_password_err = "Por favor, digite a confirmação de sua senha.";
  }

  $confirm_password = trim($_POST['pwdConfirm']);
  if (empty($confirm_password)) {
    $confirm_password_err = "Por favor, digite a confirmação de sua senha.";
  } else {
    if (empty($password_err) && ($password != $confirm_password)) {
      $confirm_password_err = "A senha e a confirmação não batem. Por favor, verifique que os dois campos foram preenchidos iguais.";
    }
  }

  if(empty($password_err) && empty($username_err) && empty($confirm_password_err) && empty($database_err) && empty($email_err)) {
    $query = "INSERT INTO Usuario (nmUsuario, pwdUsuario, emailUsuario) VALUES (:nmUsuario, :pwdUsuario, :emailUsuario)";
    if ($stmt = $connection->prepare($query)){
      $stmt->bindParam(":nmUsuario", $username, PDO::PARAM_STR);
      $stmt->bindParam(":pwdUsuario", $hashed_password, PDO::PARAM_STR);
      $stmt->bindParam(":emailUsuario", $email, PDO::PARAM_STR);
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      if ($stmt->execute()) {
        $_SESSION['cadastro'] = true;
        header("location: login.php");
      } else {
        $database_err = "Ocorreu um erro ao criar sua conta. Por favor entre em contato com o suporte técnico.";
      }
      unset($stmt);
    } else {
      $database_err = "Ocorreu um erro ao preparar sua conta. Por favor entrar em contato com o suporte técnico.";
    }
  }
  # não vou dar unset pois preciso da instacia pdo para rodar as funções de usuário abaixo
  #unset($connection);
}

require "header.php";
echo "<h1>Cadastro</h1>";
echo $usuario->createForm(
  array($username_err,$password_err,$confirm_password_err,$email_err),
  array($username, $password, $confirm_password, $email),
  $database_err
);
require "footer.php"; 
?>
