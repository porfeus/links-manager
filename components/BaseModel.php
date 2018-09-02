<?php

class BaseModel extends ActiveRecord{
  public $errors = [];

  public function addError($name, $error){
    if( isset($this->errors[$name]) ) return;
    $this->errors[$name] = $error;
  }

  public function blank($attribute){
    $var = $this->{$attribute};
    return empty($var);
  }

  public function ifnull($attribute, $defaultValue){
    $var = $this->{$attribute};
    if( empty($var) ) return $defaultValue;
    return $var;
  }

  public function getError($name){
    if( isset($this->errors[$name]) ) return $this->errors[$name];
    return '';
  }

  public function getLabel($name){
    $labels = $this->attributeLabels();
    if( isset($labels[$name]) ) return $labels[$name];
    return $name;
  }

  public function attributeLabels(){
    return [];
  }

  public function load($assoc){

    $modelAttributes = $this->attributeLabels();
    foreach($assoc as $key=>$val){
      if( !isset($modelAttributes[$key]) ) continue;
      $this->{$key} = $val;
    }
  }

  public function validate(){
    $rules = $this->rules();
    $totalResult = true;

    foreach($rules as $rule){
      list($attributes, $validator) = $rule;
      foreach($attributes as $attribute){
        $result = $this->{$validator}($attribute);
        if( !$result ) $totalResult = false;
      }
    }

    return $totalResult;
  }

  public function number($attribute){
    $value = trim($this->{$attribute});
    if( $value == '' || is_numeric($value) ) return true;
    $this->addError($attribute, App::t('Значение должно быть числом'));
    return false;
  }

  public function required($attribute){
    $value = $this->{$attribute};
    if( trim($value) != '' ) return true;
    $this->addError($attribute, App::t('Значение не может быть пустым'));
    return false;
  }

  public function unique($attribute){
    $value = $this->{$attribute};
    $id = intval($this->id);

    $model = new static();
    $model->select('count(1) as count')
      ->eq($attribute, $value)
      ->ne('id', $id)
      ->find();

    if( !$model->count ) return true;
    $this->addError($attribute, App::t('Значение должно быть уникальным'));
    return false;
  }
}
