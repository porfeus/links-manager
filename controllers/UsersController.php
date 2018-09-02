<?php
class UsersController extends BaseController{

  public function accessRules(){
    return [
        [
            'allow' => true,
            'roles' => ['admin'],
        ],
    ];
  }

  public function actionIndex(){

    $user = new Users();
    $total = $user->select('count(1) as count')
      ->find()
      ->count;
    $activated = $user->select('count(1) as count')
      ->greaterthan('activated_time', 0)
      ->find()
      ->count;

    if( Request::get('action') == 'ajax' ){
      header('Content-type: application/json');

      $start = Request::post('start');
      $search = Request::post('search');
      $columns = Request::post('columns');

      $length = Request::post('length');
      if( $length > 1000 ) $length = 1000;

      $activatedType = 'yes';
      if( !empty($columns) && !empty($columns[8]) && !empty($columns[8]['search']['value']) ){
        $activatedType = $columns[8]['search']['value'];
      }

      //filter
      $whereAdd = ['1'];
      if( !empty($search['value']) ){
        array_push($whereAdd, ' AND (
          login LIKE "%'.addslashes($search['value']).'%" OR
          email LIKE "%'.addslashes($search['value']).'%"
        )');
      }

      if( $activatedType != 'all' ){
        if( $activatedType == 'no' ){
          array_push($whereAdd, ' AND activated_time = 0');
        }else{
          array_push($whereAdd, ' AND activated_time > 0');
        }
      }

      $user->where(implode('', $whereAdd));
      $user->limit($start, $length);
      $items = $user->findAll();
      //end filter

      //count filter
      $user->select('count(1) as count');
      $user->where(implode('', $whereAdd));
      $filtered = $user->find();
      $filtered = $filtered->count;
      //end count filter

      $data = [];
      foreach($items as $item){
        array_push($data, [
          'id' => $item->id,
          'login' => $item->login,
          'password' => $item->password,
          'email' => $item->ifnull('email', App::t('Нет')),
          'activated_time' => $item->activatedDate(),
          'activated_add_time' => $item->activatedEndDate(),
          'users_limit' => $item->users_limit,
          'email_send_time' => $item->email_send_time,
          'ip_old' => $item->ifnull('ip_old', App::t('Нет')),
          'ip_new' => $item->ifnull('ip_new', App::t('Нет')),
          'last_enter_time' => $item->enterDate(),
          'online' => $item->onLine(),
        ]);
      }

      $json_data = array(
          "draw"            => intval( $_REQUEST['draw'] ),
          "recordsTotal"    => intval( $total ),
          "recordsFiltered" => intval( $filtered ),
          "data"            => $data
      );
      echo json_encode($json_data);
      exit;
    }

    return $this->render('users/index', array(
      'total' => $total,
      'activated' => $activated,
      'model' => $user,
    ));
  }

