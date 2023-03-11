<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\PersonalAsset;

PersonalAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="/favicon.ico" type="image/x-icon">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= $this->render('_preloader'); ?>
<div class="modalBody">
    <?php
        if (isset($this->blocks['modals'])) {
            echo $this->blocks['modals'];
        }
    ?>
</div>
<div class="personalArea">
    <div class="menu">
        <a href="/">
            <div class="logo">
                <svg version="1.1" id="Слой_1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" viewBox="0 0 1000 1000" xml:space="preserve">
                    <style>.st4{fill:#FF5C57}.st5{fill:#FFF}</style>
                    <g id="Orange">
                        <g id="diploma">
                            <path id="funnel" class="st4" d="M302.46 597.92c26.25 6.06 56.54-10.1 64.62 20.19 8.26 30.98-40.8 73.61-82.79 62.6-32.61-8.55-56.83-39.98-50.48-70.67 6.62-32.02 44.71-44.32 52.5-48.46-6.06 10.09 3.71 33.47 16.15 36.34z"/>
                            <path id="funnel_1_" class="st5" d="M294.07 614.5c16.42 1.58 33.77-9.83 40.66 8.09 7.05 18.32-20.05 47.48-46.33 43.51-20.42-3.08-37.21-20.64-35.34-39.74 1.95-19.93 17.67-32.46 22.15-35.49-3.04 6.53 4.03 22.2 18.86 23.63z"/>
                        </g>
                        <path id="_x5F_body_1_" class="st4" d="M988 391.87c-46.03 29.5-67.27 93.23-200.62 100.31-230.58 12.24-286.18-78.48-474.41-55.47C146.29 457.09 19.7 534.08 30.32 685.13c7.47 106.19 145.34 145.06 261.99 139.26 205.84-10.25 300.34-253.14 302.7-268.48-16.52 9.44-115.46 194.97-302.7 197.67-83.39 1.2-167.58-21.24-178.2-86.15-12.42-75.93 93.64-148.5 214.78-159.32 198.26-17.7 234.53 50.02 437.24 39.53 136.9-7.07 213.61-129.81 221.87-155.77z"/>
                        <path id="body_5_" class="st5" d="M988 391.87c-46.03 29.5-84.97 61.37-218.32 68.45-230.58 12.24-286.18-78.48-474.41-55.47C128.58 425.23 2 502.21 12.62 653.27c7.47 106.19 145.34 145.06 261.99 139.26 205.84-10.25 317.46-220.68 319.82-236.03-16.52 9.44-132.57 162.52-319.82 165.22-83.39 1.2-167.58-21.24-178.2-86.15-12.42-75.93 93.64-148.5 214.78-159.32 198.26-17.7 234.53 50.02 437.24 39.53 136.9-7.08 231.31-97.95 239.57-123.91z"/>
                        <path id="_x5F_head" class="st4" d="M441.22 371.62s-125.66-7.05-220.27 21.23c-107.66 32.17-177.08 79.41-177.08 79.41s14.61-60.43 72.41-125.1c60.74-67.96 93.33-84.81 93.33-84.81s94.18-43.49 186.32-54.55c123.97-14.87 205.87 8.16 205.87 8.16s-42.46 19.53-88.45 64.78c-38.07 37.46-72.13 90.88-72.13 90.88z"/>
                        <path id="head2" class="st5" d="M592.59 187.57s-42.46 19.53-88.45 64.78c-4.7 4.62-9.34 9.5-13.85 14.47-2.29-2.24-28.82-27.26-72.77-28.75-62.56-2.11-125.33 28.49-125.33 28.49l1.15 3.55s62.77-30.61 124.15-28.5c44.14 1.51 69.78 26.91 70.56 27.69-31.07 34.76-56.04 73.92-56.04 73.92s-125.65-7.05-220.27 21.23c-107.66 32.17-177.08 79.41-177.08 79.41s14.62-60.42 72.41-125.1c60.74-67.96 93.33-84.81 93.33-84.81s94.18-43.49 186.32-54.55c123.96-14.86 205.87 8.17 205.87 8.17z"/>
                        <g id="ribbon2">
                            <path id="_x5F_body_8_" d="M500.02 274.45c15.86 12.5 33.23 26.31 66.09 28.32 57.83 3.54 89.69-61.37 181.74-38.94" fill="none" stroke="#ff5c57" stroke-width="3" stroke-miterlimit="10"/>
                            <path id="body_11_" d="M488.21 267.19c15.86 12.5 33.23 26.31 66.09 28.32 57.83 3.54 89.69-61.37 181.74-38.94" fill="none" stroke="#fff" stroke-width="3" stroke-miterlimit="10"/>
                            <g id="_x5F_tassel">
                                <path class="st4" d="M743.85 264.31c9.8-2.13 19.52 2.6 29.32 2.79 1.93.04 1.93-2.96 0-3-10.14-.19-19.91-4.91-30.11-2.68-1.9.4-1.1 3.3.79 2.89z"/>
                                <path class="st4" d="M744.49 266.5c8.48 2.37 16.42 6.36 25.41 6.18 1.93-.04 1.93-3.04 0-3-8.71.17-16.41-3.78-24.61-6.08-1.87-.52-2.66 2.37-.8 2.9z"/>
                                <path class="st4" d="M741.58 267.86c9.15 3.68 17.45 8.93 27.41 10.14 1.91.23 1.9-2.77 0-3-9.58-1.17-17.81-6.5-26.61-10.04-1.79-.71-2.57 2.19-.8 2.9zm6.65-2.14c10.44.37 20.22 4.3 30.49 5.79 1.89.27 2.7-2.62.8-2.89-10.54-1.53-20.56-5.51-31.28-5.9-1.94-.07-1.94 2.93-.01 3z"/>
                                <path class="st4" d="M757.13 273.22l9.79.78c2.81.22 7.6-.2 9.89 1.55 1.53 1.17 3.03-1.43 1.51-2.59-2.29-1.74-5.67-1.51-8.39-1.72l-12.81-1.02c-1.91-.16-1.9 2.84.01 3z"/>
                            </g>
                            <g id="tassel_1_">
                                <path class="st5" d="M734.41 254.86c9.8-2.13 19.52 2.6 29.32 2.79 1.93.04 1.93-2.96 0-3-10.14-.19-19.91-4.91-30.11-2.68-1.9.41-1.1 3.3.79 2.89z"/>
                                <path class="st5" d="M735.05 257.05c8.48 2.37 16.42 6.36 25.41 6.18 1.93-.04 1.93-3.04 0-3-8.71.17-16.41-3.78-24.61-6.08-1.87-.51-2.66 2.38-.8 2.9z"/>
                                <path class="st5" d="M732.14 258.42c9.15 3.68 17.45 8.93 27.41 10.14 1.91.23 1.9-2.77 0-3-9.58-1.17-17.81-6.5-26.61-10.04-1.79-.71-2.57 2.19-.8 2.9zm6.65-2.15c10.44.37 20.22 4.3 30.49 5.79 1.89.27 2.7-2.62.8-2.89-10.54-1.53-20.56-5.51-31.28-5.9-1.94-.06-1.94 2.94-.01 3z"/>
                                <path class="st5" d="M747.69 263.78l9.79.78c2.81.22 7.6-.2 9.89 1.55 1.53 1.17 3.03-1.43 1.51-2.59-2.29-1.74-5.67-1.51-8.39-1.72l-12.81-1.02c-1.91-.16-1.9 2.84.01 3z"/>
                            </g>
                        </g>
                    </g>
                </svg>
            </div>
        </a>
        <a href="/personal/profile/<?= Yii::$app->user->id ?>" class="item"> <i class="icon-user"></i> </a>
        <a href="/personal/messages" class="item"> <i class="icon-mail-1"></i> </a>
        <!-- <a href="/personal/dictionary" class="item"> <i class="icon-book"></i> </a> -->
        <a href="/personal/events" class="item"> <i class="icon-calendar"></i> </a>
        <a href="/personal/check" class="item"> <i class="icon-task"></i> </a>
        <div class="bottom">
            <a href="/personal/notification" class="item"> <i class="icon-bell-o"></i> </a>
            <a href="/personal/settings" class="item"> <i class="icon-cog"></i> </a>
            <a href="<?=Yii::$app->params['listSubs'][1]['link']?>account/logout?sub=<?=Yii::$app->params['subInx']?>" class="item"> <i class="icon-logout"></i> </a>
        </div>
        <div class="close"><i class="icon-cancel"></i></div>
    </div>
    <div class="content">
        <div class="submenu">
            <div class="mobileMenuBtn"><i class="icon-navicon"></i></div>
            <?php
                $name = Yii::$app->params['shortName'];
                $name = explode(" ", $name, 2);
            ?>
            <div class="switchSubject">
                <span class="siteName">
                    <?=($name[1])?"<span>$name[0]</span> $name[1]":$name[0]?>
                </span>
                <div class="subsList">
                    <?php foreach (Yii::$app->params['listSubs'] as $id => $sub) : ?>
                        <?php if ($id != 1 && $id != Yii::$app->params['subInx'] && $sub['isActive']) : ?>
                            <span onclick="customHref('<?=$sub['link']?>');" class="sub"> <?=$sub['lable']?> </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php
            if (!Yii::$app->user->isGuest) :
                $lvl = Yii::$app->user->identity->level;
            ?>
            <div class="user">
                <div class="lvl">lvl.<span><?= $lvl['lvl'] ?></span></div>
                <a href="/personal/profile/<?= Yii::$app->user->identity->id ?>" class="info">
                    <div class="hoverBG"> </div>
                    <div class="nick"><?= Yii::$app->user->identity->username ?></div>
                    <img class="ava" src="<?=Url::to("@uAvaSmall/".Yii::$app->user->identity->ava)?>">
                </a>
            </div>
            <div class="lvlBar">
                <div class="progress" style="width:<?=
                    ($lvl['rangeExp'][1] == 'MAX')
                        ? '100': ( ($lvl['exp'] - $lvl['rangeExp'][0]) / ($lvl['rangeExp'][1] - $lvl['rangeExp'][0]) * 100 )
                ?>%;"></div>
            </div>
            <? endif; ?>
        </div>
        <div class="container">
            <?= $content ?>
        </div>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.mobileMenuBtn').click(function () {
            $('.menu').addClass('active');
        });
        $('.menu .close').click(function () {
            $('.menu').removeClass('active');
        });
    });

    function customHref(domain) {
        document.location.href = domain + document.location.pathname.slice(1) + document.location.search;
    }
</script>
