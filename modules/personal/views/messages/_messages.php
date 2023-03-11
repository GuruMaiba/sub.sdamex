<? 
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$user = Yii::$app->user->identity;
$oldCDay = (isset($lastDate)) ? intdiv($lastDate,(24*60*60)) : null;
$oldUser = (isset($lastUser)) ? $lastUser : 0;
$gmt = (isset($_COOKIE['GMT'])) ? $_COOKIE['GMT']*60*60 : 3*60*60;
?>

<? if ($type == 'old' && count($messages) < 50) :?>
<div class="begin">
    <img class="ava" src="<?=Url::to("@uAvaSmall/".$otherUser['ava'])?>">
    <div class="nick"><?=$otherUser['username']?></div>
    <div class="mess">Это начало истории ваших личных сообщений с <span class="name">@<?=$otherUser['username']?></span></div>
</div>
<? endif; ?>

<? foreach ($messages as $key => $message) :
    $cDay = intdiv($message['date'],(24*60*60));
    $message['date'] += $gmt;
    $mUser = ($message['user_id'] == $user->id) ? $user : $otherUser;

    if ($oldCDay != $cDay) :
    ?>
        <div class="delimiter">
            <div class="date"><?=ruMonth(date('d F Y', $message['date']))?></div>
        </div>
    <? endif; ?>

    <?
        $class = (($mUser['id'] != $oldUser) || ($oldCDay != $cDay)) ? 'start' : 'continue';
        $view = ($message['user_id'] != $user->id || $message['view'])?'':'notViewed';
    ?>
    <div class="message <?=$class?> <?=$view?>" numb="<?=$message['id']?>" user="<?=$message['user_id']?>" date="<?=$message['date']?>">
        <div class="top">
            <a href="/personal/profile/<?=$mUser['id']?>">
                <img class="ava" src="<?=Url::to("@uAvaSmall/".$mUser['ava'])?>">
                <span class="nick"><?=$mUser['username']?></span>
            </a>
            <span class="time"><?=date('H:i', $message['date'])?></span>
        </div>
        <div class="text"><?=Html::encode($message['message'])?></div>
    </div>
<?
    $oldUser = $mUser['id'];
    $oldCDay = $cDay;
    endforeach;
?>