<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?=App::t('Создание записи')?></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<ul class="breadcrumb">
<li><a href="<?=App::get()->controllerName?>"><?=App::t('База данных')?></a></li>
<li class="active"><?=App::t('Создание записи')?></li>
</ul>

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">

            </div>
            <div class="panel-body">
              <form role="form" method="post">
                  <?=Form::dropDownList($model, 'category', [
                      App::get()->config['base_1_name'] => 1,
                      App::get()->config['base_2_name'] => 2,
                    ])?>
                  <?=Form::textInput($model, 'link')?>
                  <?=Form::textArea($model, 'note', ['rows' => 5])?>
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
