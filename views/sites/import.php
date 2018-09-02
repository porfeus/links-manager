<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?=App::t('Импорт базы')?></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<ul class="breadcrumb">
<li><a href="<?=App::get()->controllerName?>"><?=App::t('База данных')?></a></li>
<li class="active"><?=App::t('Импорт базы')?></li>
</ul>

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">

            </div>
            <div class="panel-body">
              <form role="form" method="post" enctype="multipart/form-data">
                  <?=Form::dropDownList($model, 'variant', [
                    'С файла на компьютере' => 'file',
                    'С файла в папки files на сервере' => 'server',
                    'С поля ввода' => 'field',
                    ])?>
                  <?=Form::fileInput($model, 'file')?>
                  <?=Form::dropDownList($model, 'server', $files)?>
                  <?=Form::textArea($model, 'field', ['rows' => 10])?>
                  <?=Form::dropDownList($model, 'category', [
                      App::get()->config['base_1_name'] => 1,
                      App::get()->config['base_2_name'] => 2,
                    ])?>
                  <?=Form::dropDownList($model, 'unique', [
                    'Да' => 1,
                    'Нет' => 0,
                    ])?>
                  <?=Form::textArea($model, 'note', ['rows' => 5])?>
                  <button type="submit" class="btn btn-success"><?=App::t('Импортировать')?></button>
              </form>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-6 -->
</div>
<!-- /.row -->

<script>
$('.file-block, .server-block, .field-block').hide();
if( $('[name="variant"]').val() ){
  $('.'+ $('[name="variant"]').val() +'-block').show();
}

$('[name="variant"]').on('change', function(){
  $('.file-block, .server-block, .field-block').hide();
  $('.'+ this.value +'-block').show();
});

<?php if( $result ){
?>
$(function(){
  var modalBody = '<?=str_replace('_ADD_', $result['add'], App::t('Добавлено записей: _ADD_ ед.'))?><?=(($model->unique == 1)? ', '.str_replace('_DEL_', $result['del'], App::t('удалено дублей: _DEL_ ед.')):'')?>';

  $('#myModal .modal-title').text('<?=App::t('Загрузка завершена')?>');
  $('#myModal .modal-body').text(modalBody);
  $('#myModal').modal('show').find('.modal-info').show();
});
<?php
}?>
</script>
