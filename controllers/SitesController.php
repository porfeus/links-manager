<?php
class SitesController extends BaseController{

  public function accessRules(){
    return [
        [
            'allow' => true,
            'roles' => ['admin'],
        ],
        [
            'allow' => true,
            'actions' => ['index', 'redirect', 'complain'],
            'roles' => ['user'],
        ],
    ];
  }

  public function actionRedirect(){
    $model = new Sites();
    $model->eq('id', Request::get('id'))->find();

    header('Location: '.$model->link);
    exit;
  }

  public function actionComplain(){
    $model = new ComplainForm();
    $model->load($_GET);

    if( !$model->validate() ){
      die(json_encode($model->errors));
    }

    $site = $model->eq('id', Request::get('id'))->find();

    if( Request::get('problem') == 'other' ){
      $message = 'Другое: '.Request::get('message');
    }else{
      $problems = array_flip($model->problemList());
      $message = $problems[ Request::get('problem') ];
    }

    mail(
      $this->app->config['admin_email'],
      'Сайт не работает',
      'База: '.$this->app->config['base_'.$site->category.'_name'].'. ID-'.$site->num.'. '.$message.
      '. Пользователь: '.$this->app->user->identity->email.
      '. Ссылка: '.Request::site().'admin/sites/edit?id='.$site->id
    );

    die(json_encode(['status'=>'ok']));
  }

  public function actionIndex(){
    $sites = new Sites();

    if( Request::get('action') == 'ajax' ){
      header('Content-type: application/json');

      $start = Request::post('start');
      $search = Request::post('search');
      $columns = Request::post('columns');

      $length = Request::post('length');
      if( $length > 1000 ) $length = 1000;

      //filter
      if( !empty($search['value']) ){
        $sites->like('link', '%'.addslashes($search['value']).'%');
      }
      $sites->eq('category', $_GET['category']);
      $sites->limit($start, $length);
      $items = $sites->orderby('id')->findAll();
      //end filter

      //count filter
      $filtered = $sites->select('count(1) as count');
      if( !empty($search['value']) ){
        $filtered->like('link', '%'.addslashes($search['value']).'%');
      }
      $filtered->eq('category', $_GET['category']);
      $filtered = $filtered->find()->count;
      //end count filter

      //count total
      $total = $sites->select('count(1) as count')
        ->eq('category', $_GET['category'])
        ->find()->count;
      //end count total

      $data = [];
      foreach($items as $item){

        //add link to note
        $note = preg_replace('@(https?:\/\/([^\/ $]+)[^ $]+)@', '<a href="$1" target="_blank">$2</a>', $item->note);
        //end add link to note

        array_push($data, [
          'id' => $item->id,
          'num' => $item->num,
          'link' => htmlspecialchars($item->shortLink()),
          'note' => htmlspecialchars($note),
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
    }else{
      $total_db1 = $sites->select('count(1) as count')
        ->eq('category', 1)
        ->find()
        ->count;

      $total_db2 = $sites->select('count(1) as count')
        ->eq('category', 2)
        ->find()
        ->count;
    }

    return $this->render('sites/index', array(
      'total_db1' => $total_db1,
      'total_db2' => $total_db2,
      'activated' => $activated,
      'model' => $sites,
    ));
  }

  public function actionCreate(){
    $model = new Sites();

    if( Request::issetPost() ){
      $model->load($_POST);

      if( $model->validate() ){
        $model->insert();
        $model->updateNum($model->category); //Обновляем нумерацию
        return $this->redirect('sites/index');
      }
    }

    return $this->render('sites/create', [
      'model' => $model,
    ]);
  }

  public function actionEdit(){
    $model = new Sites();
    $model->eq('id', Request::get('id'))->find();

    if( empty($model->data) ){
      return $this->redirect('sites/index');
    }

    if( Request::issetPost() ){
      $model->load($_POST);

      if( $model->validate() ){
        $model->update();
        return $this->redirect('sites/index');
      }
    }

    return $this->render('sites/edit', [
      'model' => $model,
    ]);
  }

  public function actionDelete(){
    $deleteList = Request::post('delete');
    if( !empty($deleteList) ){
      $sites = new Sites();
      $items = $sites->in('id', array_values($deleteList))->findAll();
      $categories = [];
      foreach($items as $item){
        $categories[$item->category] = true;
        $item->delete();
      }

      foreach($categories as $key=>$status){
        $sites->updateNum($key); //Обновляем нумерацию
      }
    }
  }

  public function actionImport(){
    $model = new SitesImportForm();

    $files = glob('../files/*');
    $files = array_map(function($file){
      return basename($file);
    }, $files);
    $files = array_filter($files, function($file){
      if( $file == 'generate.csv' ) return false;
      return true;
    });

    $files = array_values($files);

    $result = false;

    if( Request::issetPost() ){
      $model->load(array_merge($_POST, $_FILES));

      if( $model->validate() ){

        $links = [];

        switch( $model->variant ){
          case "file":
            $file = $_FILES['file']['tmp_name'];
            if( !empty($file) && is_file($file) ){
              $links = file($file);
            }
          break;
          case "server":
            $file = __DIR__.'/../files/'.$model->server;
            if( !empty($file) && is_file($file) ){
              $links = file($file);
            }
          break;
          case "field":
            $links = explode("\n", $model->field);
          break;
        }

        $add = 0;
        $del = 0;
        if( !empty($links) ){
          foreach($links as $link){
            $link = trim($link);
            $link = iconv('WINDOWS-1251', 'UTF-8', $link);
            if( empty($link) ) continue;

            if( $model->unique ){
              $duplicatesNum = $model->select('count(1) as count')
                ->like('link', '%'.$model->getDomain($link).'%')
                ->find()->count;

              if( $duplicatesNum ){
                $del ++;
                continue;
              }
            }

            $site = new Sites();
            $site->category = $model->category;
            $site->link = trim($link);
            $site->note = $model->note;
            $site->insert();
            $add ++;
          }
        }

        $model->updateNum($model->category); //Обновляем нумерацию

        $result = [
          'add' => $add,
          'del' => $del,
        ];
      }
    }

    return $this->render('sites/import', [
      'model' => $model,
      'files' => $files,
      'result' => $result,
    ]);
  }
}
