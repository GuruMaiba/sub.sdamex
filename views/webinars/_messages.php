<?php
use yii\helpers\{Url, Html};

$ava = Url::to("@uAvaSmall/no_img.jpg");
$myId = Yii::$app->user->identity->id;
?>
<?php if ($model != []): ?>
    <?php foreach($model as $message): ?>
    <? $user = ($isAdd) ? Yii::$app->user->identity : $message['user'] ?>
        <div class="message" numb="<?=$message['id']?>">
            <div class="ava" user="<?=$user['id']?>">
                <img src="<?=Url::to("@uAvaSmall/$user[ava]")?>">
                <?php if ($isAuthor && $user['id'] != $myId) :?>
                <div class="like"> <i class="icon-heart-small"></i> </div>
                <?php endif; ?>
            </div>
            <a href="<?=Url::to("/personal/profile/$user[id]")?>" class="author" target="_blank">
                <span class="nick"><?=$user['username']?></span>
            </a>
            <?php if (($isAuthor || $isModer) && $user['id'] != $myId) :?>
            <div class="menu" user="<?=$user['id']?>">
                <i class="icon-android-more-horizontal"></i>
                <div class="actions">
                    <?php if ($isAuthor) :?>
                    <div class="item exps">
                        <div class="exp" exp="1">6exp</div>
                        <div class="exp" exp="2">18exp</div>
                        <div class="exp" exp="3">36exp</div>
                    </div>
                    <hr>
                    <?php endif; ?>
                    <div class="item ban"> ЗАБАНИТЬ </div>
                </div>
            </div>
            <?php endif; ?>
            <div class="txt"> <?=Html::encode($message['message'])?> </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
