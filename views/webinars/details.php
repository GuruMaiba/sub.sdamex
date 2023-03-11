<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
// $this->title = $model['title'];
$this->params['customImg'] = Url::to("@webnAvaLarge/$model[ava]");
$this->registerCssFile( "/css/jquery.mCustomScrollbar.min.css", ['rel'=>'stylesheet'], 'srollbarCSS' );

$countCourse = count($model['courses']);
$lastCKey = array_key_last($model['courses']);
?>

<div class="pageTop topWebinar">
<?php // DEBUG: Сделать мобилку для всех вариантов блока ?>
<?php if ($model['video_link'] && $model['end'] && $is['member']) : ?>
    <div class="banner video end">
        <div class="past">
            <iframe src="<?=$model['video_link']?>?iv_load_policy=3&rel=0" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
<?php elseif ($model['live_link'] && ($model['start'] || $is['author']) && !$model['end'] && $is['member']) : ?>
    <div class="banner video start">
        <div class="live">
            <iframe src="<?=$model['live_link']?>?autoplay=1&iv_load_policy=3&rel=0" allow="autoplay;" frameborder="0" allowfullscreen></iframe>
        </div>
        <div class='chat'>
            <iframe src="<?=Url::to(["/webinars/chat/$model[id]", 'frame'=>1])?>" frameborder="0"></iframe>
        </div>
    </div>
<?php else : ?>
    <div class="pageTitle">
        <h2 class="title"><?=$model['title']?></h1>

        <?php if (!$model['start']) : ?>
        <div class="timer">
            <div class="date"><?=$model['strDate']?></div>
            <?php
                $dif = $model['start_at']-time();
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
        <?php elseif ($is['member'] && $model['end']) : ?>
        <div class="comingSoon">Запись вебинара появится в скором времени!</div>
        <?php elseif (!$is['member']) : ?>
        <div class="comingSoon">Для доступа к вебинару, станьте его участником!</div>
        <?php endif; ?>

        <div class="bottom">
            <?php if (!empty($model['courses'])) : ?>
                <div class="remark">
                    Входит в подписку <?=($countCourse > 1)?'курсов':'курса'?>:<br>
                    <?php foreach ($model['courses'] as $ckey => $course) : ?>
                        <a href="/course/<?=$course['id']?>" class="main"><?=$course['title']?><?=($ckey == $lastCKey)?'':','?></a>
                    <?php endforeach; ?>
                </div>
                <!-- <div class="mesCourses">
                    <span>Входит в подписку курсов:</span><br>
                    <?php foreach ($model['courses'] as $ckey => $course) : ?>
                        <a href="/course/<?=$course['id']?>"><?=$course['title']?></a><?=($ckey == $lastCKey)?'':','?>
                    <?php endforeach; ?>
                </div> -->
            <?php elseif (!$is['member'] && $model['cost'] > 0) : ?>
                <div class="cost">
                    <div class="currency"><span class="rub">руб</span> </div>
                    <div class="numb"><?=$model['cost']?></div>
                </div>
                <a href="<?=Yii::$app->params['listSubs'][1]['link']."pay/webinar/$model[id]"?>" class="btn subscribe">Приобрести вебинар</a>
            <?php else : ?>
                <div class="countUsers"><i class="icon icon-users"></i> <span class="number"><?=$countMembers?></span></div>
                <?php if (!$is['author']) : ?>
                    <?php if (!$is['member'] && $is['guest']) : ?>
                        <a href="<?=Yii::$app->params['listSubs'][1]['link']?>account/login" class="btn">Принять участие</a>
                    <?php elseif ($is['prem'] || $model['cost'] < 1) : ?>
                        <a href="/webinars" class="btn">Все вебинары</a>
                    <?php else : ?>
                        <div class="btn participant <?=($is['member'])?'':'add'?>"><?=($is['member'])?'Отказаться от участия':'Принять участие'?></div>
                    <?php endif; ?>
                <?php else : ?>
                    <a href="<?=Yii::$app->params['listSubs'][1]['link']?>closedoor/webinar/details/<?=$model['id']?>" class="btn">
                        <?=(!$model['start'])?'Добавить трансляцию':'Редактировать'?>    
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
</div>

