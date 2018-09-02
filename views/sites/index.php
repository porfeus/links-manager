<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?=App::t('База данных')?></h1>
        <?php if(App::get()->user->role == 'admin'){ ?>
        <p>
          <a class="btn btn-success" href="sites/create"><?=App::t('Создать запись')?></a>
          <a class="btn btn-primary" href="sites/import"><?=App::t('Импорт базы')?></a>
        </p><br />
        <?php } ?>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">

    <?php
    $tablesWidth = 'col-md-6';
    if(  App::get()->user->role == 'user'){
      if( !App::get()->config['show_base_1'] || !App::get()->config['show_base_2'] ){
        $tablesWidth = 'col-md-12';
      }
    }
    ?>

    <?php if(App::get()->config['show_base_1'] || App::get()->user->role == 'admin'){ ?>
    <div class="<?=$tablesWidth?>" style="min-width:500px;">
      <form method="post" id="dataform_1" data-category="1">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?=App::get()->config['base_1_name']?>,
                <?=App::t('записей')?>: <?=$total_db1?>

                <?php if( !empty(App::t('Описание базы 1')) ){ ?>
                <p class="text-center alert alert-success">
                  <?=App::t('Описание базы 1')?>
                </p>
                <?php } ?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <table width="100%" id="datatable_1" class="dataTables table table-striped table-hover ">
                    <thead>
                        <tr>
                            <?php if(App::get()->user->role == 'admin'){ ?>
                            <th class="text-center">
                                <input type="checkbox" onclick="checkAll(this)" />
                            </th>
                            <?php } ?>
                            <th class="text-center">#</th>
                            <th class="text-center"><?=App::t('Сайты')?></th>
                            <th class="text-center"><?=App::t('Примечание')?></th>
                            <th class="text-center"><?=App::t('Действия')?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <!-- /.table-responsive -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
        <?php if(App::get()->user->role == 'admin'){ ?>
        <p>
          <a type="submit" class="btn btn-danger delete-button" onclick="deleteButton(event, 1)"><?=App::t('Удалить отмеченное')?></a>
        </p>
        <?php } ?>
      </form>
    </div>
    <!-- /.col-lg-12 -->
    <?php } ?>

    <?php if(App::get()->config['show_base_2'] || App::get()->user->role == 'admin'){ ?>
    <div class="<?=$tablesWidth?>">
      <form method="post" id="dataform_2" data-category="2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?=App::get()->config['base_2_name']?>,
                <?=App::t('записей')?>: <?=$total_db2?>

                <?php if( !empty(App::t('Описание базы 2')) ){ ?>
                <p class="text-center alert alert-success">
                  <?=App::t('Описание базы 2')?>
                </p>
                <?php } ?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <table width="100%" id="datatable_2" class="dataTables table table-striped table-hover">
                    <thead>
                        <tr>
                            <?php if(App::get()->user->role == 'admin'){ ?>
                            <th class="text-center">
                                <input type="checkbox" onclick="checkAll(this)" />
                            </th>
                            <?php } ?>
                            <th class="text-center">#</th>
                            <th class="text-center"><?=App::t('Сайты')?></th>
                            <th class="text-center"><?=App::t('Примечание')?></th>
                            <th class="text-center"><?=App::t('Действия')?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <!-- /.table-responsive -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
        <?php if(App::get()->user->role == 'admin'){ ?>
        <p>
          <a type="submit" class="btn btn-danger delete-button" onclick="deleteButton(event, 2)"><?=App::t('Удалить отмеченное')?></a>
        </p>
        <?php } ?>
      </form>
    </div>
    <!-- /.col-lg-12 -->
    <?php } ?>
</div>
<!-- /.row -->

