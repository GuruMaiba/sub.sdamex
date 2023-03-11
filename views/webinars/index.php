<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;

/* @var $this yii\web\View */
// $this->params['customImg'] = Url::to("@imgFolder/webinars.jpg");

// $this->title = 'Вебинары | SDAMEX';
$nearest = [];
$now = time();

foreach ($model as $web) {
    if ($web['start_at'] > $now && ($nearest == [] || $nearest['start_at'] > $web['start_at']))
        $nearest = $web;
}

$gmt = 3;
if (!empty($_COOKIE['GMT']))
    $gmt = $_COOKIE['GMT'];
// $this->params['customImg'] = Url::to("@webnAvaLarge/$nearest[ava]");
?>
<div class="pageTop topWebinars">
    <?php if ($nearest != []): ?>
    <?php
        $author = User::find()->select(['id','ava','username'])->where(['id'=>$nearest['author_id']])->limit(1)->one();   
    ?>
    <div class="banner newWebinar <? //noImg ?>" style="background-image: url('<?= Url::to("@webnAvaLarge/$nearest[ava]"); ?>');">
        <div class="bg"></div>
        <h1 class="markTitle">Ближайший вебинар</h1>
        <div class="cont">
            <a href="<?=Url::to(['personal/profile', 'id'=>$author['id']])?>" class="author">
                <img class="ava" src="<?=Url::to("@uAvaSmall/$author[ava]")?>">
                <span class="nick"><?=$author['username']?></span>
            </a>
            <h1 class="title"><?=$nearest['title']?></h1>
            <div class="timer">
                <?php
                    $strDate = ($gmt) ? strtotime($gmt.' hours', $nearest['start_at']) : $nearest['start_at'];
                    $strDate = ruMonth(date('d F Y H:i', $strDate));

                    $dif = $nearest['start_at'] - time();
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
                <div class="date"><?=$strDate?></div><!-- 25 Февраля 2020 в 19:00 -->
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
            <a href="/webinar/<?=$nearest['id']?>" class="defBtn btn participant">Принять участие</a>
        </div>
    </div>
    <?php else : ?>
    <div class="pageTitle">
        <h1 class="title">Вебинары</h1>
    </div>
    <?php endif; ?>
</div>
<div class="pageWebinars">
    <?php if ($model != []): ?>
        <h1 class="mainTitle">Все вебинары</h1>
        <!-- <hr> -->
        <!-- <div class="buttons">
            <div class="sort">
                <span class="name">Сортировка:</span>
                <span class="btn active">Новые</span>
                <span class="btn">Старые</span>
                <span class="btn">Популярные</span>
            </div>
            <div class="right">
                <div class="tags">
                    <i class="icon icon-tag-1"></i>
                    <div class="cloud">

                    </div>
                </div>
            </div>
        </div> -->

        <div class="list">
            <?php foreach ($model as $web) : ?>
                <a href="/webinar/<?=$web['id']?>" class="item">
                    <div class="img">
                        <img src="<?= Url::to("@webnAvaSmall/$web[ava]"); ?>">
                        <div class="blackout">
                            <?php if ((int)$web['cost'] < 1) : ?>
                            <div class="free">БЕСПЛАТНЫЙ</div>
                            <?php endif; ?>
                            <div class="btn"><i class="icon icon-youtube-play"></i></div>
                        </div>
                    </div>
                    <div class="title">
                        <?=$web['title']?>
                    </div>
                    <div class="info">
                        <span class="stat"><i class="icon icon-users"></i> <?=$web['countMembers']?></span>
                        <span class="stat"><i class="icon icon-comment-2"></i> <?=$web['countComments']?></span>
                        <span class="date"><?= date('d.m.Y', $web['start_at']+($gmt*3600)) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="default">Вебинары в скором времени будут добавлены, благодарим за понимание!</div>
    <?php endif; ?>
</div>

<?php

$js = <<<JS
    $(document).ready(function () {
        var iconNumActive = 0;
        
        setTimer();

        $('.icon').click(function(){
            let num = $(this).attr('number');
            if (iconNumActive == num) {
                iconNumActive = 0;
                changeClassStar('icon', 0, 'active');
            } else {
                iconNumActive = num;
                changeClassStar('icon', num, 'active');
            }
        });

        $('.icon').mouseover(function(){
            let num = $(this).attr('number');
            changeClassStar('icon', num, 'hover');
        });

        $('.icons').mouseout(function(){
            changeClassStar('icon', 0, 'hover');
        });
    });
JS;

$this->registerJs($js);
 ?>
