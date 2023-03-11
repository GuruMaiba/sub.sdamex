<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\exam\{Fullexam, Exercise};

/* @var $this yii\web\View */

// $this->params['customImg'] = Url::to("@imgFolder/exams.jpg");
// $this->title = 'Проверочные задания';
$user = Yii::$app->user->identity;
$isGuest = Yii::$app->user->isGuest;
$stats = json_decode($user->statistics, true);
$countModel = count($model);
$active = $_COOKIE['activeFullexam'];
$chekActive = -1;
if ($active > 0 && $countModel > 0) {
    foreach ($model as $key => $full) {
        if ($full['id'] == $active) {
            $chekActive = $key;
        }
    }
}
?>
<div class="pageTop topExams">
    <div class="pageTitle">
        <h1 class="title">Проверочные задания</h1>
        <!-- <div class="bottom">
            <a class="defBtn">Оформить подписку</a>
        </div> -->
    </div>
</div>
<div class="pageExams">
<?php if ($countModel > 0) : ?>
    <?php //debug(json_decode(Yii::$app->user->identity->statistics, true)) ?>
    <?php // Добавляем class - notNull, если список не пуст ?>
    <div class="exams <?=($countModel > 1)?'notNull':''?>">
        <div class="prompt">
            <div class="text"> Выбери нужный экзамен! </div>
            <div class="arrow"></div>
        </div>
        <div class="mark">
            <span class="txt"><i class="icon-down-open"></i>ЭКЗАМЕН</span>
            <?php if ($countModel > 1) : ?>
                <div class="list">
                    <?php foreach ($model as $full) : ?>
                        <span class="item" number="<?=$full['id']?>"><?=$full['name']?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="exam"><?=($countModel == 1 || $chekActive == -1) ? $model[0]['name'] : $model[$chekActive]['name']?></div>
    </div>
    
    <?php

    $isEditAtt = false;
    foreach ($model as $i => $full) :
        $countExamsInFull = 0;
        $isFull = true;
        $isPrem = false;

        foreach ($accesses as $acc) {
            if ($acc['course_id'] == $full['course_id'] && $acc['end_at'] > time())
                $isPrem = true;
        }

        if ($isPrem) {
            $tops = [
                'exercise' => [
                    'good' => [
                        'last' => [],
                        'all' => [],
                    ],
                    'bad' => [
                        'last' => [],
                        'all' => [],
                    ],
                ],
                'theme' => [
                    'good' => [
                        'last' => [],
                        'all' => [],
                    ],
                    'bad' => [
                        'last' => [],
                        'all' => [],
                    ],
                ],
            ];
    
            foreach ((array)$themes as $theme) {
                $stat_theme = $stats[Yii::$app->params['subInx']]['exams']['list'][$full['id']]['themes'][$theme['id']];
                $nowItem = [
                    'id' => $theme['id'],
                    'percent' => $stat_theme['percent_last'],
                    'name' => $theme['name'],
                    'count' => $stat_theme['count_corr']+$stat_theme['count_err'],
                ];
                
                if ($stat_theme && ($stat_theme['count_corr'] > 0 || $stat_theme['count_err'] > 0)) {
                    if ($nowItem['percent'] >= 50)
                        $tops['theme']['good']['last'] = setTop($tops['theme']['good']['last'], $nowItem);
                    else
                        $tops['theme']['bad']['last'] = setTop($tops['theme']['bad']['last'], $nowItem);
            
                    $nowItem['percent'] = $stat_theme['percent_all'];
                    if ($nowItem['percent'] >= 50)
                        $tops['theme']['good']['all'] = setTop($tops['theme']['good']['all'], $nowItem);
                    else
                        $tops['theme']['bad']['all'] = setTop($tops['theme']['bad']['all'], $nowItem);
                }
            }
        }
        
        $att = $attemptsList[$full['id']];
        
        if ((array)$att != [] && $att['last_date'] && (int)($att['last_date']/(24*3600)) == (int)(time()/(24*3600))) {
            if ( !(!$isPrem && $att['number_attempts'] < Fullexam::MAX_TRY_FREE)
                && !($isPrem && $att['number_attempts'] < Fullexam::MAX_TRY_PREM) )
                $isFull = false;
        } else {
            $isEditAtt = true;
            $att['last_date'] = 0;
            $att['number_attempts'] = 0;
        }
        
        $attemptsList[$full['id']] = $att;
        if ($isEditAtt) {
            if ($isGuest) {
                setcookie("fullexam_list", json_encode($attemptsList), time()+365*24*3600, "/");
            } else {
                $stats[Yii::$app->params['subInx']]['exams']['list'] = $attemptsList;
                $user->statistics = json_encode($stats);
                $user->update();
            }
        }
    ?>
    
    <div id="fullexam_<?=$full['id']?>" class="fullexam <?=($countModel == 1 || ($chekActive != -1 && $i==$chekActive))?'active':''?>" number="<?=$full['id']?>">
        <div class="desc"><?=$full['desc']?></div>
        <div class="sectionStat">
            <div class="sections">
                <?php foreach ($full['sections'] as $section) :?>
                <div class="section">
                    <span class="name"><?=$section['name']?></span>
                    <div class="tasks">
                        <?php foreach ($section['exercises'] as $exercise) : ?>
                        <?
                        $countExamsInFull += $exercise['fullexam'];

                        if ($isPrem) {
                            $stat_exe = $stats[Yii::$app->params['subInx']]['exams']['list'][$full['id']]['sections'][$section['id']]['exercises'][$exercise['id']];
                            $isSolved = ($stat_exe && ($stat_exe['count_corr'] > 0 || $stat_exe['count_err'] > 0));

                            $class = '';
                            if ($isSolved) {
                                if ($stat_exe['percent_last'] == 100)
                                    $class = 'done';
                                else if ($stat_exe['percent_last'] >= 80)
                                    $class = 'good';
                                else if ($stat_exe['percent_last'] >= 50)
                                    $class = 'something';
                            } else
                                $class = 'none';
                            
                            $nowItem = [
                                'id' => $exercise['id'],
                                'percent' => $stat_exe['percent_last'],
                                'name' => $exercise['name'],
                                'count' => $stat_exe['count_corr']+$stat_exe['count_err'],
                            ];

                            if ($isSolved) {
                                if ($nowItem['percent'] >= 50)
                                    $tops['exercise']['good']['last'] = setTop($tops['exercise']['good']['last'], $nowItem);
                                else
                                    $tops['exercise']['bad']['last'] = setTop($tops['exercise']['bad']['last'], $nowItem);

                                $nowItem['percent'] = $stat_exe['percent_all'];
                                if ($nowItem['percent'] >= 50)
                                    $tops['exercise']['good']['all'] = setTop($tops['exercise']['good']['all'], $nowItem);
                                else
                                    $tops['exercise']['bad']['all'] = setTop($tops['exercise']['bad']['all'], $nowItem);
                            }
                        }
                        ?>
                        <a href="/exam/<?=$full['id']?>?exercise=<?=$exercise['id']?>" class="task"><?=$exercise['name']?>
                        <? //if ($isPrem) : ?>
                        <span class="percent <?=$class?>"><?=($isPrem && $isSolved)?$stat_exe['percent_last'].'%':'-'?></span>
                        <? //endif; ?>
                        </a><br>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($isPrem) : ?>
            <div class="statistics">
                <div class="statTable good last">
                    <div class="head">
                        <div class="topName good">ТОП УСПЕШНЫХ ЗАДАНИЙ</div>
                        <div class="topName bad">ТОП НЕУДАЧНЫХ ЗАДАНИЙ</div>
                        <div class="range last">последние <?=Exercise::STAT_MAX_EXE?> выполненных</div>
                        <div class="range all">за всё время</div>
                    </div>
                    <?php foreach ($tops['exercise'] as $name => $top) : ?>
                    <div class="list <?=$name?>">
                        <?php foreach ($top as $type => $list) : ?>
                            <?php if ($list != []) : ?>
                            <?php foreach ($list as $item) : ?>
                                <div class="item <?=$type?>">
                                    <span class="percent"><?=$item['percent']?>%</span>
                                    <span class="name"><?=$item['name']?></span>
                                </div>
                            <?php endforeach; ?>
                            <?php else : ?>
                                <div class="item <?=$type?>"><span class="percent"></span><span class="name">-</span></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="statTable good last">
                    <div class="head">
                        <div class="topName good">ТОП УСПЕШНЫХ ТЕМ</div>
                        <div class="topName bad">ТОП НЕУДАЧНЫХ ТЕМ</div>
                        <div class="range last">последние <?=Exercise::STAT_MAX_EXE?> выполненных</div>
                        <div class="range all">за всё время</div>
                    </div>
                    <?php foreach ($tops['theme'] as $name => $top) : ?>
                    <div class="list <?=$name?>">
                        <?php foreach ($top as $type => $list) : ?>
                            <?php if ($list != []) : ?>
                            <?php foreach ($list as $item) : ?>
                                <div class="item <?=$type?>">
                                    <span class="percent"><?=$item['percent']?>%</span>
                                    <span class="name"><?=$item['name']?></span>
                                </div>
                            <?php endforeach; ?>
                            <?php else : ?>
                                <div class="item <?=$type?>"><span class="percent"></span><span class="name">-</span></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($countExamsInFull > 0) : ?>
        <div class="fullexamBtn">
            <?php if ($isFull) : ?>
                <a href="/exam/<?=$full['id']?>" class="start">Тренировать полный экзамен</a>
            <?php else : ?>
                <span class="fullDisable">Вы выполнили максимально допустимое количество полных экзаменов на сегодня.</span>
            <?php endif; ?>
        </div>            
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
<?php else : ?>
    <div class="default">Упражнения в скором времени будут добавлены!</div>
