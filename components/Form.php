<?php

class Form{
  public static function input($type, $model, $name, $attrs = array()){
    $input = '<input type="'.$type.'" name="'.$name.'" value="'.$model->{$name}.'" '.self::options($attrs).' class="form-control" />';

    return self::block($model, $name, $input, $attrs);
  }

  public static function dropDownList($model, $name, $options, $attrs = array()){
    $optionsHtml = [];
    $i = 0;
    foreach($options as $key=>$val){
      $selected = '';
      if( strval($model->{$name}) == strval($val) ){
        $selected = ' selected';
      }
      $optionValue = $key;
      if( is_numeric($key) && $i == $key ) $optionValue = $val;
      $optionsHtml[] = '<option value="'.$val.'"'.$selected.'>'.$optionValue.'</option>';
      $i ++;
    }
    $input = '<select name="'.$name.'" '.self::options($attrs).' class="form-control">
    <option value="">'.App::t('Выбрать').'</option>
    '.implode('', $optionsHtml).'
    </select>';

    return self::block($model, $name, $input, $attrs);
  }

  public static function textInput($model, $name, $attrs = array()){
    return self::input('text', $model, $name, $attrs);
  }

  public static function fileInput($model, $name, $attrs = array()){
    return self::input('file', $model, $name, $attrs);
  }

  public static function textArea($model, $name, $attrs = array()){
    $input = '<textarea name="'.$name.'" '.self::options($attrs).' class="form-control">'.$model->{$name}.'</textarea>';

    return self::block($model, $name, $input, $attrs);
  }

  private static function options($attrs){
    $attrsHtml = [];
    foreach($attrs as $key=>$val){
      $attrsHtml[] = ''.$key.'="'.$val.'"';
    }
    return implode(' ', $attrsHtml);
  }

  private static function block($model, $name, $element, $attrs){
    $error = $model->getError($name);

    $label = $model->getLabel($name);
    if( isset($attrs['label']) ) $label = $attrs['label'];

    return '
    <div class="form-group '.((!empty($error))? 'has-error':'').' '.$name.'-block">
        <label>'.$label.'</label>
        '.$element.'
        <p class="help-block">'.$error.'</p>
    </div>';
  }
}