  public function actionCreate(){
    $model = new UsersCreateForm();

    if( Request::issetPost() ){
      $model->load($_POST);

      if( $model->validate() ){

        $user = new Users();
        $user->load($_POST);
        $user->activated_add_time = floatval($model->activated_days) * 86400;
        $user->insert();

        //add info
        $post_message = trim(Request::post('message_'.$model->language));

        $message = $post_message;
        $message = str_replace('{login}', $user->login, $message);
        $message = str_replace('{password}', $user->password, $message);
        $message = str_replace('{days}', $model->activated_days, $message);
        $result = $message;

        $message = $post_message;
        $message = str_replace('{login}', $user->login.' / ', $message);
        $message = str_replace('{password}', $user->password.' / ', $message);
        $message = str_replace('{days}', $model->activated_days, $message);
        $csvResult = $message;

        //csv
        $csvResult = str_replace("
", "", $csvResult);

        $csvResult = '"'.$csvResult.'"';
        $csvResult = iconv('utf-8', 'cp1251', $csvResult);
        file_put_contents(__DIR__.'/../files/generate.csv', $csvResult);
        //end csv
      }
    }

    $languagesData = $this->app->language->data();
    $languages = [];
    $messages = [];
    foreach($languagesData as $id=>$data){
      $languages[$data['language_title']] = $id;

      $messages['message_'.$id] = $data['generate_message'];
    }

    return $this->render('users/create', [
      'model' => $model,
      'result' => $result,
      'languages' => $languages,
      'messages' => $messages,
    ]);
  }

  public function actionEdit(){
    $user = new Users();
    $user->eq('id', Request::get('id'))->find();

    if( empty($user->data) ){
      return $this->redirect('users/index');
    }

    $model = new UsersEditForm();
    $model->load($user->data);

    if( Request::issetPost() ){
      $model->load($_POST);

      if( $model->validate() ){
        $user->load($_POST);
        if(
          $user->activated_time > 0 &&
          $user->activated_time + $user->activated_add_time < time()
        ){
          $user->activated_add_time += time() - ($user->activated_time + $user->activated_add_time);
        }
        $user->activated_add_time += floatval($model->activated_days) * 86400;
        $user->update();

        return $this->redirect('users/index');
      }
    }

    return $this->render('users/edit', [
      'model' => $model,
    ]);
  }

  public function actionDelete(){
    $deleteList = Request::post('delete');
    if( !empty($deleteList) ){
      $user = new Users();
      $items = $user->in('id', array_values($deleteList))->findAll();
      foreach($items as $item){
        $item->delete();
      }
    }
  }

  public function actionGenerate(){
    $model = new UsersGenerateForm();
    $result = false;

    if( Request::issetPost() ){
      $model->load($_POST);

      if( $model->validate() ){
        $result = [];
        $csvResult = [];

        for($i=0; $i<$model->accounts_num; $i++){
          $user = new Users();
          $user->login = $this->generateLogin();
          $user->password = $this->generatePassword();
          $user->activated_add_time = floatval($model->activated_days) * 86400;
          $user->users_limit = $model->users_limit;

          if( $user->validate() ){
            $user->insert();
          }else{
            continue;
          }

          $post_message = trim(Request::post('message_'.$model->language));

          $message = $post_message;
          $message = str_replace('{login}', $user->login, $message);
          $message = str_replace('{password}', $user->password, $message);
          $message = str_replace('{days}', $model->activated_days, $message);
          $result[] = $message;

          $message = $post_message;
          $message = str_replace('{login}', $user->login.' / ', $message);
          $message = str_replace('{password}', $user->password.' / ', $message);
          $message = str_replace('{days}', $model->activated_days, $message);
          $csvResult[] = $message;
        }

        $result = implode(PHP_EOL.'================'.PHP_EOL, $result);

        //csv
        $csvResult = array_map(function($item){
          return str_replace("
", "", $item);
        }, $csvResult);
        $csvResult = '"'.implode('"'.PHP_EOL.'"', $csvResult).'"';
        $csvResult = iconv('utf-8', 'cp1251', $csvResult);
        file_put_contents(__DIR__.'/../files/generate.csv', $csvResult);
        //end csv
      }
    }

    $languagesData = $this->app->language->data();
    $languages = [];
    $messages = [];
    foreach($languagesData as $id=>$data){
      $languages[$data['language_title']] = $id;

      $messages['message_'.$id] = $data['generate_message'];
    }

    return $this->render('users/generate', [
      'model' => $model,
      'result' => $result,
      'languages' => $languages,
      'messages' => $messages,
    ]);
  }

  public static function generateLogin(){
    $letters = "ABCDEFGHIGKLMNOPQRSTUVWXYZ";
    $addNumRand = rand(0,9);
    $login = "";
    for($i=0; $i<10; $i++){
      if( $i == 5 ) $login .= "-";
      if( $addNumRand == $i ){
        $login.= $addNumRand;
      }else{
        $login.= $letters[rand(0, strlen($letters))];
      }
    }
    return $login;
  }

  public static function generatePassword(){
    $letters = implode("", [
      "ABCDEFGHIGKLMNOPQRSTUVWXYZ",
      "abcdefghigklmnopqrstuvwxyz",
      "1234567890",
    ]);
    $password = "";
    for($i=0; $i<12; $i++){
      $password.= $letters[rand(0, strlen($letters))];
    }
    return $password;
  }
}
