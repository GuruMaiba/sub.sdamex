<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = $model['title'];
$this->params['customImg'] = Url::to("@crsAvaLarge/$model[ava]");
$stats = ($isPrem) ? json_decode(Yii::$app->user->identity->statistics, true) : [];
$isGuest = Yii::$app->user->isGuest;
$count = [
    'modules' => 0,
    'lessons' => 0,
    'exams' => 0,
    'webinars' => 0,
];

foreach ($model['modules'] as $m) {
    ++$count['modules'];
    foreach ($m['lessons'] as $l) {
        ++$count['lessons'];
        if (!empty($l['test']))
            ++$count['exams'];
        if (!empty($l['write']))
            ++$count['exams'];
    }
}
foreach ($model['webinars'] as $w)
    ++$count['webinars'];
?>

<?php // Передача блока в layout
    $this->beginBlock('modals');
?>
   <div id="youtube" class="modal youtube">
       <iframe src=""></iframe>
   </div>
<?php $this->endBlock(); ?>

<div class="pageTop topCourse">
    <div class="pageTitle">
        <h1 class="title"><?=$model['title']?></h1>
        <div class="bottom">
            <?php if ($isPrem) : ?>
                <div class="remark">Доступ до<br><span class="main"><?=ruMonth(date('d F Y', $access['end_at']))?></span></div>
            <?php elseif ($isCreator) : ?>
                <div class="remark">Курс<br><a href="<?=Yii::$app->params['listSubs'][1]['link']."closedoor/course/details/$model[id]"?>" class="main">Редактировать</a></div>
            <?php else : ?>
                <div class="cost">
                    <div class="currency"><span class="rub">руб</span><span class="month">/мес</span></div>
                    <div class="numb"><?=$model['cost']?></div>
                </div>
                <a href="<?=Yii::$app->params['listSubs'][1]['link']."pay/course/$model[id]"?>" class="btn subscribe">Подписаться</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="pageCourse">
    <a href="/courses#wave" class="back"><i class="icon-android-arrow-forward"></i> Список курсов</a>
    <h1 class="mainTitle"><span class="name">Курс /</span> <?= $model['title'] ?></h1>
    <div class="desc"><?= $model['desc'] ?></div>
    <?php if (!$isPrem) : // && !$isCreator ?>
    <div class="includSub">
        <h2 class="title">В ПОДПИСКУ ВХОДИТ</h2>
        <ul>
            <li><i class="icon-rocket-1"></i> Доступ к Модулям и Урокам, которые включает в себя Курс!</li>
            <li><i class="icon-rocket-1"></i> Возможность прорешать специально подготовленные задания, на закрепление темы Урока!</li>
            <li><i class="icon-rocket-1"></i> Доступ ко всем вебинарам, прикреплённым к этому курсу.</li>
            <li><i class="icon-rocket-1"></i> Возможность выполнять практические работы, с дальнейшей проверкой учителя.</li>
            <li><i class="icon-rocket-1"></i> При выполнение заданий из раздела "Тестирование", Вы будете видеть свою статистику, топы сильных и слабых тем.</li>
        </ul>
        <div class="buyCourse">
            <a href="<?=Yii::$app->params['listSubs'][1]['link']."pay/course/$model[id]"?>" class="btn">ПОДПИСАТЬСЯ</a>
            <div class="cost">
                <div class="sale"><span class="number"><?=$model['cost']?></span> <span class="rub">руб</span><span class="month">/мес</span></div>
                <?php if ($model['old_cost'] > $model['cost']) : ?>
                <div class="old"><?=$model['old_cost']?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($model['author']) : ?>
    <div class="author">
        <?php if ($model['author_desc']) : ?>
        <div class="about">
            <h2 class="title">АВТОР КУРСА<?= ($model['author']['name'] != '')
                ? " / <span class='name'>".$model['author']['surname'].' '.$model['author']['name']."</span>" : '' ?></h2>
            <div class="text"><?=$model['author_desc']?></div>
        </div>
        <?php endif; ?>
        <div class="ava">
            <?php if (!empty($model['author']['teacher']['video'])) : ?>
                <div class="hellowVideo" link="<?=$model['author']['teacher']['video']?>"><i class="icon icon-youtube-play"></i></div>
            <?php endif; ?>
            <img src="<?=Url::to("@uAvaLarge/".$model['author']['ava']) ?>">
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($model['modules'])) : ?>
    <div id="modules" class="set modules">
        <h2 class="listName">Модули</h2>
        <div class="list">
            <?php if ($model['modules'] != []) : ?>
                <?php
                $prevEnd = 1;
                if (!$isGuest)
                    $statCourse = (array)$stats[Yii::$app->params['subInx']]['courses'][$model['id']];
                foreach ($model['modules'] as $mKey => $module) :
                    if ($model['free'])
                        $module['free'] = 1;
                    $isActive = $module['free'];

                    if (!$isGuest) {
                        $statModule = (array)$statCourse['modules'][$module['id']];
                        $isActive = ( ($isActive || $isPrem) && ($mKey == 0 || $statModule['end'] || ($prevEnd && !$statModule['end'])) ) || ($isCreator);
                        $countExam = 0;
                        $points = 0;
                        $moduleEnd = 1;
                    }

                    foreach ((array)$module['lessons'] as $lesson) {
                        if ($lesson['free'])
                            $module['free'] = 1;

                        if (!$isGuest) {
                            $statLess = (array)$statModule['lessons'][$lesson['id']];
                            $points += $statLess['test']['points'];
                            $points += $statLess['write']['right'];

                            $qst = count($lesson['test']['questions']);
                            if ($lesson['test'] == [] || $qst == 0 || ($statLess['test']['points']/$qst * 100) >= 75)
                                $statLess['end'] = 1;

                            if (!$statLess['end'])
                                $moduleEnd = 0;

                            $countExam += $qst;
                            if ($lesson['write'] != [])
                                ++$countExam;

                            $statModule['lessons'][$lesson['id']] = $statLess;
                        }
                    }

                    if (!$isGuest) {
                        $progress = ($countExam == 0 || $module['free']) ? 100 : $points/$countExam * 100;
                        if ($prevEnd)
                            $statModule['end'] = ($countExam == 0) ? 1 : $moduleEnd;
                        $statCourse['modules'][$module['id']] = $statModule;
                    }
                ?>

                    <a <?=(($isActive && $isPrem) || $module['free'] || $isCreator)?"href='/module/$module[id]#wave'":''?>
                        class="module<?= ($module['free'] && !$isPrem && !$isCreator) ? ' free': ((!$isActive) ? ' disable' : '') ?>">
                        <div class="progress" style="width:<?=$progress?>%"></div>
                        <span class="text"><?=$module['title']?></span>
                        <div class="status">
                            <? if ($module['free'] && !$isPrem && !$isCreator) : ?>
                                <div class="free">FREE</div>
                            <? elseif ($isActive) : ?>
                                <div class="points">
                                    <span class="myPoints"><?=$points?></span>
                                    <span class="needPoints"><?=$countExam?></span>
                                </div>
                            <? else : ?>
                                <div class="disable"><i class="icon-lock"></i></div>
                            <? endif; ?>
                        </div>
                    </a>
                <?php if (!$isGuest) {$prevEnd = $statModule['end'];} endforeach; ?>
            <?php
                if (!$isGuest) {
                    $stats[Yii::$app->params['subInx']]['courses'][$model['id']] = $statCourse;
                    Yii::$app->user->identity->statistics = json_encode($stats);
                    Yii::$app->user->identity->update();
                }
            ?>
            <?php else : ?>
                <div class="default">Курс находится в разработке, пожалуйста проверьте наличие уроков позднее.</div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($model['webinars'])) : ?>
    <div id="webinars" class="set webinars">
        <h2 class="listName">Вебинары</h2>
        <div class="list">
            <?php foreach ($model['webinars'] as $web) : ?>
                <a href="/webinar/<?=$web['id']?>" class="webinar">
                    <div class="img">
                        <img src="<?= Url::to("@webnAvaSmall/$web[ava]"); ?>">
                        <div class="blackout">
                            <div class="btn"><i class="icon icon-youtube-play"></i></div>
                        </div>
                    </div>
                    <div class="title">
                        <?=$web['title']?>
                    </div>
                    <!-- <div class="info">
                        <span class="stat"><i class="icon icon-users"></i> <?=$web['countMembers']?></span>
                        <span class="stat"><i class="icon icon-comment-2"></i> <?=$web['countComments']?></span>
                        <span class="date"><?= 1//date('d.m.Y', $web['start_at']) ?></span>
                    </div> -->
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
$js = <<<JS
$(document).ready(function() {
    $('.author .hellowVideo').click(function(e) {
        e.preventDefault();
        let link = $(this).attr('link');
        $('#youtube iframe').attr('src', link);
        openModal('#youtube');
    });
});
JS;

$this->registerJs($js);
?>
