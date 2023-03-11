<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\assets\MainAsset;
use app\components\ExamType;
use app\models\exam\write\Reply;

MainAsset::register($this);
/* @var $this yii\web\View */
$this->title = strip_tags($model['name']);
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
    <?php if (Yii::$app->params['subInx'] == 5) : ?> 
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3.0.1/es5/tex-mml-chtml.js"></script>
    <?php endif; ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= $this->render('@app/views/layouts/_preloader'); ?>

<div class="pageExam">
    <? if ($type == 'exercise') : ?>
    <div class="content container <?=($model['type'] == ExamType::WRITE && !$isPrem)?'checked':''?>">
        <div class="top">
            <a href="/exams" class="back"><i class="icon-angle-right"></i>Назад</a>
            <div class="logo">
                LOGO
            </div>
            <div class="title">
                <div class="course"> <?= $model['section']['fullexam']['name'] ?> </div>
                <hr class="defHr">
            </div>
        </div>
        <div class="exams">
            <div id="task" class="section training">
                <div class="nameSection"> <?= $model['section']['name'] ?> <div class="strik"></div> </div>
                <div class="task">
                    <?php $form = ActiveForm::begin([
                        'id' => 'exerciseForm',
                        // 'method' => 'post',
                        // 'action' => '/exams/check-exercise',
                        // 'options' => ['enctype' => 'multipart/form-data']
                        ]); ?>
                    <input type="hidden" name="Exercise[fullexam_id]" value="<?=$model['section']['fullexam']['id']?>">
                    <input type="hidden" name="Exercise[section_id]" value="<?=$model['section']['id']?>">
                    <input type="hidden" name="Exercise[id]" value="<?=$model['id']?>">
                    <input type="hidden" name="Exercise[type]" value="<?=$model['type']?>">
                    <div class="number"><?= $model['name'] ?></div>
                    <?php
                        // ЗАДАНИЕ
                        if ($model['type'] == ExamType::TEST) {
                            $task_id = $model['tests']['id'];
                            echo $this->render('_test', [
                                'model' => $model['tests'],
                                'parents' => 'Exercise[test]',
                            ]);
                        } else if ($model['type'] == ExamType::WRITE) {
                            $task_id = $model['writes']['id'];
                            echo $this->render('_write', [
                                'model' => $model['writes'],
                                'parents' => 'Exercise[write]',
                                'isPrem' => $isPrem,
                                'countChars' => Reply::COUNT_CHARS,
                            ]);
                        } else if ($model['type'] == ExamType::CORRELATE) {
                            $task_id = $model['correlates']['id'];
                            echo $this->render('_correlate', [
                                'model' => $model['correlates'],
                                'parents' => 'Exercise[correlate]',
                            ]);
                        } else if ($model['type'] == ExamType::ADDITION) {
                            $task_id = $model['additions']['id'];
                            echo $this->render('_addition', [
                                'model' => $model['additions'],
                                'parents' => 'Exercise[addition]',
                            ]);
                        }
                    ?>
                    <!-- <button type="submit">Отправить</button> -->
                    <?php ActiveForm::end(); ?>
                </div><!-- end task -->
            </div><!-- end section -->
        </div><!-- end exams -->
        <div class="control">
            <a href="<?=Url::to(['/exams/skip', 'exercise'=>$model['id'], 'task'=>$task_id])?>" class="btn skip">ПРОПУСТИТЬ</a>
            <div class="btn check">ОТВЕТИТЬ</div>
            <?php if ($model['type'] != ExamType::WRITE) : ?>
            <div class="result">
                <label class="item exp"><i>опыт</i>\<span class="num expNum">0</span></label>
                <label class="item points"><i>баллы</i>\<span class="num pointsNum">0</span></label>
            </div>
            <?php endif; ?>
            <a href="<?=Yii::$app->request->url?>" class="btn next">СЛЕДУЮЩЕЕ ЗАДАНИЕ</a>
        </div>
    </div><!-- end content -->
    <? else : ?>
    <div class="content container <?=($isResult)?'checked':''?>">
        <?php $form = ActiveForm::begin([ 'id' => 'fullexamForm', 'action' => '/exams/check-fullexam' ]); ?>
        <div class="top">
            <div class="logo">
                LOGO
            </div>
            <div class="title">
                <div class="course"><a href="/exams" class="back"><i class="icon-angle-right"></i>Назад</a> <?= $model['name'] ?></div>
                <hr class="defHr">
            </div>
            <!-- <div class="timer">
                <div class="icon"><i class="icon-clock"></i></div>
                <div class="counter">03:43:20</div>
            </div> -->
        </div>
        <div class="exams">
            <input type="hidden" name="Fullexam[id]" value="<?=$model['id']?>">
            <?php foreach ($model['sections'] as $section) :?>
            <div class="section training">
                <div class="nameSection"> <?=$section['name']?> <div class="strik"></div> </div>
                <?php foreach ($section['exercises'] as $exercise) : ?>
                <? $parents = 'Fullexam[sections]['.$section['id'].'][exercises]['.$exercise['id'].']'; ?>
                <input type="hidden" name="<?=$parents?>[type]" value="<?=$exercise['type']?>">
                <div class="task">
                    <div class="number"><?=$exercise['name']?></div>
                    <?php
                        $resExe = $resultModel['answers']['sections'][$section['id']]['exercises'][$exercise['id']];
                        $taskExp = 0;
                        $taskPoints = 0;
                        $taskMaxExp = 0;
                        // ЗАДАНИЕ
                        if ($exercise['type'] == ExamType::TEST) {
                            if ($isResult) {
                                $taskExp += $resExe['test']['exp'];
                                $taskPoints += $resExe['test']['points'];
                                $taskMaxExp += $exercise['tests'][0]['qst_exp'] * count($exercise['tests'][0]['questions']);
                            }
                            echo $this->render('_test', [
                                'model' => $exercise['tests'][0],
                                'parents' => $parents.'[test]',
                                'isResult' => $isResult,
                                'userAns' => json_decode($resExe['test']['answers'],true),
                            ]);
                        } else if ($exercise['type'] == ExamType::WRITE) {
                            if ($isResult) {
                                $taskExp += $resExe['write']['exp'];
                                $taskPoints += $resExe['write']['points'];
                                $taskMaxExp += $exercise['writes'][0]['exp'];
                            }
                            echo $this->render('_write', [
                                'model' => $exercise['writes'][0],
                                'parents' => $parents.'[write]',
                                'isPrem' => $isPrem,
                                'isResult' => $isResult,
                                'isCheck' => $resultModel['check'],
                                'subject' => Yii::$app->params['listSubs'][$model['subject_id']],
                                'result' => $resExe['write'],
                                'countChars' => Reply::COUNT_CHARS,
                            ]);
                        } else if ($exercise['type'] == ExamType::CORRELATE) {
                            if ($isResult) {
                                $taskExp += $resExe['correlate']['exp'];
                                $taskPoints += $resExe['correlate']['points'];
                                $taskMaxExp += $exercise['correlates'][0]['pair_exp'] * count($exercise['correlates'][0]['pairs']);
                            }
                            echo $this->render('_correlate', [
                                'model' => $exercise['correlates'][0],
                                'parents' => $parents.'[correlate]',
                                'isResult' => $isResult,
                                'userAns' => json_decode($resExe['correlate']['answers'],true),
                            ]);
                        } else if ($exercise['type'] == ExamType::ADDITION) {
                            if ($isResult) {
                                $taskExp += $resExe['addition']['exp'];
                                $taskPoints += $resExe['addition']['points'];
                                $taskMaxExp += $exercise['additions'][0]['word_exp'] * substr_count($exercise['additions'][0]['text'], '_(');
                            }
                            echo $this->render('_addition', [
                                'model' => $exercise['additions'][0],
                                'parents' => $parents.'[addition]',
                                'isResult' => $isResult,
                                'userAns' => $resExe['addition']['answers'],
                            ]);
                        }
                    ?>
                    <?php if ($isResult) : $notCheck = (!$resultModel['check'] && $exercise['type'] == ExamType::WRITE); ?>
                    <div class='taskResult <?=($notCheck)?'notCheck':''?>'>
                        <?php if ($notCheck) : ?>
                            <i>идёт проверка</i>
                        <?php else : ?>
                            <label class="item"><i>опыт</i>\<span class="num"><?=$taskExp?></span><span class="max"><?=$taskMaxExp?></span></label>
                            <label class="item"><i>баллы</i>\<span class="num"><?=$taskPoints?></span><span class="max"><?=$exercise['fullexam_points']?></span></label>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div><!-- end task -->
                <?php endforeach; ?>
            </div><!-- end section -->
            <?php endforeach; ?>
        </div><!-- end exams -->
        <div class="control full">
            <div class="result">
                <label class="item exp"><i>опыт</i>\<span class="num expNum"><?=($isResult)?$resultModel['answers']['user_exp']:0?></span><span class="max expMax"><?=($isResult)?$resultModel['answers']['max_exp']:$model['max_exp']?></span></label>
                <label class="item points"><i>баллы</i>\<span class="num pointsNum"><?=($isResult)?$resultModel['answers']['user_points']:0?></span><span class="max expMax"><?=($isResult)?$resultModel['answers']['max_points']:$model['max_points']?></span></label>
                <? if ($isResult && $resultModel['check']) : ?>
                <label class="item mark"><i>оценка</i>\<span class="num markNum"><?=$resultModel['answers']['mark']?></span></label>
                <? endif; ?>
            </div>
            <? if ($isResult && $resultModel['check']) : ?>
                <div class="endMessage"><?=$resultModel['teacher_comment']?></div>
                <a href="/personal/check" class="btn back">ЛИЧНЫЙ КАБИНЕТ</a>
            <? else : ?>
                <div class="endMessage">Конечные баллы и оценку, вы сможете узнать в <a href="/personal/check">личном кабинете</a> после проверки учителем практических заданий!</div>
                <a href="/exams" class="btn back">НА ГЛАВНУЮ</a>
            <? endif; ?>
            <div class="btn endExam">ЗАВЕРШИТЬ ЭКЗАМЕН</div>
        </div>
        <!-- <button type="submit">Отправить</button> -->
        <?php ActiveForm::end(); ?>
    </div><!-- end content -->
    <? endif; ?>
