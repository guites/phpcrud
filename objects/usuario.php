<?php

class Usuario extends Objeto {

  function __construct($connection){
    parent::__construct($connection);
    $this->connection = $connection;
    $this->table_name = "Usuario";
    if(session_id() == ''){
      session_start();
    }
  }

  private $idUsuario;
  private $nmUsuario;
  private $pwdUsuario;
  private $emailUsuario;
  private $created_at;

  public function createForm($errors,$objeto, $database_err, $enctype = NULL) 
  {
    $obj = array(
      "nmUsuario" => ["Nome de usuário","text",$objeto[0],$errors[0], array("required" => "required")],
      "pwdUsuario" => ["Senha", "password",$objeto[1], $errors[1], array("required" => "required")],
      "pwdConfirm" => ["Confirme sua senha", "password",$objeto[2],$errors[2],array("required" => "required")],
      "emailUsuario" => ["Seu e-mail", "email",$objeto[3], $errors[3],array("required" => "required")]
    );

    return parent::createForm($action,$obj,$database_err);
  }

  public function createLogin($errors, $objeto, $database_err)
  {
    $obj = array(
      "emailUsuario" => ["Seu e-mail", "email",$objeto[0],$errors[0],array("required" => "required")],
      "pwdUsuario" => ["Senha", "password",$objeto[1],$errors[1], array("required" => "required")],
    );

    return parent::createForm($action, $obj, $database_err);
  }

  public function isLoggedIn() {
    return (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true);
  }

}
