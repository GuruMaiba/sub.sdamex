<?
use yii\helpers\Url;

$model['text'] = preg_replace_callback(
    '/\_\(([\s\S]*?)\)/',
    function ($matches) use ($parents, $userAns) {
        $arr = explode('/',$matches[1]);
        $val = $userAns[$arr[0]];
        $class = ($val == $arr[1])?'':'err';
        $req = '<span class="wordInput noLabel toCenter '.$class.'">'
                .'<input type="text" name="'.$parents.'[answers]['.$arr[0].']" '
                    .'placeholder="'.$arr[0].'" '
                    .'correct="'.$arr[1].'" '
                    .'value="'.$val.'" '
                    .'autocomplete="off" required>'
                .'<span class="line"></span>'
            .'</span>';
        return $req;
    },
    $model['text']
);
?>

<div class="exam">
    <?php // ОПИСАЕНИЕ ЗАДАНИЯ ?>
    <div class="desc"> <?= $model['task'] ?> <div class="connect"></div> </div>

    <?php // СОДЕРЖИМОЕ ЗАДАНИЯ ?>
    <div class="cont">
        <?php // ЗАДАНИЕ ?>
        <? if (isset($model['text'])) : ?>
        <div class="addition"><?= $model['text'] ?></div>
        <? endif; ?>

        <input type="hidden" name="<?=$parents?>[id]" value='<?=$model['id']?>'>
    </div><!-- end content -->
</div><!-- end exam -->
