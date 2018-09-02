<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <base href="<?=Request::basedir()?>" />

    <title><?=App::t('База сайтов')?></title>

    <!-- Bootstrap Core CSS -->
    <link href="../design/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="../design/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="../design/datatables-responsive/dataTables.responsive.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../css/style.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../design/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- Languages for Bootstrap -->
    <link href="../design/languages/languages.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery -->
    <script src="../design/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../design/bootstrap/js/bootstrap.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="../design/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../design/datatables-plugins/select.js"></script>
    <script src="../design/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../design/datatables-responsive/dataTables.responsive.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../js/common.js"></script>

    <?php
    //отключаем копирование для пользователей
    if(App::get()->user->role == 'user'){ ?>
    <script src="../js/disable_copy.js"></script>
    <link href="../css/disable_copy.css" rel="stylesheet">
    <?php } ?>
</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only"><?=App::t('Смена навигации')?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand"><?=App::t('База сайтов')?></a>
            </div>
            <!-- /.navbar-header -->
            <?php if(App::get()->user->role == 'admin'){ //admin ?>
            <div class="collapse navbar-collapse">
              <ul class="nav navbar-nav navbar-top-links navbar-left" id="main-links">
                  <li>
                      <a href="sites">
                          <i class="fa fa-database fa-fw"></i> <?=App::t('База данных')?>
                      </a>
                  </li>
                  <li>
                      <a href="users">
                          <i class="fa fa-users fa-fw"></i> <?=App::t('Пользователи')?>
                      </a>
                  </li>
                  <!-- /.dropdown -->
                  <!-- <li class="dropdown">
                      <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                          <i class="fa fa-sliders fa-fw"></i> <?=App::t('Настройки')?> <i class="fa fa-caret-down"></i>
                      </a>
                      <ul class="dropdown-menu">
                          <li>
                              <a href="#"><i class="fa fa-download fa-fw"></i> <?=App::t('Импорт базы данных')?></a>
                          </li>
                          <li>
                              <a href="#"><i class="fa fa-upload fa-fw"></i> <?=App::t('Экспорт базы данных')?></a>
                          </li>
                          <li>
                              <a href="settings"><i class="glyphicon glyphicon-wrench"></i> <?=App::t('Общие настройки')?></a>
                          </li>
                      </ul>
                  </li> -->
                  <!-- /.dropdown -->
              </ul>

              <ul class="nav navbar-nav navbar-top-links navbar-right">
                  <li class="dropdown">
                      <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                          <i class="fa fa-user fa-fw"></i> <?=App::get()->user->identity->login?> <i class="fa fa-caret-down"></i>
                      </a>
                      <ul class="dropdown-menu">
                          <li>
                              <a href="main/logout"><i class="fa fa-sign-out fa-fw"></i> <?=App::t('Выход')?></a>
                          </li>
                      </ul>
                      <!-- /.dropdown-user -->
                  </li>
              </ul>
              <!-- /.navbar-top-links -->
            </div>
        </nav>
            <?php } // end admin ?>

            <?php if(App::get()->user->role == 'user'){ // user ?>
            <div class="collapse navbar-collapse">
              <ul class="nav navbar-nav navbar-top-links navbar-left" id="main-links">
                  <li>
                      <a href="sites">
                          <i class="fa fa-database fa-fw"></i> <?=App::t('База данных')?>
                      </a>
                  </li>
              </ul>

              <ul class="nav navbar-nav navbar-top-links navbar-right">
                <?=App::get()->language->dropdown()?>

                  <li class="dropdown">
                      <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                          <i class="fa fa-user fa-fw"></i> <?=App::get()->user->identity->login?> <i class="fa fa-caret-down"></i>
                      </a>
                      <ul class="dropdown-menu">
                          <li>
                              <a href="main/logout"><i class="fa fa-sign-out fa-fw"></i> <?=App::t('Выход')?></a>
                          </li>
                      </ul>
                      <!-- /.dropdown-user -->
                  </li>
              </ul>
              <!-- /.navbar-top-links -->
            </div>
        </nav>
        <div class="text-center alert alert-info">

          <?=App::t('E-mail')?>:
          <?=App::get()->user->identity->email?>,
          <?=App::t('лимит пользователей')?>:
          <?=App::get()->user->identity->users_limit?>,
          <?=App::t('активирован')?>:
          <?=App::get()->user->identity->activatedDate()?>,
          <?=App::t('окончание активации')?>:
          <?=App::get()->user->identity->activatedEndDate()?>

        </div>
          <?php } // end user ?>

        <div id="page-wrapper">
          <?=$content?>
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer modal-save" style="display: none">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=App::t('Нет')?></button>
                    <button type="button" class="btn btn-primary modal-confirm"><?=App::t('Сохранить')?></button>
                </div>

                <div class="modal-footer modal-send" style="display: none">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=App::t('Нет')?></button>
                    <button type="button" class="btn btn-primary modal-confirm"><?=App::t('Отправить')?></button>
                </div>
                <div class="modal-footer modal-info" style="display: none">
                    <button type="button" class="btn btn-primary" data-dismiss="modal"><?=App::t('Закрыть')?></button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <?php /*if( Request::session('confirm_language') ){
      Request::session('confirm_language', '');
    ?>
      <script>
      $('#myModal .modal-title').text('<?=App::t('Требуется подтверждение')?>');
      $('#myModal .modal-body').text('<?=App::t('Сохранить выбранный вами язык?')?>');
      $('#myModal').modal('show').find('.modal-save').show();
      $('#myModal .modal-confirm').off('click').on('click', function(){
        $('#myModal').modal('hide');
        $.post( "main/ajax", { action: "save-language" } );
      });
      </script>
    <?php }*/ ?>
    <script>
    //select active navbar link
    $('#main-links > li').each(function(){
      var href = $(this).find('a').attr('href').replace('.', '');
      if( location.pathname.replace(/index$/, '').match(new RegExp(href + "$")) ){
        $(this).addClass('active');
      }
    });

    $('#myModal').on('hidden.bs.modal', function (e) {
      $('#myModal .modal-footer').hide();
      clearTimeout(window.modalHideTimeout);
    });
    </script>
</body>

</html>