<script>
$(document).ready(function() {
    window.dataTable = {};

    for(var category = 1; category <= 2; category++ ){
      window.dataTable[category] = $('#datatable_'+ category).DataTable({
          "processing": true,
          "serverSide": true,
          "pagingType": "listbox",
          "ajax": {
            "url" :"./sites?action=ajax&category=" + category,
            "type": "post"
          },
          "columns":[
              <?php if(App::get()->user->role == 'admin'){ ?>
              { "data": function ( row, type, set ) {
                  return '<input type="checkbox" name="delete[]" value="'+ row.id +'" />';
              } },
              <?php } ?>
              { "data": "num" },
              { "data": function ( row, type, set ) {
                  return '<a style="float:left" href="sites/redirect?id='+ row.id +'" target="_blank">'+ row.link +'</a>';
              } },
              { "data": function ( row, type, set ) {
                  if( row.note != '' ){
                    return '<a data-note="'+ row.note +'" class="btn btn-success" onclick="noteLink(event, this)"><?=App::t('Примечание')?></a>';
                  }
                  return '';
              } },
              { "data": function ( row, type, set ) {
                  <?php if(App::get()->user->role == 'admin'){ ?>
                  return [
                    '<a href="sites/edit?id='+ row.id +'"><i class="glyphicon glyphicon-pencil" title="<?=App::t('Редактировать')?>"></i></a>',
                    '<a href="#" data-id="'+ row.id +'" onclick="deleteLink(event, this)"><i class="glyphicon glyphicon-trash" title="<?=App::t('Удалить')?>"></i></a>'
                  ].join(' ');
                  <? }else{ ?>
                    return [
                      '<a onclick="complainLink(event, this)" data-id="'+ row.id +'" href="#"><i class="fa fa-frown-o fa-fw" title="<?=App::t('Вы можете отправить сообщение администратору об этом сайте')?>"></i></a>'
                    ].join(' ');
                  <?php } ?>
              } }
          ],
          "responsive": true,
          "sort": false,
          "pageLength": <?=App::get()->config['sites_show_num']?>,
          "language": {
            "infoFiltered": "",
            "paginate": {
            "first": "<?=App::t('Первая')?>",
            "last": "<?=App::t('Последняя')?>",
            "next": "<?=App::t('Далее')?>",
            "previous": "<?=App::t('Назад')?>"
          },
          "emptyTable": "<?=App::t('Таблица пуста')?>",
          "info": "<?=App::t('Страница _PAGE_ из _PAGES_')?>",
          "infoEmpty": "<?=App::t('Нет записей для отображения')?>",
          "lengthMenu": "",
          "loadingRecords": "<?=App::t('Пожалуйста, ждите...')?>",
          "processing": "<?=App::t('Пожалуйста, ждите...')?>",
          "search": "<?=App::t('Поиск:')?>",
          "zeroRecords": "<?=App::t('Нет записей для отображения')?>"
        }
      });
    }
});

function deleteButton(e, category){
  if( !confirm("<?=App::t('Удалить выбранное')?>?") ) return;
  $.post('sites/delete', $( '#dataform_'+ category ).serialize(), function(){
    window.dataTable[category].draw();

    $('#dataform_'+ category).find('[type="checkbox"]').prop('checked', false);
  });
}

function deleteLink(e, o){
  e.preventDefault();
  if( !confirm("<?=App::t('Удалить выбранное')?>?") ) return;
  var category = $(o).parents('form').data('category');
  $.post('sites/delete', 'delete[0]='+ $(o).data('id') +'', function(){
    window.dataTable[category].draw();
  });
}

function complainLink(e, o){
  e.preventDefault();
  $('#myModal .modal-title').text('<?=App::t('Пожаловаться на сайт')?>');

  $('#myModal .modal-body').html('');
  $('#complain-body [name="id"]').val($(o).data('id'));
  $('#myModal .modal-body').append($('#complain-body').html());
  $('#myModal').modal('show').find('.modal-send').show();

  $('#myModal .modal-confirm').off('click').on('click', function(){
     $.getJSON( "sites/complain", $( '#myModal .complain-form' ).serialize(), function(data){
       $('#myModal .complain-form .has-error')
        .removeClass('has-error')
        .find('.help-block')
        .text('');

       if( data.status == 'ok' ){
         $('#myModal .modal-body').text('<?=App::t('Спасибо, сообщение отправлено администратору.')?>');
         $('#myModal .modal-footer').hide();
         $('#myModal').modal('show').find('.modal-info').show();

         window.modalHideTimeout = setTimeout(function(){
           $('#myModal').modal('hide');
         }, 10000);
       }else{
         for(var name in data){
           $('#myModal .complain-form .'+ name +'-block')
            .addClass('has-error')
            .find('.help-block')
            .text(data[name]);
         }
       }
     });
  });
}

function noteLink(e, o){
  e.preventDefault();

  if( $(o).text() == '<?=App::t('Примечание')?>' ){
    var html = '';
    html += '<div class="text-center"><b><?=App::t('Примечание')?>:</b></div>';
    html += $(o).data('note');
    $(o).parents('tr').after('<tr><td colspan="20" style="text-align:left">'+ html +'</td></tr>');
    $(o).text('<?=App::t('Скрыть')?>')
    $(o).addClass('btn-warning').removeClass('btn-success');
  }else{
    $(o).text('<?=App::t('Примечание')?>');
    $(o).addClass('btn-success').removeClass('btn-warning');
    $(o).parents('tr').next().remove();
  }
}
</script>


<div style="display: none" id="complain-body">
  <form role="form" method="post" class="complain-form">
    <input type="hidden" name="id" value="" />
    <?php
    $ComplainForm = new ComplainForm();
    echo Form::dropDownList($ComplainForm, 'problem', $ComplainForm->problemList());
    echo Form::textArea($ComplainForm, 'message', ['rows' => 5, 'maxlength'=>500]);
    ?>
  </form>
  <script>
  $('.message-block').hide();

  $('.complain-form [name="problem"]').on('change', function(){
    if( $(this).val() == 'other' ){
      $('.message-block').show();
    }else{
      $('.message-block').hide();
    }
  });
  </script>
</div>
