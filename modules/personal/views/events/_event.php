<?php
use yii\helpers\Url;
$href = 'href="profile/'.$event['user_id'].'"';
 ?>

<a <?=(!$isPast)?$href:''?>
    class="event <?=($isTeacher)?'teacher':'student'?> <?=($isPast)?'past':''?>"
    numb='<?= $event['id'] // json_encode($event['id']) ?>'>
    <?php if ($isPast) : ?>
        <div class="date"><?=$date?></div>
        <div class="status">
            <i class="icon icon-help"></i>
            <div class="list <?=(!$isTeacher)?'user':''?>">
                <div class="txt">Как прошло Ваше занятие?</div>
                <div class="item succ">Прошло успешно!</div>
                <?php if ($isTeacher) :?>
                <div class="item diff">Возникли трудности!</div>
                <?php endif; ?>
                <div class="item err">Не состоялось!</div>
            </div>
        </div>
    <?php endif; ?>
    <div class="time"><?=$event['time']?></div>
    <img class="ava" src="<?=Url::to("@uAvaSmall/".$event['ava'])?>">
    <div class="name"><?=$event['name']?></div>
    <div class="nick"><?=$event['nick']?></div>

    <?php if ($isTeacher) : //DEBUG: title="нажмите чтобы скопировать"?>
        <div class="skype"> <i class="icon icon-social-skype"></i> <?=$event['active']?></div>
    <?php else : $rat = "$event[active]"; ?>
        <?php if ($rat > 0) : $rat = explode('.', $rat); ?>
            <div class="rating">
                <? viewStars($rat[0]) ?>
                <span class="numb"><?=$rat[0]?></span>
                <span class="fraction">.<?=$rat[1]?></span>
            </div>
        <?php else : ?>
            <div class="rating"> Отзывы отсутствуют </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="bottom"> </div>
</a>