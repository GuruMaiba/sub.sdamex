<?
use yii\helpers\Url;

// $model - test
// $parents - relational nesting
// $isResult - if isset result
// $userAns - user answers
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
        <div class="test">
            <?php
            $json_ans = [];
            $json_corr = json_decode($model['correct_answers'], true);
            foreach ((array)$model['questions'] as $i => $qst) :
                $json_ans[$qst['id']] = [];
            ?>
            <div id="qst-<?=$qst['id']?>" class="question <?=($qst['multiple_answer'])?'multi':''?> err" dbid="<?=$qst['id']?>">
                <div class="numb"><?=$i+1?></div>
                <div class="exe">
                    <div class="txt"><?=$qst['text']?></div>
                    <div class="variants">
                        <?php foreach ((array)$qst['answers'] as $j => $ans) : ?>
                        <div id="ans-<?=$ans['id']?>" dbid='<?=$ans["id"]?>'
                            class="item<?php
                                echo ( in_array($ans['id'], $json_corr[$qst['id']]['answers']) ) ?' right':'';
                                if ($isResult)
                                    echo ( in_array($ans['id'], $userAns[$qst['id']]) ) ?' active':''?>">
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
            <input type="hidden" name="<?=$parents?>[id]" value='<?=$model['id']?>'>
            <input class="ansStr" type="hidden" name="<?=$parents?>[answers]" value='<?=json_encode($json_ans)?>'>
        </div>
    </div><!-- end content -->
</div><!-- end exam -->