<?php endif; ?>
</div>

<?php

$js = <<<JS
    $('.exams .mark .txt').click(function() {
        let parent = $(this).parents('.exams');
        if (!parent.hasClass('active')) {
            if (parent.hasClass('notNull')) {
                parent.addClass('active');
                $('.exams .mark .list').slideDown(300);
            }
        } else {
            $('.exams .mark .list').slideUp(300);
            parent.removeClass('active');
        }
    });

    $('.exams .mark .list .item').click(function() {
        let parent = $(this).parents('.exams');
        let id = $(this).attr('number');
        $('.exams .exam').text($(this).text());
        $('.exams .mark .list .item').removeClass('active');
        $(this).addClass('active');
        $('.fullexam').removeClass('active');
        $('#fullexam_'+id).addClass('active');
        $('.exams .mark .list').slideUp(300);
        parent.removeClass('active');
        if (!$.cookie('activeFullexam') || $.cookie('activeFullexam') != id)
            $.cookie('activeFullexam', id, { expires : 365, path: '/'});
    });

    $('.statTable .topName').click(function () {
        $(this).parents('.statTable').toggleClass('good bad');
    });

    $('.statTable .range').click(function () {
        $(this).parents('.statTable').toggleClass('last all');
    });
JS;

function setTop($top, $nowItem) {
    $temp = [];
    $addItem = false;
    foreach ($top as $i => $item) {
        if ($i < 3) {
            if ($temp != []) {
                if ($item['percent'] < $temp['percent']) {
                    $t = $temp;
                    $temp = $item;
                    $top[$i] = $t;
                    $addItem = true;
                }
            } else if ($item['percent'] < $nowItem['percent']) {
                $temp = $item;
                $top[$i] = $nowItem;
                $addItem = true;
            }
        } else
            return false;
    }
    if (count($top) < 3)
        $top[] = ($addItem) ? $temp : $nowItem;

    return $top;
}

$this->registerJs($js);
 ?>