</div><!-- end pageExam -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<script type="text/javascript">
    let isResult = <?= ($isResult)?1:0 ?>;
    if (isResult) {
        $('input').attr({'disabled': 'disabled'});
        $('textarea').attr({'disabled': 'disabled'});
        $('.test .variants').each(function (i) {
            let qst = $(this).parents('.question');
            let err = false;

            $(this).children('.item').each(function (i) {
                if ( ($(this).hasClass('right') && !$(this).hasClass('active')) 
                || (!$(this).hasClass('right') && $(this).hasClass('active')) )
                    err = true;
            });

            if (err)
                qst.addClass('err');
            else
                qst.removeClass('err');
        });
    }

    $(document).ready(function() {

        $('#exerciseForm').keydown(function(event) {
            if(event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });

        $('#fullexamForm').keydown(function(event) {
            if(event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });

        // TEST
        // ----------------------------
        //
        $('.test .variants .item').click(function() {
            if ($('.content').hasClass('checked'))
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

        // CORRELATE
        // ----------------------------
        //
        $('.numbers .item').click(function () {
            if ($('.content').hasClass('checked'))
                return false;
            
            let parent = $(this).parent();
            let lett = $('.letters');
            let isFocus = $(this).hasClass('focus');
            if (parent.hasClass('focus')) {
                $('.numbers .item').removeClass('focus');
                if (isFocus) {
                    parent.removeClass('focus');
                    lett.removeClass('focus');
                } else
                    $(this).addClass('focus');
            } else {
                parent.addClass('focus');
                lett.addClass('focus');
                $(this).addClass('focus');
            }
        });

        $('.letters .item.qst').click(function () {
            let letts = $(this).parents('.letters')
            if (!letts.hasClass('focus'))
                return false;

            let qstId = $(this).attr('number');
            let focus = $('.numbers .item.focus');
            let focusId = focus.attr('number');
            let focusNum = focus.children('.check').text();
            let ans = $(this).siblings('.item.ans');

            if (qstId == focusId)
                $(this).removeClass('error');
            else
                $(this).addClass('error');
            if (ans.attr('number') > 0)
                ans.click();
            if (focusId > 0)
                $(".letters .item.ans").each(function (i) {
                    if ($(this).attr('number') == focusId)
                        $(this).click();
                });

            ans.attr('number', focusId);
            ans.children('.check').text(focusNum);
            ans.children('.txt').text(focus.children('.txt').text());
            $('#tbl-ans-'+qstId).text(focusNum);

            $(this).parent().addClass('active');
            $('.numbers').removeClass('focus');
            $('.letters').removeClass('focus');
            focus.removeClass('focus').addClass('active');

            let answers = JSON.parse(letts.siblings('.ansStr').val());
            answers[qstId] = focus.attr('number');
            letts.siblings('.ansStr').val(JSON.stringify(answers));
        });

        $('.letters .item.ans').click(function () {
            if ($('.content').hasClass('checked'))
                return false;

            let id = $(this).attr('number');
            $(this).attr('number', 0);

            let qstId = $(this).siblings('.item.qst').attr('number');
            $(this).parent().removeClass('active');
            $('#num-'+id).removeClass('active');
            $('#tbl-ans-'+qstId).text('');

            let input = $(this).parents('.letters').siblings('.ansStr');
            let answers = JSON.parse(input.val());
            answers[qstId] = 0;
            input.val(JSON.stringify(answers));
        });

        // ADDITION
        // ----------------------------
        //
        $('.addition .wordInput input').change(function () {
            let parent = $(this).parent();
            let val = $(this).val().toLowerCase().trim()/*.replace(/\s+/g, '')*/;
            let cor = $(this).attr('correct').toLowerCase().trim();

            parent.addClass('err');
            if (cor.indexOf('_') > -1) {
                let corArr = cor.split('_');
                if (corArr.length > 0) {
                    corArr.forEach(elm => {
                        if (elm.trim() == val)
                            parent.removeClass('err');
                    });
                }
            } else if (val == cor)
                parent.removeClass('err');
        });

        // WRITE
        // ----------------------------
        //
        $('.write textarea').keyup(function (e) {
            let count = $(this).val().length;
            $(this).siblings('.countChars').children('.have').text(count);
            if (count > <?=Reply::COUNT_CHARS?>) {
                $('.control .check').addClass('disable');
                $(this).siblings('.errMess').text('Превышено допустимое количество символов!');
            } else {
                $('.control .check').removeClass('disable');
                $(this).siblings('.errMess').text('');
            }
        });

        $('.write .files').change(function () {
            if ($(this).hasClass('disable'))
                return false;

            let fls = $(this)[0].files;
            let lg = fls.length;
            let err = $(this).parent().siblings('.errMess');
            $(this).siblings('.countFiles').children('.have').text(lg);

            if (lg > <?=Reply::COUNT_FILES?>) {
                $('.control .check').addClass('disable');
                err.text('Превышено допустимое количество файлов!');
                $(this).val('');
                return false;
            } else {
                $('.control .check').removeClass('disable');
                err.text('');
            }

            if (lg > 0) {
                let types = "<?=implode(',',Reply::FILE_TYPES)?>";
                    types = types.split(',');
                let isErr = false;

                for (let i = 0; i < lg; i++) {
                    if (fls[i].size > <?=Reply::FILE_SIZE?>) {
                        $('.control .check').addClass('disable');
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
                        $('.control .check').addClass('disable');
                        err.text('Присутствует недопустимое расширение файла!');
                        $(this).val('');
                        isErr = true;
                    }
                }

                if (!err) {
                    $('.control .check').removeClass('disable');
                    err.text('');
                }
           }
        });

        // CONTROL
        // ----------------------------
        //
        $('.control .check').click(function () {
            let th = $(this);
            if (th.hasClass('disable'))
                return false;

            th.addClass('disable');    
            let parent = $(this).parent();
            let data = new FormData($('#exerciseForm')[0]);

            $.ajax({
                url         : '/exams/check-exercise',
                type        : 'POST',
                data        : data,
                // cache       : false,
                // dataType    : 'json',
                // отключаем обработку передаваемых данных, пусть передаются как есть
                processData : false,
                // отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
                contentType : false,
                success     : function( req, status, jqXHR ){
                    console.log(req);
                    if (req['req'] == 1) {
                        $('.control .result .expNum').text(req['exp']);
                        $('.control .result .pointsNum').text(req['points']);
                        $('.content').addClass('checked');
                        $('.control .check').remove();
                        $('.control .skip').remove();
                        $('.write .download').remove();
                    } else {
                        globalError();
                    }
                    th.removeClass('disable');
                },
                error: (jqXHR, status, errorThrown) => {ajaxError(errorThrown, jqXHR);}
            });
        });

        $('.control .endExam').click(function () {
            let th = $(this);
            if (th.hasClass('disable'))
                return false;

            th.addClass('disable');    
            let parent = $(this).parent();
            let data = new FormData($('#fullexamForm')[0]);

            // $.post('/exams/check-fullexam', data)
            //     .done(function (req) {
            //         // console.log(req);
            //         if (req['req'] == 1) {
            //             $('.control .result .expNum').text(req['exp']);
            //             $('.control .result .pointsNum').text(req['points']);
            //             $('.content').addClass('checked');
            //             $('.control .endExam').remove();
            //             $('input').attr({'disabled': 'disabled'});
            //         } else {
            //             globalError();
            //             th.removeClass('disable');
            //         }
            //     }).fail(() => {globalError();});
            
            $.ajax({
                url         : '/exams/check-fullexam',
                type        : 'POST',
                data        : data,
                // отключаем обработку передаваемых данных, пусть передаются как есть
                processData : false,
                // отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
                contentType : false,
                success     : function( req, status, jqXHR ){
                    console.log(req);
                    if (req['req'] == 1) {
                        $('.control .result .expNum').text(req['exp']);
                        $('.control .result .pointsNum').text(req['points']);
                        $('.content').addClass('checked');
                        $('.control .endExam').remove();
                        $('.write .download').remove();
                        $('input').attr({'disabled': 'disabled'});
                    } else {
                        globalError();
                    }
                    th.removeClass('disable');
                },
                error: (jqXHR, status, errorThrown) => {ajaxError(errorThrown, jqXHR);}
            });
        });
    });
</script>

<!--
<div class="exams">
    <div class="section">
        <div class="nameSection"> Грамматика <div class="strik"></div> </div>
        <div class="task">
            <div class="number">Задание №2</div>
            <?php //$this->render('_test') ?>
        </div>
        <div class="task">
            <div class="number">Задание №2</div>
            <?php //$this->render('_test') ?>
        </div>
    </div>
    <div class="section">
        <div class="nameSection"> Грамматика <div class="strik"></div> </div>
        <div class="task">
            <div class="number">Задание №2</div>
            <?php //$this->render('_test') ?>
        </div>
        <div class="task">
            <div class="number">Задание №2</div>
            <?php //$this->render('_test') ?>
        </div>
    </div>
</div>
 -->
