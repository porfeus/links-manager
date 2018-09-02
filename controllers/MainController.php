<?php
class MainController extends BaseController{

  public function accessRules(){
    return [
        [
            'allow' => true,
            'roles' => ['admin', 'user'],
        ],
        [
            'allow' => false,
            'actions' => ['download'],
            'roles' => ['user'],
        ],
        [
            'allow' => true,
            'actions' => ['login', 'cron', 'install'],
            'roles' => ['guest'],
        ],
    ];
  }

  public function actionLogin(){

    $error_message = '';
    $login_error = false;
    $captcha_error = false;
    $need_email = false;
    $email_error = false;

    if( Request::issetPost() ){
      if( !Request::session('captcha_off') && (!Request::post('captcha') || Request::post('captcha') != $_SESSION['captcha']['code']) ){
        $error_message = App::t('Неправильно введен проверочный код');
        $captcha_error = true;
      }else
      if( $this->app->user->login(Request::post('login'), Request::post('password')) ){
        Request::session('captcha_off', 1);

        $identity = $this->app->user->identity;

        if( $this->app->user->role == 'user' ){

          // Проверка лимита пользователей
          if( $identity->usersOnlineLimited() ){
            $error_message = App::t('Достиг лимит пользователей онлайн на Вашем аккаунте');
            $this->app->user->logout();
          }

          // Проверка отсутствия активации
          if( empty($error_message) && $identity->blank('email') ){

            if( Request::post('email') ){
              if( filter_var(Request::post('email'), FILTER_VALIDATE_EMAIL) ){
                $identity->email = Request::post('email');
                $identity->activated_time = time();
                $identity->update();
              }else{
                $need_email = true;
                $email_error = true;
                $error_message = App::t('Е-mail указан неправильно');
                $this->app->user->logout();
              }
            }else{
              $need_email = true;
              $email_error = true;
              $error_message = App::t('Укажите Ваш e-mail');
              $this->app->user->logout();
            }
          }

          // Проверка активационного периода
          if( empty($error_message) && !$identity->blank('activated_time') &&
              $identity->activated_time + $identity->activated_add_time <= time()
          ){
            $error_message = App::t('Действие аккаунта приостановлено по истечении времени. Обратитесь в службу поддержки');
            $this->app->user->logout();
          }

          // Сохранение входных данных
          if( empty($error_message) ){
            $identity->ip_old = $identity->ip_new;
            $identity->ip_new = $_SERVER['REMOTE_ADDR'];
            $identity->last_enter_time = time();
            $identity->last_update_time = time();
            $identity->language = $this->app->language->getActiveId();
            $identity->usersOnlineSet();
            $identity->update();
          }
        }

        // Редирект на защищенную страницу
        if( empty($error_message) ){
          return $this->redirect('main/index');
        }

      }else{
        $error_message = App::t('Неправильный логин или пароль');
        $login_error = true;
      }
    }

    include(__DIR__."/../captcha/simple-php-captcha.php");
    $_SESSION['captcha'] = simple_php_captcha();

    return $this->renderPartial('login-'.PAGE_TYPE, array(
      'error_message' => $error_message,
      'login_error' => $login_error,
      'captcha_error' => $captcha_error,
      'need_email' => $need_email,
      'email_error' => $email_error,
    ));
  }

  public function actionLogout(){
    // Сохранение выходных данных
    if( $this->app->user->role == 'user' ){
      $identity = $this->app->user->identity;
      $identity->last_update_time = 0;
      $identity->usersOnlineDel();
      $identity->update();
    }

    Request::session('captcha_off', '');
    Request::session('active_language', '');

    $this->app->user->logout();
    return $this->redirect('main/login');
  }

  public function actionIndex(){
    return $this->redirect('sites/index');
  }

  public function actionDownload(){
    $basename = basename(Request::get('file'));
    $file = __DIR__.'/../files/'.$basename;
    header('Content-Disposition: attachment; filename="'.$basename);
    readfile($file);
  }

  /*
  public function actionSettings(){
    return $this->render('settings');
  }
  */
  public function actionAjax(){
    /*
    switch( Request::post('action') ){
      case 'save-language':
        $this->app->language->saveActive();
      break;
    }
    */
  }

  public function sendNotification(){
    $users = new Users();

    $days = $this->app->config['notification_days'];

    if( empty($days) ) return;

    $sql_add = [];
    foreach($days as $day){
      $sql_add[] = '
      (
        activated_time + activated_add_time - (86400*'.($day-1).') >= '.time().' and
        activated_time + activated_add_time - (86400*'.($day).') < '.time().'
      )
      ';
    }

    $items = $users->where('
      activated_time > 0 and
      activated_time + activated_add_time > '.time().' and
      email_send_time + 86400 < '.time().' and
      (
        '.implode(' or ', $sql_add).'
      )
    ')->limit(0, $this->app->config['notification_limit'])->findAll();

    $languagesData = $this->app->language->data();

    foreach($items as $item){
      $item->email_send_time = time();
      $item->update();

      $language = $item->getLanguage();

      $daysLeft = ceil((($item->activated_time + $item->activated_add_time) - time()) / 86400);
      $message = str_replace('_DAYS_', $daysLeft, $languagesData[$language]['notification_message']);

      echo $languagesData[$language]['notification_subject'].'<br />';
      echo $message.'<br /><br />';

      mail($item->email, $languagesData[$language]['notification_subject'], $message);
    }
  }

  public function actionCron(){
    $this->sendNotification();
  }

  public function actionInstall(){
    if( Request::get('password') != $this->app->config['admin_password'] ){
      die( App::t('Доступ запрещен!') );
    }

    $result = $this->app->pdo->query('SELECT 1 FROM sites');

    if( !$result ){
      $this->app->pdo->exec(file_get_contents('../config/sql.sql'));
      echo App::t('База импортирована.');
    }else{
      echo App::t('База не нуждается в импорте.');
    }
  }
}
