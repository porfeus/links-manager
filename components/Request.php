<?php

class Request{
  public static function post($name, $defaultValue = ''){
    if( isset($_POST[$name]) ) return $_POST[$name];
    return $defaultValue;
  }

  public static function issetPost(){
    return !empty($_POST);
  }

  public static function get($name, $defaultValue = ''){
    if( isset($_GET[$name]) ) return $_GET[$name];
    return $defaultValue;
  }

  public static function issetGet(){
    return !empty($_GET);
  }

  public static function session($name, $value = 'no-value'){
    $name = PAGE_TYPE.'_'.$name;

    if( $value == 'no-value' ){ // if get
      if( isset($_SESSION[$name]) ) return $_SESSION[$name];
      return false;
    }else{ // if set
      if( $value == '' ){
        unset($_SESSION[$name]);
      }else{
        $_SESSION[$name] = $value;
      }
    }
  }

  public static function basedir(){
    $basedir = dirname($_SERVER['PHP_SELF']);
    if( $basedir != "/" ) $basedir.= "/";
    return $basedir;
  }

  public static function path(){
    $basedir = self::basedir();

    $page = $_SERVER['REQUEST_URI'];
    if( $basedir != "/" ){
      $page = preg_replace('@'.$basedir.'@', '', $page, 1);
    }

    $page = trim($page, '/');

    $url = parse_url($page);

    $path = $url['path'];

    if( isset(App::get()->config['urlManager']) ){
      $urlManager = App::get()->config['urlManager'];
      if( isset($urlManager[$path]) ){
        $path = $urlManager[$path];
      }
    }

    return $path;
  }

  public static function site(){
    return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/';
  }
}
