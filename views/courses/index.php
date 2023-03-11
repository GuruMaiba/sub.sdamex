<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

// $this->title = 'Курсы';
// $this->params['customImg'] = Url::to("@imgFolder/courses.jpg");
$isBanner = false;
?>

<div class="pageTop topCourses">
    <!-- <div class="banner ogeVSege">
        <div class="bg"></div>
        <div class="oge">
            <h1 class="title">ОГЕ</h1>
            <div class="desc">
                Cursus Quisque eu proin, per sit in enim posuere morbi dictumst commodo erat duis nam massa. Pellentesque nulla litora maecenas ornare at. Enim nonummy.
            </div>
            <div class="buy">
                <div class="action">
                    <a href="#" class="defBtn btn">Оформить подписку</a><br>
                    <a href="#" class="tryFree">Попробовать бесплатно</a>
                </div>
                <div class="cost">
                    2.500
                    <span class="rub">руб</span><span class="month">/мес</span>
                </div>
            </div>
        </div>
        <div class="brdVS"></div>
        <div class="ege">
            <h1 class="title">ЕГЕ</h1>
            <div class="desc">
                Ultricies consequat convallis ad tincidunt justo iaculis viverra rhoncus, lorem torquent, mauris est. Phasellus elementum non potenti, elit ultricies magna leo.
            </div>
            <div class="buy">
                <div class="cost">
                    2.500
                    <span class="rub">руб</span><span class="month">/мес</span>
                </div>
                <div class="action">
                    <a href="#" class="defBtn btn">Оформить подписку</a><br>
                    <a href="#" class="tryFree">Попробовать бесплатно</a>
                </div>
            </div>
        </div>
    </div> -->

    <? foreach ($model as $course) : ?>
    <? if ($course['id'] == 2) : $isBanner = true; ?>
    <div class="banner newWebinar <? //noImg ?>" style="background-image: url('<?= Url::to("@crsAvaLarge/$course[ava]"); ?>');">
        <div class="bg"></div>
        <h1 class="markTitle">Старт серии вебинаров</h1>
        <div class="cont">
            <!-- <a href="<?=Url::to(['personal/profile', 'id'=>$author['id']])?>" class="author">
                <img class="ava" src="<?=Url::to("@uAvaSmall/$author[ava]")?>">
                <span class="nick"><?=$author['username']?></span>
            </a> -->
            <h1 class="title"><?=$course['title']?></h1>
            <div class="timer">
                <?php
                    $gmt = $_COOKIE['GMT'];
                    $date = strtotime('16.11.2020 15:00');
                    $strDate = ($gmt) ? strtotime($gmt.' hours', $date) : $date;
                    $strDate = ruMonth(date('d F Y H:i', $strDate));

                    $dif = $date - time();
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
            <a href="/course/<?=$course['id']?>#wave" class="defBtn btn participant">Принять участие</a>
        </div>
    </div>
    <? endif; ?>
    <? endforeach; ?>

    <? if (!$isBanner) : ?>
    <div class="pageTitle">
        <h1 class="title">Курсы</h1>
    </div>
    <? endif; ?>
</div>

<!-- <h1 class="mainTitle1">Все курсы</h1> -->
<div class="pageCourses">
    <?php if ($model != []) : ?>
    <div class="list">
        <?php
        foreach ($model as $course) : 
            $cMod = 0;
            $cLess = 0;
            $cExam = 0;
            $cWeb = count($course['webinars']);
            foreach ($course['modules'] as $module) {
                ++$cMod;
                foreach ($module['lessons'] as $lesson) {
                    ++$cLess;
                    if (isset($lesson['test']))
                        ++$cExam;
                    if (isset($lesson['write']))
                        ++$cExam;
                }
            }
            if ($course['id'] == 1) {
                $cLess = 30;
                $cExam = 40;
            } else if ($course['id'] == 2) {
                $cWeb = 5;
            }
        ?>
            <a id="<?= $course['id'] ?>" href="<?= Url::to(["/course/$course[id]#wave"]) ?>" class="course leftImg">
                <div class="desc">
                    <div class="img">
                        <img src="<?= Url::to("@crsAvaSmall/$course[ava]"); ?>">
                        <div class="bg"></div>
                    </div>
                    <div class="info">
                        <div class="item allProgress">
                            <div class="progress">
                                <div class="borders">
                                    <div class="circle bg"><div class="border"></div></div>
                                    <div class="circle leftActive" style="transform: rotate(230deg);"><div class="border"></div></div>
                                    <div class="circle rightActive" style="transform: rotate(180deg);"><div class="border"></div></div>
                                    <div class="circle disable" style="display:none;"><div class="border"></div></div>
                                </div>
                            </div>
                            ПРОГРЕСС
                        </div>
                        <?php if ($cMod > 0) : ?>
                        <div class="item modules">
                            <div class="progress">
                                <div class="borders">
                                    <div class="circle bg"><div class="border"></div></div>
                                    <div class="circle leftActive" style="transform: rotate(230deg);"><div class="border"></div></div>
                                    <div class="circle rightActive" style="transform: rotate(180deg);"><div class="border"></div></div>
                                    <div class="circle disable" style="display:none;"><div class="border"></div></div>
                                </div>
                                <span class="number"><?=$cMod?></span>
                            </div>
                            Модули
                        </div>
                        <?php endif; ?>
                        <?php if ($cLess > 0) : ?>
                        <div class="item lessons">
                            <div class="progress">
                                <div class="borders">
                                    <div class="circle bg"><div class="border"></div></div>
                                    <div class="circle leftActive" style="transform: rotate(230deg);"><div class="border"></div></div>
                                    <div class="circle rightActive" style="transform: rotate(180deg);"><div class="border"></div></div>
                                    <div class="circle disable" style="display:none;"><div class="border"></div></div>
                                </div>
                                <span class="number"><?=$cLess?></span>
                            </div>
                            Уроки
                        </div>
                        <?php endif; ?>
                        <?php if ($cExam > 0) : ?>
                        <div class="item points">
                            <div class="progress">
                                <div class="borders">
                                    <div class="circle bg"><div class="border"></div></div>
                                    <div class="circle leftActive" style="transform: rotate(195deg);"><div class="border"></div></div>
                                    <div class="circle rightActive" style="transform: rotate(180deg);"><div class="border"></div></div>
                                    <div class="circle disable" style="display:none;"><div class="border"></div></div>
                                </div>
                                <span class="number"><?=$cExam?></span>
                            </div>
                            Задания
                        </div>
                        <?php endif; ?>
                        <?php if ($cWeb > 0) : ?>
                        <div class="item points">
                            <div class="progress">
                                <div class="borders">
                                    <div class="circle bg"><div class="border"></div></div>
                                    <div class="circle leftActive" style="transform: rotate(195deg);"><div class="border"></div></div>
                                    <div class="circle rightActive" style="transform: rotate(180deg);"><div class="border"></div></div>
                                    <div class="circle disable" style="display:none;"><div class="border"></div></div>
                                </div>
                                <span class="number"><?=$cWeb?></span>
                            </div>
                            Вебинары
                        </div>
                        <?php endif; ?>
                        <!-- <div class="item cost">
                            2000 руб/мес
                        </div> -->
                    </div>
                </div>
                <hr>
                <div class="titleTest"><?=$course['title']?></div>
            </a>
        <?php endforeach; ?>
    </div>
    <?php else : ?>
        <div class="default">Курсы уже в разработке и скоро появятся в открытом доступе!</div>
    <?php endif; ?>
</div>

<?php

$js = <<<JS
    setTimer();
JS;

$this->registerJs($js);
 ?>
