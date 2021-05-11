<?php
require __DIR__ . "/../db/connection.php";

class Objeto {
  
  private $connection;

  function __construct($connection){
    $this->connection = $connection;
    return $this;
  }

  public function getAll($order = NULL, $sort_order = NULL, $clause = NULL) {
    /**
     *
     * $clause = array(
     *  'column' => 'column name',
     *  'value' => 'value'
     * )
     *
     */

    if (!$order) $order = "id" . get_class($this);
    if (!$sort_order) $sort_order = "DESC";
  
    $results = [];
    $query = "SELECT * FROM " . $this->table_name;
    if ($clause) {
     $query .= " WHERE " . $clause['column'] . " = " . $clause['value'];
    }
    $query .= " ORDER BY " . $order . " " . $sort_order; 
    if($stmt = $this->connection->prepare($query)) {
      if($stmt->execute()) {
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
          $results[] = $row;
        }
      } else {
        $results['error'] = true;
        $results['message'] = json_encode($stmt->errorInfo());
      }
    } else {
      $results['error'] = true;
      $results['message'] = json_encode($stmt->errorInfo());
    }
    return $results;

  }

  public function Insert($args){
    $este_objeto = get_class($this);
    $columns = array_keys($args);
    $query = "INSERT INTO " . $this->table_name . " (" . implode(",",$columns) . ") VALUES (:" . implode(",:",$columns) . ")";
    if ($stmt = $this->connection->prepare($query)){
      foreach($args as $binding => $value) {
        $stmt->bindParam(":$binding", $args[$binding], PDO::PARAM_STR);
        #$stmt->bindParam(":$binding", $value, PDO::PARAM_STR);
      }
      if ($stmt->execute()) {
        return array(
          "error" => false,
          "message" => ""
        );
      } else {
        #print_r($stmt->errorInfo());
        return array(
          "error" => true,
          "message" => "Erro ao inserir $este_objeto."
        );
       }
    } else {
      return array(
        "error" => true,
        "message" => "Erro ao preparar $este_objeto."
      );
    }
  }

  public function update($args){
    $idColumn = "id".$this->table_name;
    $query = "UPDATE " . $this->table_name . " SET ";
    foreach($args['campos'] as $campo => $valor) {
      $query .= "$campo=:$campo, ";
    }
    $query = substr($query, 0, -2);
    $query.= " WHERE $idColumn=:id";
    if ($stmt = $this->connection->prepare($query)){
      foreach($args['campos'] as $binding => $value) {
        $stmt->bindParam(":$binding", $args['campos'][$binding], PDO::PARAM_STR);
        $objeto[] = $value;
      }
      $stmt->bindParam(":id",$args['id'],PDO::PARAM_STR);
      array_unshift($objeto, $args['id']);
      if ($stmt->execute()) {
        return array(
          "error" => false,
          "objeto" => $objeto
        );
      } else {
        return array(
          "error" => true,
          "message" => "Erro ao inserir $this->table_name."
          #"message" => json_encode($stmt->errorInfo())
        );
      }
    } else {
      return array(
        "error" => true,
        "message" => "Erro ao preparar $this->table_name."
      );
    }
  }
  public function deleteRecord($id) {
    $query = "DELETE FROM $this->table_name WHERE id$this->table_name = :id";
    if ($stmt = $this->connection->prepare($query)) {
      $stmt->bindParam(":id", $id, PDO::PARAM_STR);
      if ($stmt->execute()) {
        return array(
          "error" => false,
          "message" => ""
        );
      } else {
        return array(
          "error" => true,
          "message" => "Erro ao deletar."
        );
      }
    } else {
      return array(
        "error" => true,
        "message" => "Erro ao preparar $este_objeto."
      );
    } 
  }

  public function checkId($id) {
    $query = "SELECT * FROM $this->table_name WHERE id$this->table_name = :id";
    if ($stmt = $this->connection->prepare($query)){
        $stmt->bindParam(":id", $id, PDO::PARAM_STR);
        if($stmt->execute()) {
          if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_NUM);
            return array(
              "error" => "",
              "objeto" => $row
            );
          } else {
            return array(
              "error" => true,
              "message" => "Erro ao executar consulta."
            );
          }
        } else {
          return array(
            "error" => true,
            "message" => "Erro ao executar consulta."
          );
        }
    } else {
      return array(
        "error" => true,
        "message" => "Erro ao preparar $este_objeto."
      );
    }
  }

  public function createForm($action,$obj,$database_err) {
    $idColumn = "id".$this->table_name;
    if (!$database_err) {
      $flash_error = '';
    } else {
      $flash_error = "
        <div class='col-lg-4'>
            <div class='bs-component'>
              <div class='alert alert-dismissible alert-danger'>
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                <strong>Oh não!</strong>Um erro ocorreu!<a href='#' class='alert-link'>Por favor, nos avise</a> para que possamos corrigir o quanto antes.
              </div>
            <button class='source-button btn btn-primary btn-xs' role='button' tabindex='0'>&lt; &gt;</button></div>
        </div>
      ";
    }
    $formHtml = "
      <form method='post' action=''>
        <fieldset>
          <legend>$this->table_name</legend>
    ";
    foreach($obj as $campo => $valores) {
      # cria os pares de atributo html (required, readonly, title, etc)
      $html_attrs = "";
      foreach ($valores[4] as $html_attribute => $html_attr_value) {
        $html_attrs .= "$html_attribute='$html_attr_value' ";
      }
      # verifica se o campo foi preenchido com erro
      # $valores[3] é o valor preenchido no input, $valores[4] indica se o campo foi preenchido com erro 
      # $valores[2] é o erro a ser mostrado
      if (!empty($valores[3])) {
        # input foi preenchido de forma errada, mostrar erro em $valores[2]
        $validation_class = "is-invalid";
        $validation_message = "
         <div class='invalid-feedback'>
           $valores[3]
         </div>
        ";
      } else {
        # se o campo não foi preenchido com erro, mostro sucesso caso ele não estiver vazio.
        # preciso pensar em algo para quando o campo está sendo mostrado pela primeira vez e vem com valores prévios preenchidos
        # por ex. quando a pessoa entra na página de update.
        if(!empty($valores[2])) {
          $validation_class = "is-valid";
          $validation_message = "
           <div class='valid-feedback'>
              Ok.
           </div>
          ";
        }
      }

      $formHtml .= "
      <div class='form-group'>
        <label for='$campo' class='col-sm-10 col-form-label'>
          $valores[0]
        </label>
        <div class='col-sm-10'>
          <input type='$valores[1]' value='$valores[2]' name='$campo' id='$campo' class='form-control $validation_class' $html_attrs >
          $validation_message
        </div>
      </div>
      ";
    }
    $formHtml .= "
        <input type='hidden' name='action' value='$action'>
      </fieldset>
      <div class='bs-component mb-3 mt-3'>
        <button type='submit' class='btn btn-primary'>Enviar</button>
      </div>
    </form>
    ";
    return $formHtml;
  }

  public function flashMessage($session_var, $level, $message) {
    /**
     *
     *$level warning, danger, success, info, primary, secondary, light
     *
     */
    
    if(isset($_SESSION[$session_var]) && $_SESSION[$session_var] === true) {
      $msg = "
        <div class='col-lg-4'>
          <div class='bs-component'>
            <div class='alert alert-dismissible alert-$level'>
              <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              $message
            </div>
          </div>
        </div>
      ";
      echo $msg;
      unset($_SESSION[$session_var]);
    }

  }


  public function adminTableList($rows, $media = NULL) {
    # precisa pegar a informação de que tabela que se trata, para acessar a coluna id.
    $idColumn = "id". get_class($this);
    # início cabeçalho tabela
    $head_rows = array_keys($rows[0]);
    $tableHtml = "
      <table class='table table-hover'>
        <thead>
          <tr>
      ";
    foreach($head_rows as $hr) {
      $tableHtml.= "
        <th scope='col'>$hr</ht>
      ";
    }
    if ($media) $tableHtml .= "<th scope='col'>Mídias</th>";
    $tableHtml .= "
          <th scope='col'>Editar</th>
          <th scope='col'>Deletar</th>
        </tr>
      </thead>
    ";
    # fim cabeçalho tabela
    # início corpo tabela
    $tableHtml .= "
      <tbody>
    ";
    foreach($rows as $row) {
      $tableHtml .= "
        <tr>
      ";
      foreach($row as $r) {
        $tableHtml .= "
          <td>$r</td>
        ";
      }
      # botão para ver midias, caso $media = true
      if ($media) {
        $tableHtml .="
          <td>
            <button type='button' class='btn btn-link'>
              <a href='/admin/imagem?object=$this->table_name&id=$row[$idColumn]'>
                Mídias
              </a>
            </button>
          </td>
        ";
      }
      # botão para editar categoria
      $tableHtml .="
        <td>
          <button type='button' class='btn btn-link'>
            <a href='update.php?id=$row[$idColumn]'>
              Editar
            </a>
          </button>
        </td>
      ";
      # botão e modal para deletar categoria
      $tableHtml .="
        <td>
          <button type='button' class='btn btn-link' data-bs-toggle='modal' data-bs-target='#delete_$row[$idColumn]'>
            Deletar
          </button>
        <div class='modal fade' id='delete_$row[$idColumn]' tabindex='-1' role='dialog' aria-hidden='true' aria-labelledby='myModalLabel'>
          <div class='modal-dialog' role='document'>
            <div class='modal-content'>
              <div class='modal-header'>
                <button type='button' class='close' data-bs-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                <h4 class='modal-title' id='myModalLabel'>Deletar Categoria $row[$idColumn]</h4>
              </div>
              <div class='modal-body'>
                <form method='post'>
                  <fieldset>
                    <legend>Esta operação não pode ser desfeita.</legend>
                    <input type='hidden' name='$idColumn' value='$row[$idColumn]'>
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
        </td>
      ";
      $tableHtml .= "
        </tr>
      ";
    }
    $tableHtml .= "
        </tbody>
      </table>
    ";
    # fim do corpo da tabela
    return $tableHtml;
  }

}
