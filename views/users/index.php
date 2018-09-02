<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?=App::t('Пользователи')?></h1>
        <p style="float: right">
          <select class="form-control" id="activated-filter">
            <option value="yes">Активированные</option>
            <option value="no">Не активированные</option>
            <option value="all">Любые</option>
          </select>
        </p>
        <p>
          <a class="btn btn-success" href="users/create"><?=App::t('Создать пользователя')?></a>
          <a class="btn btn-primary" href="users/generate"><?=App::t('Сгенерировать')?></a>
        </p><br />
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-md-12">
        <form method="post" id="delete-form">
          <div class="panel panel-default">
              <div class="panel-heading">
                  <?=App::t('Всего пользователей')?>: <?=$total?>,
                  <?=App::t('активированных')?>: <?=$activated?>
              </div>
              <!-- /.panel-heading -->
              <div class="panel-body">
                  <table width="100%" class="dataTables table table-striped table-hover ">
                      <thead>
                          <tr>
                              <th>
                                  <input type="checkbox" onclick="checkAll(this)" />
                              </th>
                              <th><?=$model->getLabel('login')?></th>
                              <th><?=$model->getLabel('password')?></th>
                              <th><?=$model->getLabel('email')?></th>
                              <th><?=$model->getLabel('ip_old')?></th>
                              <th><?=$model->getLabel('ip_new')?></th>
                              <th><?=$model->getLabel('users_limit')?></th>
                              <th><?=$model->getLabel('activated_time')?></th>
                              <th><?=$model->getLabel('activated_add_time')?></th>
                              <th><?=$model->getLabel('last_enter_time')?></th>
                              <th><?=App::t('Действие')?></th>
                          </tr>
                      </thead>
                  </table>
                  <!-- /.table-responsive -->
              </div>
              <!-- /.panel-body -->
          </div>
          <!-- /.panel -->
        </form>
        <p>
          <a type="submit" class="btn btn-danger" id="delete-button"><?=App::t('Удалить отмеченное')?></a>
        </p>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<script>
$(document).ready(function() {
    window.dataTable = $('.dataTables').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url" :"./users?action=ajax",
          "type": "post"
        },
        "columns":[
            { "data": function ( row, type, set ) {
                return '<input type="checkbox" name="delete[]" value="'+ row.id +'" />' +
                (row.online? '<span class="online"></span>':'');
            } },
            { "data": "login" },
            { "data": "password" },
            { "data": "email" },
            { "data": "ip_old" },
            { "data": "ip_new" },
            { "data": "users_limit" },
            { "data": "activated_time" },
            { "data": "activated_add_time" },
            { "data": "last_enter_time" },
            { "data": function ( row, type, set ) {
                return [
                  '<a href="users/edit?id='+ row.id +'"><i class="glyphicon glyphicon-pencil" title="<?=App::t('Редактировать')?>"></i></a>',
                  '<a href="#" data-id="'+ row.id +'" onclick="deleteLink(event, this)"><i class="glyphicon glyphicon-trash" title="<?=App::t('Удалить')?>"></i></a>'
                ].join(' ');
            } }
        ],
        "responsive": true,
        "sort": false,
        "pageLength": <?=App::get()->config['users_show_num']?>,
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

    $('#activated-filter').on('change', function(){
      window.dataTable
        .column( 8 )
        .search( this.value )
        .draw();
    });

    $('#delete-button').on('click', function(){
      if( !confirm("<?=App::t('Удалить выбранное')?>?") ) return;
      $.post('users/delete', $( "#delete-form" ).serialize(), function(){
        window.dataTable.draw();
        $('.dataTables').find('[type="checkbox"]').prop('checked', false);
      });
    });

    $('.dataTables').on( 'draw.dt', function () {
        $(this).find('.online').each(function(){
          $(this).parents('tr').css('background-color', '#d6e9c6');
        });
    });
});

function deleteLink(e, o){
  e.preventDefault();
  if( !confirm("<?=App::t('Удалить выбранное')?>?") ) return;
  $.post('users/delete', 'delete[0]='+ $(o).data('id') +'', function(){
    window.dataTable.draw();
  });
}
</script>