<div class="webinar">
    <!-- <div class="stats">
        <span class="countUsers"><i class="icon icon-users"></i> 20.000</span>
        <span class="countReviews"><i class="icon icon-comment-2"></i> 20.000</span>
        <span class="countReviews"><i class="icon icon-star"></i> 5.0</span>
    </div> -->
    <a href="<?=Url::to(['personal/profile', 'id'=>$model['author']['id']])?>" class="mainInfo author">
        <img class="ava" src="<?=Url::to("@uAvaSmall/".$model['author']['ava'])?>">
        <div class="appeal">
            <span class="nick"><?=$model['author']['username']?></span><br>
            <span class="name"><?=$model['author']['surname']. ' ' .$model['author']['name']?></span>
        </div>
    </a>


    <h1 class="mainTitle"><?=$model['title']?></h1>
    <?php if ($countCourse > 0) : ?>
    <div class="courses">
        <span><?=($countCourse > 1)?'Курсы':'Курс'?>:</span>
        <?php foreach ($model['courses'] as $ckey => $course) : ?>
            <a href="/course/<?=$course['id']?>"><?=$course['title']?></a><?=($ckey == $lastCKey)?'':','?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="desc"><?=$model['desc']?></div>
    <div class="links">
        <?php foreach (json_decode($model['links'], true) as $i => $val) : ?>
            <a href="<?=$val['link']?>"><?=$val['name']?></a>
        <?php endforeach; ?>
    </div>
    <div class="comments">
        <h3 class="comTitle">КОММЕНТАРИИ <span class="count"><?=$countComments?></span></h3>
        <?php if (Yii::$app->user->isGuest) : ?>
            <div class="banComments">Чтобы оставлять комментарии, необходимо <a href="<?=Yii::$app->params['listSubs'][1]['link']?>account/login?sub=<?=Yii::$app->params['subInx']?>&p=signin">авторизоваться</a> на сайте!</div>
        <?php elseif (!$is['member']) : ?>
            <div class="banComments">Чтобы оставлять комментарии, станьте участником вебинара!</div>
        <?php else : ?>
            <form class="form">
                <div class="item new">
                    <div class="info">
                        <a href="<?=Url::to(['profile', 'id'=>Yii::$app->user->identity->id])?>" class="author">
                            <img src="<?=Url::to("@uAvaSmall/".Yii::$app->user->identity->ava)?>">
                            <span class="nick"><?=Yii::$app->user->identity->username?></span>
                        </a>
                    </div>
                    <div class="text">
                        <textarea name="comment" placeholder="Напишите, что вы думаете об этом вебинаре..."></textarea>
                        <div class="send"><i class="icon-android-send"></i></div>
                        <div class="error"></div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
        <div class="list">
            <?= $this->render('_comments', [
                'model' => $model['commentslim'],
                // 'type' => 'comments',
            ]) ?>
        </div>
    </div>
</div>

