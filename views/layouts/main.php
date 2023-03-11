<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\MainAsset;

MainAsset::register($this);
$isGuest = Yii::$app->user->isGuest;

// Передаём переменную с путём к картинке, для изменения фона
$customImg = (isset($this->params['customImg'])) ? true : false;
if (!$customImg) {
    switch (Yii::$app->params['subInx']) {
        case 3:
            $this->params['customImg'] = Url::to("@imgFolder/russian.jpg");
            break;

        case 5:
            $this->params['customImg'] = Url::to("@imgFolder/math.jpg");
            break;

        case 7:
            $this->params['customImg'] = Url::to("@imgFolder/physics.png");
            break;

        case 8:
            $this->params['customImg'] = Url::to("@imgFolder/chemistry.jpg");
            break;
        
        default:
            $this->params['customImg'] = Url::to("@imgFolder/russian.jpg");
            break;
    }
}

if (!$isGuest)
    Yii::$app->user->identity->checkStat();

$lvl = Yii::$app->user->identity->level;
$rExp = $lvl['rangeExp'];
?><?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="/favicon.ico" type="image/x-icon">

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" >
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(65126053, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor:true
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/65126053" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->

    <?= Html::csrfMetaTags() ?>

    <title><?= Html::encode($this->title) ?></title>
    <!-- <meta name="description" content="<?= '' ?>">
    <meta name="keywords" content="<?= '' ?>"> -->
    
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= $this->render('_preloader') ?>
<div class="modalBody">
    <?php
        if (isset($this->blocks['modals']))
            echo $this->blocks['modals'];
    ?>
</div>
<div class="subjectsList">
    <?php
    $activeSub = [];
    foreach (Yii::$app->params['listSubs'] as $sId => $sub) :
        if ($sub['isActive']) :
            $activeSub[$sId] = $sub;
    ?>
            <a href="<?=$sub['link']?>"><?=$sub['lable']?></a>
        <? endif; ?>
    <? endforeach; ?>
</div>
<div class="wrap">
    <div class="header">
        <div class="bg <?=($customImg)?'custom':''?>">
            <div class="img" style="background-image:url('<?=$this->params['customImg']?>');">
            </div>
            <div class="trnspBG"></div>
            <svg class="bgCut shadow1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1925.07 606.04" preserveAspectRatio="none"><path class="cls-1" d="M.5,401.54s121.28,74.71,306,41c126-23,200.32-137.13,414-195,240-65,332,88,629,38,318.41-53.61,340-169,570-285,10.79-5.45,1,605,1,605H.5Z"/></svg>
            <svg class="bgCut shadow2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1925.07 606.04" preserveAspectRatio="none"><path class="cls-1" d="M.5,401.54s121.28,74.71,306,41c126-23,200.32-137.13,414-195,240-65,332,88,629,38,318.41-53.61,340-169,570-285,10.79-5.45,1,605,1,605H.5Z"/></svg>
            <svg class="bgCut" id="wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1925.07 606.04" preserveAspectRatio="none"><path class="cls-1" d="M.5,401.54s121.28,74.71,306,41c126-23,200.32-137.13,414-195,240-65,332,88,629,38,318.41-53.61,340-169,570-285,10.79-5.45,1,605,1,605H.5Z"/></svg>
        </div>
        <div class="navbar"> <!-- movableMenu -->
            <?php if (!$isGuest) : ?>
            <div class="mobileLvlBar">
                <div class="progress" style="width:<?= ($rExp[1] != 'MAX') ? (($lvl['exp'] - $rExp[0])/($rExp[1]-$rExp[0])*100) : 100 ?>%;"></div>
                <!-- <span class="exp"><?= ($rExp[0] != 'MAX') ? $lvl['exp']." exp" : '' ?></span> -->
                <!-- <span class="exp2"><?= ($rExp[1] != 'MAX') ? ($rExp[1] - $lvl['exp'])." exp" : 'MAX' ?></span> -->
                <div class="lvl"><?=$lvl['lvl']?> lvl</div>
            </div>
            <?php endif; ?>
            <div class="mobileMenuBtn"><img src="/<?=Url::to('@imgFolder/wh-or-logo.svg')?>"></div>
            <div class="logo">
                <div class="fill"></div>
                <img src="/<?=Url::to('@imgFolder/wh-or-logo.svg')?>">
                <a class="txt" href="/">SDAMEX</a>
            </div>
            <div class="menu">
                <a class="item" href="/courses#wave">Курсы</a>
                <a class="item" href="/exams#wave">Тесты</a>
                <a class="item" href="/webinars#wave">Вебинары</a>
                <!-- <a class="item" href="/fun#wave">Развлечения</a> -->
                <!-- <a class="item" href="/teachers#wave">Учителя</a> -->
                <span class="item subjects" onclick="$('.subjectsList').slideToggle(300);">Предметы</span>
            </div>
            <div class="auth">
                <?php if ($isGuest) : ?>
                    <div class="loginBtn">
                        <a href="<?=Yii::$app->params['listSubs'][1]['link']?>account/login?sub=<?=Yii::$app->params['subInx']?>&p=signup" class="btn signup"> Зарегистрироваться </a>
                        <a href="<?=Yii::$app->params['listSubs'][1]['link']?>account/login?sub=<?=Yii::$app->params['subInx']?>&p=signin" class="btn signin"> Войти </a>
                    </div>
                <?php else : ?>
                <div class="user">
                    <!-- <div class="notification">
                        <i class="icon-bell-o"></i>
                        <div class="list">
                            <div class="triangle"> </div>
                            <div class="item">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                            </div>
                            <div class="item">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                            </div>
                            <div class="item">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                            </div>
                        </div>
                    </div>
                    <div class="line"> </div> -->
                    <div class="info"> <!-- active -->
                        <div class="nick"><?= Yii::$app->user->identity->username ?></div>
                        <img class="ava" src="<?=Url::to("@uAvaSmall/".Yii::$app->user->identity->ava)?>">
                        <div class="userMenu">
                            <ul>
                                <li><a href="/personal/profile">Профиль</a></li>
                                <li><a href="/personal/messages">Сообщения</a></li>
                                <li><a href="/personal/settings">Настройки</a></li>
                                <li><a class="last" href="<?=Yii::$app->params['listSubs'][1]['link']?>account/logout?sub=<?=Yii::$app->params['subInx']?>">Выход</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="lvlBar">
                        <div class="progress" style="width:<?= ($rExp[1] != 'MAX') ? (($lvl['exp'] - $rExp[0])/($rExp[1]-$rExp[0])*100) : 100 ?>%;"></div>
                        <span class="exp"><?= ($rExp[0] != 'MAX') ? $lvl['exp']." exp" : '' ?></span>
                        <span class="exp2"><?= ($rExp[1] != 'MAX') ? ($rExp[1] - $lvl['exp'])." exp" : 'MAX' ?></span>
                        <span class="lvl"><?=$lvl['lvl']?> lvl</span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div id="content" class="content">
        <div class="container">
            <h1><?= $this->params['mainBgImg'] ?></h1>
            <?= $content ?>
        </div>
    </div>

    <div class="footer">
        <div class="wave">
            <svg class="bgCut" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920.5 863.08" preserveAspectRatio="none">
                <path class="cls-1" d="M.5,811.5s166,81,301,40c150.18-45.61,249-187,485-203,185.27-12.56,269.4,84,520,66,335-24,344-191,614-314-.33-196.33-1.17-204.17-1.5-400.5H0C0,275.33.5,536.17.5,811.5Z"/></svg>
            <svg class="bgCut shadow2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920.5 863.08" preserveAspectRatio="none">
                <path class="cls-1" d="M.5,811.5s166,81,301,40c150.18-45.61,249-187,485-203,185.27-12.56,269.4,84,520,66,335-24,344-191,614-314-.33-196.33-1.17-204.17-1.5-400.5H0C0,275.33.5,536.17.5,811.5Z"/></svg>
            <svg class="bgCut shadow1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920.5 863.08" preserveAspectRatio="none">
                <path class="cls-1" d="M.5,811.5s166,81,301,40c150.18-45.61,249-187,485-203,185.27-12.56,269.4,84,520,66,335-24,344-191,614-314-.33-196.33-1.17-204.17-1.5-400.5H0C0,275.33.5,536.17.5,811.5Z"/></svg>
        </div>
        <div class="container">
            <div class="logo">
                <img src="/<?=Url::to('@imgFolder/wh-or-logo.svg')?>">                    
            </div>
            <div class="col contacts">
                <div class="list">
                    <div class="name">Контакты</div>
                    <a href="/personal/messages/1" class="item support">Служба поддержки</a>
                    <div class="item">- +7 (977) 834-60-15 / Телефон</div>
                    <div class="item">- team@sdamex.ru / Email</div>
                    <hr>
                    <div class="item socialLinks">
                        <a href="https://vk.com/sdamex_ru" target="_blank"><i class="icon-vk-logo"></i></a>
                        <a href="https://www.instagram.com/sdam.ex/" target="_blank"><i class="icon-instagrem"></i></a>
                    </div>
                </div>
            </div>
            <div class="col usefulLinks">
                <div class="list">
                    <div class="name">Полезности</div>
                    <a href="https://sdamex.ru/#feedback" class="item">Обратный звонок</a>
                    <!-- <a href="#" class="item">Политика конфиденциальности</a> -->
                    <a href="https://sdamex.ru/contract-offer" class="item">Договор оферты</a>
                    <!-- <a href="#" class="item">Правила сайта</a> -->
                </div>
            </div>
            <div class="copyright">Используя сайт, вы соглашаетесь с <a href="https://sdamex.ru/contract-offer" target="_blank">договором оферты</a>.<br>© Copyright SDAMEX 2020-<?=date('Y', time())?> | <a href="https://www.rusprofile.ru/ip/320774600253350" target="_blank">ИП Миломаева П.А.</a></div>
        </div>
    </div>
</div>
<div class="goTop hide"><i class="icon-rocket-1"></i></div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.mobileMenuBtn').click(function () {
            if ($(this).hasClass('disable'))
                return false;

            $(this).addClass('disable');
            $('.navbar .logo').toggleClass('active');
            $('.navbar .menu').toggleClass('active');
            setTimeout(() => {
                $(this).removeClass('disable');
            }, 700);
        });
    });

    var scroll = $(window).scrollTop();
    // var footer = $("footer").outerHeight();

    $(window).on('scroll', function () {
        var scrollWin = $(window).scrollTop();

        // Показываем кнопку прокрутки
        if (scrollWin > 0) {
            $('.goTop').show(0);
            $('.goTop').removeClass('hide');
        } else {
            $('.goTop').addClass('hide');
            setTimeout(() => {
                $('.goTop').hide(0);
            }, 300);
        }

        // Изменения меню при скролле
        // if (scrollWin + 1 > scroll && scroll > 200 || scroll > $(document).height() - $(window).height() - 500) {
        //     $("header").css("margin", "-56px 0 0 0");
        //     scroll = scrollWin;
        // } else {
        //     $("header").css("margin", "0");
        //     scroll = scrollWin;
        // }

    });

    $('.goTop').click(function () {
        $('html, body').animate({
            scrollTop: 0
        }, 700);
    });
</script>