<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?=App::t('Редактирование пользователя')?>: <?=$model->login?></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<ul class="breadcrumb">
<li><a href="<?=App::get()->controllerName?>"><?=App::t('Пользователи')?></a></li>
<li class="active"><?=App::t('Редактирование пользователя')?></li>
</ul>

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">

            </div>
            <div class="panel-body">
              <form role="form" method="post">
                  <?=Form::textInput($model, 'login')?>
                  <?=Form::textInput($model, 'password')?>
                  <?=Form::textInput($model, 'email')?>
                  <?=Form::textInput($model, 'activated_days', ['label' => App::t('Добавить/отнять дни активации')])?>
                  <?=Form::dropDownList($model, 'users_limit', [1,2,3,4,5,6,7,8,9,10])?>
                  <button type="submit" class="btn btn-success"><?=App::t('Сохранить')?></button>
              </form>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-6 -->
</div>
<!-- /.row -->
