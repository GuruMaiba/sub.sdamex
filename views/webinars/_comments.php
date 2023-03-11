<?php
use yii\helpers\{Url, Html};

$myId = Yii::$app->user->identity->id;
if ($model != []) :
foreach ($model as $com) :
    $user = ($isAdd) ? Yii::$app->user->identity : $com['user'];
    $gmt = ($_COOKIE['GMT']) ? $_COOKIE['GMT'] : 3;
?>

<div class="item" numb="<?=$com['id']?>">
    <div class="info">
        <a href="<?=Url::to(['profile', 'id'=>$user['id']])?>" class="author">
            <img src="<?=Url::to("@uAvaSmall/$user[ava]")?>">
            <span class="nick"><?=$user['username']?></span>
        </a>
        <span class="date">&middot; <?=ruMonth(date('d F Y H:i', ($com['create_at'] + $gmt*60*60) ))?></span> <?//25 Апреля 2020 19:30?>
        <?php if ($user['id'] == $myId || Yii::$app->user->can('admin')) : ?>
        <div class="control">
            <span class="middot">&middot;</span>
            <i class="edit icon-pencil-1"></i><i class="del icon-trash-b"></i>
        </div>
        <?php endif; ?>
    </div>
    <div class="text"><?=Html::encode($com['message'])?></div>
</div>

<?php endforeach; ?>
<?php endif; ?>