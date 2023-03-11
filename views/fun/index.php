<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'Курсы';

?>

<div class="pageTop topFun">

</div>

<div class="pageFun">
<div class="posts">
    <div class="tags">
        <div class="tag">Спорт</div>
        <div class="tag">Политика</div>
        <div class="tag">Политика</div>
        <div class="connect"></div>
        <div class="name">#Tags</div>
    </div>

    <div class="post">
        <div class="date">21 Декабря 2019 <div class="connect"></div> </div>
        <div class="img">
            <img src="<?= Url::to("@imgWebinar/webinarTest.jpg"); ?>">
            <div class="blackout"> </div>
        </div>
        <div class="text">
            <p>Tree great hath shall seed let. Isn&#39;t forth dominion after whales made dominion creature beast moveth lights, firmament female spirit for which seed. After, give image moving Heaven there spirit you unto winged is moving night bring. Evening fruit may two creature they&#39;re yielding wherein land Creature without multiply after. After called them don&#39;t saying thing, morning very for god from creature multiply was cattle abundantly male darkness created. Said gathering itself so it they&#39;re Deep let first divide created dry spirit it, herb over fruitful whales.</p>

            <p>God itself first so bring can&#39;t rule our earth us sea. Which over kind dry male gathered beginning waters herb was, they&#39;re tree life place there have creeping man there appear one moved first you&#39;ll so is midst very whales void was second. Winged man beginning. Bring spirit Over fruitful she&#39;d. Waters his midst thing life replenish saw and can&#39;t. Which Evening lights.</p>

            <p>To so without can&#39;t unto, every darkness was good a days abundantly the fruitful first stars isn&#39;t fish subdue from god seasons his sea, life multiply upon kind. Air creepeth. Abundantly have void She&#39;d every were. Make earth gathered after them days itself man fourth great. Whose gathered without. Living.</p>
        </div>
        <div class="info">
            <a href="<?=Url::to(['profile', 'id'=>1])?>" class="author">
                <span class="nick">StGuruMaiba</span>
                <img class="ava" src="<?=Url::to("@imgUser/ava.jpg")?>">
            </a>
            <div class="likes">
                <span class="numb">5.000</span>
                <i class="icon icon-heart-o"></i>
            </div>
        </div>
    </div>

    <div class="post mem">
        <div class="date">21 Декабря 2019 <div class="connect"></div> </div>
        <div class="img">
            <img src="<?= Url::to("@imgMem/testmem1.jpg"); ?>">
            <div class="blackout"> </div>
        </div>
        <div class="info">
            <a href="<?=Url::to(['profile', 'id'=>1])?>" class="author">
                <span class="nick">StGuruMaiba</span>
                <img class="ava" src="<?=Url::to("@imgUser/ava.jpg")?>">
            </a>
            <div class="likes">
                <span class="numb">5.000</span>
                <i class="icon icon-heart-o"></i>
            </div>
        </div>
    </div>

    <div class="post mem">
        <div class="date">21 Декабря 2019 <div class="connect"></div> </div>
        <div class="img">
            <img src="<?= Url::to("@imgMem/testmem2.jpg"); ?>">
            <div class="blackout"> </div>
        </div>
        <div class="info">
            <a href="<?=Url::to(['profile', 'id'=>1])?>" class="author">
                <span class="nick">StGuruMaiba</span>
                <img class="ava" src="<?=Url::to("@imgUser/ava.jpg")?>">
            </a>
            <div class="likes">
                <span class="numb">5.000</span>
                <i class="icon icon-heart-o"></i>
            </div>
        </div>
    </div>
</div>
<div class="fixBlock">
    <div class="outBrd"> </div>
    <div class="sort">
        <div class="name">СОРТИРОВКА</div>
        <div class="subname">ТИП</div>
        <div class="types">
            <div class="type">Все</div>
            <div class="type">Новости</div>
            <div class="type">Мемы</div>
        </div>
    </div>
</div>
</div>

<?php

$js = <<<JS
    $('.posts').on('mouseover', '.likes', function() {
        $(this).children('.icon').removeClass('icon-heart-o').addClass('icon-heart');
    });

    $('.posts').on('mouseout', '.likes', function() {
        $(this).children('.icon').removeClass('icon-heart').addClass('icon-heart-o');
    });
JS;

$this->registerJs($js);
 ?>
