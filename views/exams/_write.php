<?
use yii\helpers\Url;
use app\models\exam\write\Reply;
?>

<div class="exam">

    <?php // ОПИСАЕНИЕ ЗАДАНИЯ ?>
    <div class="desc"> <?= $model['task'] ?> <div class="connect"></div> </div>

    <?php // СОДЕРЖИМОЕ ЗАДАНИЯ ?>
    <div class="cont">
        <?php // ТЕКСТ ?>
        <? if (isset($model['text'])) : ?>
        <div class="text"><?= $model['text'] ?></div>
        <? endif; ?>

        <?php // АУДИРОВАНИЕ ?>
        <? if (isset($model['audio_name'])) : ?>
        <audio controls controlsList="nodownload" class="audio">
            <source src="<?= Url::to('@audioFolder/'.$model['audio_name'])?>" type="audio/mpeg">
        </audio>
        <? endif; ?>

        <?php // ЗАДАНИЕ ?>
        <div class="write <?=(!$isPrem)?'default':''?> <?=(!$isResult)?'new':''?>">
            <? if ($isPrem) : ?>
                <div class="countChars" title="Количество символов"><span class="have"><?=($isResult)?iconv_strlen($result['answer']):0?></span>/<span class="all"><?=Reply::COUNT_CHARS?></span></div>
                <input type="hidden" name="<?=$parents?>[id]" value='<?=$model['id']?>'>
                <textarea name="<?=$parents?>[answer]" placeholder="Введите ваш ответ..."><?=($isResult)?$result['answer']:''?></textarea>
                <div class="download">
                    <? if ($isResult) : ?>
                        <? if (!empty($result['archiveFile'])) : ?>
                        <a href="<?=Yii::$app->params['listSubs'][1]['link'].Url::to('@fileWrites/').$result['archiveFile']?>" title="Скачать свою работу"><i class="icon-download"></i></a>
                        <? endif; ?>
                    <? else : ?>
                    <input id="ansFiles-<?=$model['id']?>" class="files" type="file" name="<?=$parents?>[answerFiles][]" multiple>
                    <label for="ansFiles-<?=$model['id']?>" title="Загрузить работу"><i class="icon-download"></i></label>
                    <div class="countFiles" title="Количество файлов"><span class="have">0</span>/<span class="all"><?=Reply::COUNT_FILES?></span></div>
                    <? endif; ?>
                </div>
                <div class="errMess"></div>
                <div class="done"><i>Результаты, после проверки учителем, будут в Вашем личном кабинете!</i></div>
            <? else : ?>
                <i>Для проверки практических заданий, Вам необходимо стать учеником нашей школы!</i>
            <? endif; ?>
        </div>
        
    </div><!-- end content -->
</div><!-- end exam -->