<?php
$csrf = Yii::$app->getRequest()->getCsrfToken();
$status = ($model['start']) ? ((!$model['end']) ? 1 : 2) : -1;
$js = <<<JS
    let csrf = '$csrf';
    let webId = $model[id];
    let delay = 10000;
    let status = $status;

    $( document ).ready(function() {
        setTimer();
        startListener();

        // участие в вебинаре
        $('.participant').click(function () {
            let th = $(this);
            if (th.hasClass('disable'))
                return false;

            th.addClass('disable');

            let type = 'del';
            if (th.hasClass('add'))
                type = 'add';

            $.post( '/webinars/member', {'_csrf': csrf, 'type': type, 'id': webId})
                .done(function( data, status, jqXHR ) {

                    if (data == 1) {
                        location.reload();
                        // let count = $('.countUsers .number');
                        // if (type == 'add') {
                        //     th.removeClass('add');
                        //     count.text(parseInt(count.text()) + 1);
                        //     th.text('Отказаться от участия');
                        // } else {
                        //     th.addClass('add');
                        //     count.text(parseInt(count.text()) - 1);
                        //     th.text('Принять участие');
                        // }
                    } else
                        globalError('Что-то пошло не так!');

                    th.removeClass('disable');
                })
                .fail(function( jqXHR, status, errorThrown ){
                    ajaxError(errorThrown, jqXHR);
                });
        });

        // отслеживания нажатия
        $('.comments .form textarea').keydown(function (e) {
            if (e.which == 13) { // e.ctrlKey && 
                e.preventDefault();
                $('.comments .form .send').click();
            }
        });

        // добавление комментария
        $('.comments .form .send').click(function () {
            let th = $(this);
            if (th.hasClass('disable'))
                return false;

            let comment = $('.comments .form textarea').val();
            if (comment == '')
                return false;

            th.addClass('disable');
            $.post( '/webinars/add-comment', {'_csrf': csrf, 'id': webId, 'comment': comment})
                .done(function( data, status, jqXHR ) {
                    if (data != 0) {
                        $('.comments .form textarea').val('');
                        $('.comments .list').prepend(data);
                        ++addNewComment;
                    } else
                        globalError('Что-то пошло не так!');

                    th.removeClass('disable');
                })
                .fail(function( jqXHR, status, errorThrown ){
                    ajaxError(errorThrown, jqXHR);
                });
        });

        // кнопка редактирования
        $('.comments .list').on('click', '.edit', function () {
            let th = $(this);
            let item = th.parents('.item');
            if (item.hasClass('edit'))
                return false;

            let txtBlock = item.children('.text');
            let txt = txtBlock.text();
            item.addClass('edit');
            txtBlock.html($('.comments .form .text').html());
            txtBlock.children('textarea').text(txt);
        });

        // редактирование комментария
        $('.comments .list').on('change', 'textarea', function () {
            let th = $(this);
            let item = th.parents('.item');
            let txtBlock = th.parents('.text');
            let txt = th.val();

            if (txt != '') {
                item.children('.text').html(txt);
                item.removeClass('edit');
                $.post( '/webinars/edit-comment', {'_csrf': csrf, 'id': item.attr('numb'), 'comment': txt})
                    .done(function( data, status, jqXHR ) {
                        if (data == 0)
                            globalError('Комментарий не сохранён, что-то пошло не так!');
                    })
                    .fail(function( jqXHR, status, errorThrown ){
                        ajaxError(errorThrown, jqXHR, 'Комментарий не сохранён, что-то пошло не так!');
                    });
            }
        });

        // удаление комментария
        $('.comments .list').on('click', '.del', function () {
            let th = $(this);
            if (th.hasClass('disable') || !confirm('Вы уверены, что хотите удалить этот комментарий?'))
                return false;

            let item = th.parents('.item');
            th.addClass('disable');
            $.post( '/webinars/del-comment', {'_csrf': csrf, 'id': item.attr('numb')})
                .done(function( data, status, jqXHR ) {
                    if (data != 0) {
                        item.remove();
                    } else
                        globalError('Что-то пошло не так!');

                    th.removeClass('disable');
                })
                .fail(function( jqXHR, status, errorThrown ){
                    ajaxError(errorThrown, jqXHR);
                });
        });

        $( window ).scroll(function() {
            let scroll = $(this).scrollTop();
            let h = $(document).height() - $(window).height() - $('.footer').height() - 300;
            let list = $('.comments .list');

            if (scroll > h && !list.hasClass('download') && !list.hasClass('end')) {
                let countCom = list.children().length;
                list.addClass('download');
                $.post( '/webinars/get-comments', {'_csrf': csrf, 'id': webId, 'count': countCom})
                    .done(function( data, status, jqXHR ) {
                        if (data == 1) {
                            list.addClass('end');
                        } else if (data == 0) {
                            console.log('Ошибка в атрибутах!');
                        } else {
                            $('.comments .list').append(data);
                            list.removeClass('download');
                        }
                    })
                    .fail(function( jqXHR, status, errorThrown ){
                        ajaxError(errorThrown, jqXHR);
                    });
            }
        });
    });

    // Создание слушателя
    function startListener() {
        listener = setTimeout(listenerStatusWebinar, delay);
    }

    // Удаление слушателя
    function stopListener() {
        clearTimeout(listener);
        delay = 10000;
    }

    // Функция слушателя новых сообщений
    function listenerStatusWebinar() {
        if (status != 2) {
            $.post('/webinars/check-status', {'_csrf': csrf, 'id': webId})
                .done(function ( data, ajaxStatus, jqXHR ) {
                    if (data != 0) {
                        if (data != status)
                            location.reload();
                        startListener();
                    }
                }).fail(function( jqXHR, status, errorThrown ){
                    startListener();
                });
        }
    }
JS;
$this->registerJs($js);
$this->registerJsFile( '@scrLibs/jquery.mCustomScrollbar.concat.min.js', ['depends' => ['app\assets\PersonalAsset'],], 'scrollbarJS' );

?>
