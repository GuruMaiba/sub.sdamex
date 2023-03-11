<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\assets\MainAsset;
use app\components\ExamType;
use app\models\exam\write\Reply;

MainAsset::register($this);
/* @var $this yii\web\View */
$this->title = "ЧАТ | SDAMEX";
$ava = Url::to("@uAvaSmall/no_img.jpg");
$now = time();
$isBegin = $webinar['start_at'] <= $now;
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="pageChat">
    <div class="bg" style="background-image: url('<?=Url::to("@webnAvaLarge/$webinar[ava]")?>')"> <div class="blackout"></div> </div>
    <div class="list">
        <?php if ($is['frame']) : ?>
            <a href="/webinars/chat/<?=$webinar['id']?>" class="fullscreen" target="_blank"><i class="icon-display"></i></a>
        <?php endif; ?>
        <?= $this->render('_messages', [
                'model' => $model,
                'isAuthor' => $is['author'],
                'isModer' => $is['moder'],
                // 'type' => 'comments',
            ]) ?>
    </div>
    <form class="form">
        <?php if ($is['author']) : ?>
        <div class="authorBlock">
            <?php if (!$isBegin) : ?>
            <div class="item timer active">
                <?php
                    $dif = $webinar['start_at']-$now;
                    $timer = [
                        'day' => intdiv(($dif), (24*60*60)),
                    ];
                    $timer['hours'] = intdiv(($dif%(24*60*60)),(60*60));
                    $timer['mins'] = intdiv((($dif%(24*60*60))%(60*60)),60);
                    $timer['secs'] = (($dif%(24*60*60))%(60*60))%60;
                    foreach ($timer as $key => $value) {
                        if ($value < 0)
                            $timer[$key] = 0;
                        if ($value < 10)
                            $timer[$key] = "0$timer[$key]";   
                    }

                ?>
                <ul>
                    <li id="day"><?=$timer['day']?></li>
                    <li class="point">:</li>
                    <li id="hour"><?=$timer['hours']?></li>
                    <li class="point">:</li>
                    <li id="min"><?=$timer['mins']?></li>
                    <li class="point">:</li>
                    <li id="sec"><?=$timer['secs']?></li>
                </ul>
            </div>
            <?php endif; ?>
            <div class="item start <?=($isBegin && !$webinar['start'])?'active':''?>">Начать вебинар</div>
            <div class="item end <?=($isBegin && $webinar['start'])?'active':''?>">Завершить вебинар</div>
        </div>
        <?php endif; ?>
        <div class="newMessage<?= ($is['ban'] || Yii::$app->user->isGuest) ? ' err' : '' ?>">
            <?php if (!Yii::$app->user->isGuest) : ?>
                <div class="error">Вы были заблокированы и больше не можете отправлять сообщения!</div>
                <img class="ava" src="<?=Url::to("@uAvaSmall/".Yii::$app->user->identity->ava)?>">
                <textarea name="message" placeholder="Введите ваше сообщение"></textarea>
                <div class="send"><i class="icon-email-plane"></i></div>
            <?php else : ?>
                <div class="error">Для отправки сообщений, Вам необходимо <a href="<?php Yii::$app->params['listSubs'][1]['link']?>account/login" target="_blank">авторизироваться</a> на сайте</div>
            <?php endif; ?>
        </div>
    </form>
