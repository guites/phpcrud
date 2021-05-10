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

  public function createForm($errors,$objeto, $database_err) 
  {
    $obj = array(
      "nmUsuario" => ["Nome de usuário","text",$errors[0],$oks[0], array("required" => "required")],
      "pwdUsuario" => ["Senha", "password",$errors[1],$oks[1], array("required" => "required")],
      "pwdConfirm" => ["Confirme sua senha", "password",$errors[2],$oks[2],array("required" => "required")],
      "emailUsuario" => ["Seu e-mail", "email",$errors[3],$oks[3],array("required" => "required")]
    );

    return parent::createForm($action,$obj,$database_err);
  }

  public function createLogin($errors, $obj, $database_err)
  {
    $obj = array(
      "emailUsuario" => ["Seu e-mail", "email",$errors[0],$oks[0],array("required" => "required")],
      "pwdUsuario" => ["Senha", "password",$errors[1],$oks[1], array("required" => "required")],
    );

    return parent::createForm($action, $obj, $database_err);
  }

  public function isLoggedIn() {
    return (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true);
  }

}
