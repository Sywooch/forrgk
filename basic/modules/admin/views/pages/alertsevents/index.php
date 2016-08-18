<?php
use yii\helpers\Url;
?>

<div class="pull-left">
    <h1>Уведомления событий</h1>
    <h5>Настройка отправки уведомлений событий</h5>
</div>
<div class="pull-right" style="padding-top: 20px;">
    <a href="<?php echo Url::to(['pages/alertsevents/alertevent']);?>" class="btn btn-warning"><i class="fa fa-plus"></i> Создать уведомление события</a>
</div>
<div class="clearfix"></div>
<br />
<table class="table table-bordered table-hover">
    <thead>
    <tr>
        <th data-order="id">
            ID <span></span>
        </th>
        <th data-order="name">
            Название <span></span>
        </th>
        <th data-order="alert_type">
            Тип <span></span>
        </th>
        <th>
            Действия
        </th>
    </tr>
    </thead>
    <tbody id="tout">
    <tr>
        <td colspan="6" class="text-center text-muted"><i class="fa fa-spin fa-spinner"></i> Загрузка данных, пожалуйста, подождите... </td>
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
            url: '<?php echo Url::to(['pages/alertsevents/alerts']);?>',
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
                    var $tr = $('<tr><td></td><td></td><td></td><td></td></tr>');

                    $tr.find('td:eq(0)').text(data.items[i].id);
                    $tr.find('td:eq(1)').append($('<a></a>').attr('href', '<?php echo Url::to(['pages/alertsevents/alertevent']);?>&id='+data.items[i].id).text(data.items[i].name)     );
                    $tr.find('td:eq(2)').text(data.items[i].alert_type);
                    $tr.find('td:eq(3)').html($('<a class="btn btn-info btn-sm" title="Редактировать" href="<?php echo Url::to(['pages/alertsevents/alertevent']);?>&id='+data.items[i].id+'"><i class="fa fa-pencil"></i></a>&nbsp;<a href="javascript:void(0);" class="btn btn-danger remove-btn btn-sm" data-id="'+data.items[i].id+'"><i class="fa fa-trash"></i></a>'));

                    $tout.append($tr);
                }


                // if empty
                if (!data.items.length) {
                    $('#tout').html('<tr><td class="text-muted text-center" colspan="4">Статьи ещё не были созданы</td></tr>');
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


        // delete btn parser
        $('#tout').on('click', '.remove-btn', function () {
            if (!confirm('Вы действительно хотите удалить уведомление события?\nЭто действие невозможно отменить.')) return false;

            $.ajax({
                url: '<?php echo Url::to(['pages/alertsevents/remove']);?>',
                'type': 'POST',
                data: {
                    'id': $(this).attr('data-id')
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
                    if (data != 'OK') alert('Произошла ошибка при удалении...');

                    loadPage(currentPage, currentOrder, currentAsc);
                }
            });

            return false;
        });


        $(function () {loadPage(currentPage, currentOrder, currentAsc);});
    })();
</script>