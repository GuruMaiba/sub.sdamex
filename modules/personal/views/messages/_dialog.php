<? use yii\helpers\Url; ?>
<? if ($model) : ?>
<? foreach ($model as $key => $dialog) : ?>
    <?
        $user = null;
        $myId = Yii::$app->user->identity->id;
        if (isset($dialog['firstUser']) && $dialog['first_user_id'] != $myId)
            $user = $dialog['firstUser'];
        else if (isset($dialog['secondUser']) && $dialog['secondUser']['id'] != $myId)
            $user = $dialog['secondUser'];
    ?>
    <div id="dialog-<?=$dialog['id']?>" class="item<?=($activeId == $dialog['id'])?' active':''?>" numb="<?=$dialog['id']?>" userId="<?=$user['id']?>">
        <div class="bg"></div>
        <div class="nick"><?=$user['username']?></div>
        <img src="<?=Url::to("@uAvaSmall/".$user['ava'])?>">
        <div class="del"> <i class="iconDel icon-cancel"></i> </div>
    </div>
<? endforeach; ?>
<? endif; ?>