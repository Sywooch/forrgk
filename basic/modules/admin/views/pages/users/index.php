<?php
/* @var $this yii\web\View */
use yii\helpers\Url;
use yii\helpers\Html;
?>
<h1>Пользователи системы</h1>

<table class="table table-bordered table-hover">
    <thead>
    <tr>
        <th data-order="id">
            ID <span></span>
        </th>
        <th data-order="username">
            Имя пользователя <span></span>
        </th>
        <th data-order="email">
            email <span></span>
        </th>
        <th data-order="is_admin">
            Админ? <span></span>
        </th>
        <th>
            Действия
        </th>
    </tr>
    </thead>
    <tbody id="tout">
    <tr>
        <td colspan="5" class="text-center text-muted"><i class="fa fa-spin fa-spinner"></i> Загрузка данных, пожалуйста, подождите... </td>
    </tr>
    </tbody>
</table>
<style>
    th[data-order] {
        cursor: pointer;
    }
    th[data-order]:hover {
        text-decoration: underline;
    }
</style>
<div class="text-center">
    <ul class="pagination" id="paginator" style="display: none;">

    </ul>
</div>
<script>
    var currentOrder = '';
    var currentAsc   = '';
    var currentPage  = 1;

    function loadPage(page, order, asc) {
        $.ajax({
            url: '<?php echo Url::to(['pages/users/users']);?>',
            type: 'POST',
            data: {
                'page': page,
                'order': order,
                'asc': asc
            },
            dataType: 'json',
            beforeSend: function () {
                waitingDialog.show('Загрузка данных...');
            },
            complete: function () {
                waitingDialog.hide();
            },
            error: function () {
                alert('Произошла ошибка. Перезагрузите страницу...');
            },
            success: function (data) {

                currentPage = data.page;

                // set pageinator
                if (data.pages > 1) {
                    $('#paginator').html('');

                    //prev btn
                    if (data.page > 1) {
                        $('#paginator').append($('<li><a data-page="'+(data.page-1)+'" href="javascript:void(0);"><span><i class="fa fa-angle-double-left"></i></span></a></li>'));
                    }


                    for (var i=1;i<=data.pages;++i) {
                        var $item = $('<li><a data-page="'+(i)+'" href="javascript:void(0);">'+i+'</a></li>');
                        if (i == data.page) $item.addClass('active');
                        $('#paginator').append($item);
                    }


                    // next btn
                    if (data.page < data.pages) {
                        $('#paginator').append($('<li><a data-page="'+(data.page+1)+'" href="javascript:void(0);"><span><i class="fa fa-angle-double-right"></i></span></a></li>'));
                    }

                    $('#paginator').show();
                } else {
                    $('#paginator').hide();
                }



                // set data
                var $tout = $('#tout');
                $tout.html('');;
                for(var i=0;i<data.items.length;++i) {
                    var $tr = $('<tr><td></td><td></td><td></td><td></td><td></td></tr>');

                    $tr.find('td:eq(0)').text(data.items[i].id);
                    $tr.find('td:eq(1)').text(data.items[i].username);
                    $tr.find('td:eq(2)').text(data.items[i].email);

                    if (data.items[i].is_admin == 1) {
                        $tr.find('td:eq(3)').html($('<a href="javascript:void(0);" data-set="1" class="btn btn-success btn-sm admin-btn" data-id="'+data.items[i].id+'">Да</a>'));
                    } else {
                        $tr.find('td:eq(3)').html($('<a href="javascript:void(0);" data-set="0" class="btn btn-danger btn-sm admin-btn" data-id="'+data.items[i].id+'">Нет</a>'));
                    }


                    if (data.items[i].halted == 0) {
                        $tr.find('td:eq(4)').html($('<a href="javascript:void(0);" class="btn btn-danger remove-btn btn-sm ban-btn" data-id="' + data.items[i].id + '" data-set="0" title="Забанить"><i class="fa fa-times"></i></a>'));
                    } else {
                        $tr.find('td:eq(4)').html($('<a href="javascript:void(0);" class="btn btn-success remove-btn btn-sm ban-btn" data-id="' + data.items[i].id + '" data-set="1" title="Разбанить"><i class="fa fa-check"></i></a>'));
                    }
                    $tout.append($tr);
                }


                // if empty
                if (!data.items.length) {
                    $('#tout').html('<tr><td class="text-muted text-center" colspan="5">Статьи ещё не были созданы</td></tr>');
                }

            }
        });
    }

    (function () {
        // set ordering
        $('th[data-order]').click(function () {
            currentOrder = $(this).attr('data-order');
            currentAsc   = $(this).attr('data-asc');


            $(this).parent().find('span').html('');
            if (currentAsc == 'asc') {
                currentAsc = 'desc';
                $(this).children('span').html('<i class="fa fa-caret-down"></i>');
            } else {
                currentAsc = 'asc';
                $(this).children('span').html('<i class="fa fa-caret-up"></i>');
            }

            $(this).attr('data-asc', currentAsc);


            loadPage(currentPage, currentOrder, currentAsc);

        });


        // set paging
        $('#paginator').on('click', 'li > a', function () {
            var page = $(this).attr('data-page');

            currentPage = page;

            loadPage(currentPage, currentOrder, currentAsc);
            return false;
        });


        // admin btn parser
        $('#tout').on('click', '.admin-btn', function () {
            if (!confirm($(this).attr('data-set') == 0 ? 'Вы действительно хотите выдать привелегии администратора?' : 'Вы действительно хотите забрать привелегии администратора?')) return false;

            $.ajax({
                url: '<?php echo Url::to(['pages/users/admin']);?>',
                'type': 'POST',
                data: {
                    'id': $(this).attr('data-id'),
                    'mode':($(this).attr('data-set') == 0 ? 1 : 0)
                },
                beforeSend: function () {
                    waitingDialog.show('Загрузка данных...');
                },
                complete: function () {
                    waitingDialog.hide();
                },
                error: function () {
                    alert('Произошла ошибка. Перезагрузите страницу...');
                },
                success: function (data) {
                    if (data != 'OK') alert(data);

                    loadPage(currentPage, currentOrder, currentAsc);
                }
            });
            return false;
        }).on('click', '.ban-btn', function () {
            if (!confirm($(this).attr('data-set') == 0 ? 'Вы действительно хотите забанить пользователя?' : 'Вы действительно хотите разбанить пользователя?')) return false;

            $.ajax({
                url: '<?php echo Url::to(['pages/users/ban']);?>',
                'type': 'POST',
                data: {
                    'id': $(this).attr('data-id'),
                    'mode':($(this).attr('data-set') == 0 ? 1 : 0)
                },
                beforeSend: function () {
                    waitingDialog.show('Загрузка данных...');
                },
                complete: function () {
                    waitingDialog.hide();
                },
                error: function () {
                    alert('Произошла ошибка. Перезагрузите страницу...');
                },
                success: function (data) {
                    if (data != 'OK') alert(data);

                    loadPage(currentPage, currentOrder, currentAsc);
                }
            });
        });



        // ban btn handler


        $(function () {loadPage(currentPage, currentOrder, currentAsc);});
    })();
</script>
