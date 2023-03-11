<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\exam\write\Reply;

/* @var $this yii\web\View */
$this->title = $model['title'];
$this->params['customImg'] = Url::to("@crsAvaModule/$model[ava]");

?>
<div class="pageTop topModule">
    <div class="pageTitle">
        <h1 class="title"><?= Html::encode($this->title) ?></h1>
        <div class="bottom">
            <?php if ($isCreator) : ?>
                <div class="remark">Модуль<br><a href="<?=Yii::$app->params['listSubs'][1]['link']."closedoor/course/module/$model[id]?course_id=$model[course_id]"?>" class="main">Редактировать</a></div>
            <?php elseif ($isPrem) : ?>
                <div class="remark">Доступ до<br><span class="main"><?=ruMonth(date('d F Y', $access['end_at']))?></span></div>
            <?php else : ?>
                <div class="cost">
                    <div class="currency"><span class="rub">руб</span><span class="month">/мес</span></div>
                    <div class="numb"><?=$model['course']['cost']?></div>
                </div>
                <a href="<?=Yii::$app->params['listSubs'][1]['link']."pay/course/".$model['course']['id']?>" class="btn subscribe">Подписаться</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="pageModule">

    <a href="/course/<?=$model['course_id']?>#wave" class="back"><i class="icon-android-arrow-forward"></i> Курс | <?=$model['course']['title']?></a>
    <h1 class="mainTitle"><span class="name">Модуль /</span> <?= $model['title'] ?></h1>
    <div class="desc"><?= $model['desc'] ?></div>

    <div id="lessons" class="lessons">
        <h2 class="listName">Уроки</h2>
        <?php
            if ($model['lessons'] != []) :
                $prevEnd = 1;
                $nextAcc = 0;
        ?>
            <ul class="numbers">
                <?php
                    foreach ($model['lessons'] as $lKey => $less) :
                    $thisAcc = ($model['course']['free'] || $model['free'] || $less['free']) ? 1 : 0;
                    if ($thisAcc && $less['id'] == $lesson['id'])
                        $nextAcc = ($model['course']['free'] || $model['free'] || $model['lessons'][$lKey+1]['free']) ? 1 : 0;
                ?>
                    <li class="<?=($less['id'] == $lesson['id'])?'active':((!$prevEnd || !$thisAcc)?'disable':'')?>" numb="<?=$less['id']?>"><?=$lKey+1?></li>
                <?php
                    if ($less['id'] == $lesson['id'] && !$prevEnd) {$lesson = null;}
                    if (!empty($stats) && !$stats['lessons'][$less['id']]['end']) {$prevEnd = 0;}
                    endforeach; 
                ?>
            </ul>
            <div class="lesson">
                <?= $this->render('_lesson', [
                    'model' => $lesson,
                    'isPrem' => $isPrem,
                    'stats' => $stats['lessons'][$lesson['id']],
                    'nextAcc' => ($isPrem || $nextAcc),
                    'course' => $model['course'],
                ]) ?>
            </div>
        <?php else : ?>
            <div class="defMess">Уроки в процессе разработки, в скором времени они появятся на сайте!</div>
        <?php endif; ?>
    </div>
</div>

<?php

$csrf = Yii::$app->getRequest()->getCsrfToken();
$crsId = $model['course']['id'];
$countChars = Reply::COUNT_CHARS;

// Файлы
$cntFls = Reply::COUNT_FILES;
$flTps = implode(',',Reply::FILE_TYPES);
$flSize = Reply::FILE_SIZE;

