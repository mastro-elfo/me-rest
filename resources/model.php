<?php

class Model {
  public ?string $type = NULL;

  public function create(array $data)
  // Union types are available as of PHP 8.0.0.
  // https://www.php.net/manual/en/language.types.declarations.php#language.types.declarations.union
  // : integer|string|bool
  {
    $model = R::dispense($this->type);
    $this->_merge($model, $data);
    return R::store($model);
  }

  public function read(integer $id): object {
    return R::load($this->type, $id);
  }

  public function update(integer $id, array $data)
  // Union types are available as of PHP 8.0.0.
  // https://www.php.net/manual/en/language.types.declarations.php#language.types.declarations.union
  // : integer|string|bool
  {
    $model = R::loadForUpdate($this->type, $id);
    if($this->_exists($model)) {
      $this->_merge($model, $data);
      return R::store($model);
    }
    return FALSE;
  }

  public function delete(integer $id): bool {
    $model = R::load($this->type, $id);
    if($this->_exists($model)) {
      R::trash($model);
      return TRUE;
    }
    return FALSE;
  }

  protected function _merge(object & $model, array $data = []) {
    foreach ($data as $key => $value) {
      $model[$key] = $value;
    }
  }

  protected function _exists(object $model): bool {
    return $model
      && property_exists($model, "id")
      && is_integer($model->id)
      && $model->id !== 0;
  }
}

?>
