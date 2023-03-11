<?
use yii\helpers\Url;
use app\models\exam\write\Reply;
?>
<div class="task"><?=$model['task']?><hr></div>
<div class="desc"><?=$model['text']?><hr></div>

<?php // АУДИРОВАНИЕ ?>
<? if ($model['audio_name'] && $model['audio_name'] != '') : ?>
<audio controls controlsList="nodownload" class="audio">
    <source src="<?= Url::to('@audioFolder/'.$model['audio_name'])?>" type="audio/mpeg">
</audio>
<? endif; ?>

<?php // ЗАДАНИЕ ?>
<form id="formTesting">
<? if ($type == 'test') : ?>
    <div class="test">
        <?php
        $json_ans = [];
        $json_corr = json_decode($model['correct_answers'], true);
        foreach ((array)$model['questions'] as $i => $qst) :
            $json_ans[$qst['id']] = [];
        ?>
        <div id="qst-<?=$qst['id']?>" class="question <?=($qst['multiple_answer'])?'multi':''?>" dbid="<?=$qst['id']?>">
            <div class="numb"><?=$i+1?></div>
            <div class="exe">
                <div class="txt"><?=$qst['text']?></div>
                <div class="variants">
                    <?php foreach ((array)$qst['answers'] as $j => $ans) : ?>
                    <div id="ans-<?=$ans['id']?>" dbid='<?=$ans["id"]?>'
                        class="item<?php
                            // echo ( in_array($ans['id'], $json_corr[$qst['id']]['answers']) ) ?' right':'';
                            ?>">
                        <div class="check"><?=$j+1?></div>
                        <div class="lable"><?=$ans['text']?></div>
                    </div>
                    <?php endforeach; ?>
                    <div class="answer hidden">
                        <span class="name">ОТВЕТ</span>
                        <span class="position"></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <input type="hidden" name="Test[id]" value='<?=$model['id']?>'>
        <input class="ansStr" type="hidden" name="Test[answers]" value='<?=json_encode($json_ans)?>'>
    </div>
<? elseif ($type == 'write') : ?>
    <? if ($isStudent || $isAuthor) : ?>
        <div class="write new">
                <div class="countChars"><span class="have">0</span>/<span class="all"><?=Reply::COUNT_CHARS?></span></div>
                <input type="hidden" name="Write[id]" value='<?=$model['id']?>'>
                <textarea name="Write[answer]" placeholder="Введите ваш ответ..."></textarea>
                <div class="done"><i>Результаты, после проверки учителем, будут в Вашем личном кабинете!</i></div>
                <div class="download">
                    <? //if ($isResult) : ?>
                    <!-- <a href="<?=$subject['link'].Url::to('@fileWrites/').$result['archiveFile']?>" title="Скачать свою работу"><i class="icon-download"></i></a> -->
                    <? //else : ?>
                    <input id="ansFiles-<?=$model['id']?>" class="files" type="file" name="Write[answer_files][]" multiple>
                    <label for="ansFiles-<?=$model['id']?>" title="Загрузить работу"><i class="icon-download"></i></label>
                    <div class="countFiles" title="Количество файлов"><span class="have">0</span>/<span class="all"><?=Reply::COUNT_FILES?></span></div>
                    <? //endif; ?>
                </div>
                <div class="errMess"></div>
        </div>
    <? else : ?>
        <div class="write default"><i>Для выполнения практических заданий, Вам необходимо приобрести курс.</i></div>
    <? endif; ?>
<? endif; ?>
</form>
<? if ($type == 'test' || $isStudent) : ?>
<div class="send">Ответить</div>
<? endif; ?>
