<?php
use yii\helpers\Url;
$this->title = 'Уведомления | SDAMEX';
$gmt = (isset($_COOKIE['GMT'])) ? $_COOKIE['GMT'] : 0;
$create = Yii::$app->user->identity->created_at + ($gmt * 3600);
 ?>

<div class="page pageNotification">
    <div class="pTitle">УВЕДОМЛЕНИЯ</div>
    <div class="list">

        <div class="item"> <? // link notView ?>
            <div class="icon default"> <i class="icon-bell-o"></i> </div>
            <div class="cont">
                <div class="text"><strong>Рады приветствовать Вас на проекте SDAMEX!</strong><br><br>
                    Надеемся Вы останетесь довольны нашим сайтом. В случае возникновения вопросов, обращайтесь в службу поддержки!</div>
                <div class="date"> <?=ruMonth(date('d F Y', $create))?> &middot; <?=date('H:i', $create)?> </div>
            </div>
        </div>

    </div> <!-- end .list -->
</div> <!-- end .pageNotification -->
