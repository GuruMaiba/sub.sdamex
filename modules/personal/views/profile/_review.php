<?php
use yii\helpers\Url;
use yii\helpers\Html;
 ?>

<div class="item">
    <? if ($model['review_anonymously']) : ?>
    <a class="user">
        <img class="ava" src="<?=Url::to("@uAvaSmall/anonymous.jpg")?>">
        <div class="nick">Аноним</div>
    </a>
    <? else : ?>
    <a class="user" href="<?=$model['student_id']?>">
        <img class="ava" src="<?=Url::to("@uAvaSmall/".$model['student']['ava'])?>">
        <div class="nick"><?=$model['student']['username']?></div>
    </a>
    <? endif; ?>
    <div class="stars"><?php viewStars($model['review_rating']) ?></div>
    <div class="cloud">
        <div class="text"><?=Html::encode($model['review_text'])?></div>
    </div>
    <div class="reFooter"><span class="date"><?=$model['review_date']?></span></div>
</div>