</div><!-- end pageChat -->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<script type="text/javascript">
    let csrf = '<?=Yii::$app->getRequest()->getCsrfToken()?>';
    let webId = <?=$webinar['id']?>;
    let listener = null;
    let delay = 500;

    $(document).ready(function() {
        startListener();
        $(document).scrollTop($(document).height());
        setTimer(() => {
            $('.authorBlock .timer').removeClass('active');
            $('.authorBlock .start').addClass('active');
        });

        // отслеживания нажатия enter
        $('.form textarea').keydown(function (e) {
            if (e.which == 13 ) {
                e.preventDefault();
                $('.form .send').click();
            }
        });

        // добавление сообщения
        $('.form .send').click(function () {
            let th = $(this);
            let message = $('.form textarea').val();
            if (message == '' || th.hasClass('disable'))
                return false;

            th.addClass('disable');
            $('.form textarea').val('');
            $.post( '/webinars/add-message', {'_csrf': csrf, 'id': webId, 'message': message})
                .done(function( data, status, jqXHR ) {
                    if (data != 0) {
                        if (data != -1) {
                            $('.list').prepend(data);
                            $(document).scrollTop($(document).height());
                        } else
                            $('.form .newMessage').addClass('err');
                        
                    } else
                        globalError('Что-то пошло не так!');

                    th.removeClass('disable');
                })
                .fail(function( jqXHR, status, errorThrown ){
                    ajaxError(errorThrown, jqXHR);
                });
        });

        $('.pageChat .list').on('click', '.message .ava', function () {
            let th = $(this);
            let userId = th.attr('user');
            if (th.hasClass('disable') && th.hasClass('active'))
                return false;

            th.addClass('disable');
            $.post( '/webinars/add-like', {'_csrf': csrf, 'id': webId, 'user_id': userId})
                .done(function( data, status, jqXHR ) {
                    if (data != 0)
                        th.addClass('active');
                    else
                        globalError('Что-то пошло не так!');

                    th.removeClass('disable');
                })
                .fail(function( jqXHR, status, errorThrown ){
                    ajaxError(errorThrown, jqXHR);
                });
        });

        $('.pageChat .list').on('click', '.message .menu .exp', function () {
            let th = $(this);
            let menu = th.parents('.menu');
            let exp = th.attr('exp');
            let userId = menu.attr('user');
            if (menu.hasClass('disable'))
                return false;

            menu.addClass('disable');
            $.post( '/webinars/add-exp', {'_csrf': csrf, 'id': webId, 'user_id': userId, 'exp': exp})
                .done(function( data, status, jqXHR ) {
                    if (data != 0)
                        th.parent().remove();
                    else
                        globalError('Что-то пошло не так!');

                    menu.removeClass('disable');
                })
                .fail(function( jqXHR, status, errorThrown ){
                    ajaxError(errorThrown, jqXHR);
                });
        });

        $('.pageChat .list').on('click', '.message .menu .ban', function () {
            let th = $(this);
            let menu = th.parents('.menu');
            let userId = menu.attr('user');
            if (menu.hasClass('disable'))
                return false;

            menu.addClass('disable');
            $.post( '/webinars/add-ban', {'_csrf': csrf, 'id': webId, 'user_id': userId})
                .done(function( data, status, jqXHR ) {
                    if (data != 0) {
                        menu.remove();
                    } else
                        globalError('Что-то пошло не так!');

                    menu.removeClass('disable');
                })
                .fail(function( jqXHR, status, errorThrown ){
                    ajaxError(errorThrown, jqXHR);
                });
        });

        // Начало вебинара
        $('.form .authorBlock .start').click(function () {
            startEndWebinar($(this));
        });

        // Конец вебинара
        $('.form .authorBlock .end').click(function () {
            startEndWebinar($(this));
        });
    });

    // Создание слушателя
    function startListener() {
        listener = setTimeout(listenerNewMess, delay);
    }

    // Удаление слушателя
    function stopListener() {
        clearTimeout(listener);
        delay = 500;
    }

    // Функция слушателя новых сообщений
    function listenerNewMess() {
        let lastMess = $('.list .message:first');
        let id = lastMess.attr('numb');
            id = (id === undefined) ? 0 : id;

        $.post('/webinars/check-message', {'_csrf': csrf, 'id': webId, 'lastId': id})
            .done(function ( data, status, jqXHR ) {
                console.log(data);
                if (data != 0 && data != -1) {
                    $('.list').prepend(data);
                    $(document).scrollTop($(document).height());
                }
                startListener();
            }).fail(function( jqXHR, status, errorThrown ){
                if (delay < 3000)
                    delay *= 2;
                startListener();
            });
    }

    function startEndWebinar(btn) {
        if (btn.hasClass('disable'))
            return false;

        let type = (btn.hasClass('start')) ? 1 : 2;
        btn.addClass('disable');
        $.post( '/webinars/start-end', {'_csrf': csrf, 'id': webId, 'type': type})
            .done(function( data, status, jqXHR ) {
                console.log(data);
                if (data != 0) {
                    if (type == 1)
                        btn.siblings('.end').addClass('active');
                    btn.remove();
                } else {
                    globalError('Что-то пошло не так!');
                    btn.removeClass('disable');
                }
            })
            .fail(function( jqXHR, status, errorThrown ){
                ajaxError(errorThrown, jqXHR);
            });
    }
</script>
