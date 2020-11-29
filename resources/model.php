<?php

// Using Medoo namespace
use Medoo\Medoo;

class Model {
  public function __construct($table, $columns, $options = []) {
    $config = get_config("Medoo", [
      "database_type" => "sqlite",
      "database_file" => "database.db"
    ]);

    $this->db = new Medoo($config);
    $this->table = $table;
    $this->db->create($table, $columns, $options);
  }

  public function create($data) {
    $this->db->insert($this->table, $data);
    return $this->db->id();
  }

  public function read($id) {
    return $this->db->get($this->table, "*", ["id" => $id]);
  }

  public function update($id, $data) {
    $ret = $this->db->update($this->table, $data, ["id" => $id]);
    return $ret->rowCount();
  }

  public function delete($id) {
    $ret = $this->db->delete($this->table, ["id" => $id]);
    return $ret->rowCount();
  }

  public function select($where = []) {
    return $this->db->select($this->table, "*", $where);
  }
}

?>
