<?php
require_once __DIR__ . "/../dotenv.php";
(new DotEnv(__DIR__ . '/../.env'))->load();
class DBClass{

  /**
   *
   * adaptado de https://www.techiediaries.com/php-rest-api/
   *
   */

  private $host;
  private $username;
  private $password;
  private $database;

  public function __construct(){
    $this->host = getenv('DB_HOST');
    $this->username = getenv('DB_USER');
    $this->password = getenv('DB_PW');
    $this->database = getenv('DB_NAME');
  }


  public $connection;

  // get the database connection
  public function getConnection(){
      $this->connection = null;
      try{
          $this->connection = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database, $this->username, $this->password);
          $this->connection->exec("set names utf8");
      }catch(PDOException $exception){
          echo "Error: " . $exception->getMessage();
      }
      return $this->connection;
  }

}