$js = <<<JS
    let csrf = '$csrf';

    $(document).ready(function () {

        $('.numbers li').click(function () {
            let th = $(this);
            if (th.hasClass('disable') || th.hasClass('active'))
                return false;

            th.addClass('disable');
            let id = th.attr('numb');
            getLesson(id, th);
            th.removeClass('disable');
        });

        $('.lesson').on('click', '.nextLes', function () {
            let th = $(this);
            if (th.hasClass('disable') || th.hasClass('hide'))
                return false;

            th.addClass('disable');
            let next = $('.numbers li.active').next();
            let id = next.attr('numb');
            if (id === undefined) {
                window.location.href = "/course/$crsId#modules";
                return false;
            }

            getLesson(id, next);
            th.removeClass('disable');
        });

        $('.lesson').on('click', '.exams .link', function () {
            let th = $(this);
            if (th.hasClass('disable') || th.hasClass('check'))
                return false;

            let tBlock = $('.testing');
            let type = th.attr('type');

            if ((tBlock.hasClass('testCont') && type == 'test') || (tBlock.hasClass('writeCont') && type == 'write')) {
                tBlock.slideUp(300);
                setTimeout(() => {
                    tBlock.html('');
                    tBlock.removeClass('writeCont testCont');
                    if (!$('.nextLes').hasClass('disable'))
                        $('.nextLes').removeClass('hide');
                }, 300);
                return false;
            }

            tBlock.removeClass('checked');
            th.addClass('disable');
            $('.nextLes').addClass('hide');
            $.post( '/courses/get-test-write', {'_csrf': csrf, 'id': th.attr('numb'), 'type': type})
                .done(function( data, status, jqXHR ) {
                    if (data != 0 && data != 2) {
                        let html = tBlock.html();
                        let timeout = 0;

                        if (html != '') {
                            timeout = 300;
                            tBlock.slideUp(timeout);
                        }

                        setTimeout(() => {
                            if (type == 'test')
                                tBlock.removeClass('writeCont').addClass('testCont');
                            else if (type == 'write')
                                tBlock.removeClass('testCont').addClass('writeCont');
                                
                            tBlock.html(data);
                            tBlock.slideDown(500);
                        }, timeout);
                    } else if (data == 2) {
                        globalError('Вы недавно выполнили задание. После проверки учителем, Вы сможете повторить попытку.');
                    } else
                        globalError('Что-то пошло не так!');
                })
                .fail(function( jqXHR, status, errorThrown ){
                    ajaxError(errorThrown, jqXHR);
                });
            th.removeClass('disable');
        });

        // TEST
        // ----------------------------
        $('.lesson').on('click', '.testing .test .variants .item', function () {
            if ($('.testing').hasClass('checked'))
                return false;
            
            let qst = $(this).parents('.question');
            let listAns = $(this).parent();
            let label = $(this).siblings('.answer');
            let answersStr = qst.siblings('.ansStr');
            let answers = answersStr.val();
                answers = (answers != '' || answers != undefined) ? JSON.parse(answers) : answers;

            $(this).toggleClass('active');
            if ($(this).hasClass('active') && !qst.hasClass('multi'))
                $(this).siblings('.item').removeClass('active');

            let labelPosition = '';
            let err = false;
            answers[qst.attr('dbid')] = [];
            listAns.children('.item').each(function (i) {
                let num = $(this).children('.check').text();
                if ($(this).hasClass('active')) {
                    answers[qst.attr('dbid')].push(parseInt($(this).attr('dbid')));
                    labelPosition += (labelPosition == '') ? num : ','+num;
                }
                if ( ($(this).hasClass('right') && !$(this).hasClass('active')) 
                    || (!$(this).hasClass('right') && $(this).hasClass('active')) )
                    err = true;
            });
            answersStr.val(JSON.stringify(answers));

            if (err)
                qst.addClass('err');
            else
                qst.removeClass('err');

            if (labelPosition != '') {
                label.find('.position').text(labelPosition);
                label.removeClass('hidden');
            } else {
                label.addClass('hidden')
            }
        });

        // WRITE
        // ----------------------------
        $('.lesson').on('keyup', '.testing .write textarea', function (e) {
            let count = $(this).val().length;
            $(this).siblings('.countChars').children('.have').text(count);
            if (count > $countChars) {
                $('.lesson .testing .send').addClass('disable');
                $(this).siblings('.errMess').text('Превышено допустимое количество символов!');
            } else {
                $('.lesson .testing .send').removeClass('disable');
                $(this).siblings('.errMess').text('');
            }
        });

        $('.lesson').on('change', '.testing .write .files', function () {
            if ($(this).hasClass('disable'))
                return false;

            let fls = $(this)[0].files;
            let lg = fls.length;
            let err = $(this).parent().siblings('.errMess');
            let btn = $('.lesson .testing .send');
            $(this).siblings('.countFiles').children('.have').text(lg);

            if (lg > $cntFls) {
                btn.addClass('disable');
                err.text('Превышено допустимое количество файлов!');
                $(this).val('');
                return false;
            } else {
                btn.removeClass('disable');
                err.text('');
            }

            if (lg > 0) {
                let types = "$flTps";
                    types = types.split(',');
                    console.log(types);
                let isErr = false;

                for (let i = 0; i < lg; i++) {
                    if (fls[i].size > $flSize) {
                        btn.addClass('disable');
                        err.text('Превышен допустимый размер файлов!');
                        $(this).val('');
                        isErr = true;
                    }

                    let isType = false;
                    let lgFls = fls[i].name.length;
                    for (let j = 0; j < types.length; j++) {
                        if (fls[i].name.indexOf('.'+types[j], lgFls - types[j].length - 2) > -1)
                            isType = true;
                    }

                    if (!isType) {
                        btn.addClass('disable');
                        err.text('Присутствует недопустимое расширение файла!');
                        $(this).val('');
                        isErr = true;
                    }
                }

                if (!err) {
                    btn.removeClass('disable');
                    err.text('');
                }
           }
        });

        // SEND
        $('.lesson').on('click', '.testing .send', function () {
            let th = $(this);
            if (th.hasClass('disable'))
                return false;

            let tBlock = $('.testing');
            let type = '';
            if (tBlock.hasClass('testCont'))
                type = 'test';
            else if (tBlock.hasClass('writeCont'))
                type = 'write';

            if (type == '' || (type == 'write' && $('.testing .write textarea').val() == ''))
                return false;

            th.addClass('disable');
            let data = new FormData($('#formTesting')[0]);
                data.append('_csrf', csrf);
                data.append('type', type);
            // let data = $('#formTesting').serialize();
            //     data += '&_csrf='+csrf;
            //     data += '&type='+type;

            $.ajax({
                url         : '/courses/check-exercise',
                type        : 'POST',
                data        : data,
                // отключаем обработку передаваемых данных, пусть передаются как есть
                processData : false,
                // отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
                contentType : false,
                success     : function( data, status, jqXHR ){
                    console.log(data);
                    if (data.req != 0) {
                        tBlock.slideUp(300);
                        setTimeout(() => {
                            tBlock.html('');
                            tBlock.removeClass('writeCont testCont');

                            if (type == 'test') {
                                $('.exam.test').addClass('completed');
                                let points = parseInt($('.exam.test .points .myPoints').text());
                                
                                if (data.points > points) {
                                    let deg = data.percent/100 * 360;
                                    let firstAnm = (deg > 180)?180:deg;

                                    $('.exam.test .points .myPoints').text(data.points);
                                    $('.exam.test .progress .leftActive').css({'transform': 'rotate('+firstAnm+'deg)'});
                                    $('.exam.test .progress .rightActive').css({'transform': 'rotate('+firstAnm+'deg)'});

                                    if (deg > 180) {
                                        setTimeout(() => {
                                            $('.exam.test .progress .visiblePath').addClass('full');
                                            $('.exam.test .progress .rightActive').css({'transform': 'rotate('+deg+'deg)'});
                                            if (deg == 360) {
                                                $('.exam.test .progress').addClass('done');
                                                $('.exam.test .points').addClass('done');
                                            }
                                        }, 120);
                                    }
                                }
                            } else 
                                $('.exam.write .link').addClass('check');

                            if (( data.percent >= 75 || !$('.nextLes').hasClass('disable') )
                                && !$('.nextLes').hasClass('notStudent'))
                                $('.nextLes').removeClass('hide disable');

                            $('.includSub').slideDown(300);
                        }, 300);
                    } else
                        globalError('Что-то пошло не так!');
                },
                error: (jqXHR, status, errorThrown) => {ajaxError(errorThrown, jqXHR);}
            });

            th.removeClass('disable');
        });
    });

    function getLesson(id, next) {
        $.post( '/courses/lesson', {'_csrf': csrf, 'id': id})
            .done(function( data, status, jqXHR ) {
                if (data != 0) {
                    $('.numbers li').removeClass('active');
                    next.addClass('active').removeClass('disable');
                    $('.lesson').html(data);
                } else
                    globalError('Что-то пошло не так!');
            })
            .fail(function( jqXHR, status, errorThrown ){
                ajaxError(errorThrown, jqXHR);
            });
    }
JS;
$this->registerJs($js);
?>
