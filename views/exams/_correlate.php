<?
use yii\helpers\Url;
$letts = [
    'A', 'B', 'C', 'D', 'E', 'F', 'G',
    'H', 'I', 'J', 'K', 'L', 'M', 'N',
    'O', 'P', 'Q', 'R', 'S', 'T', 'U', 
    'V', 'W', 'X', 'Y', 'Z'
];
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
        <div class="correlate">
            <?php // СООТНОШЕНИЕ ?>
            <div class="variants">
                <?php
                    $letters = [];
                    $numbers = [];
                    $json_ans = [];
                    $count = 0;
                    foreach ((array)$model['pairs'] as $pair) {
                        $json_ans[$pair['id']] = 0;
                        if ($model['qst_hidden'] || !empty($pair['qst_text'])) {
                            $letters[] = ['id'=>$pair['id'], 'text'=>($model['qst_hidden'])?null:$pair['qst_text']];
                            ++$count;
                        }
                        $numbers[] = ['id'=>$pair['id'], 'text'=>$pair['ans_text'], 'isActive'=>($isResult)?in_array($pair['id'], $userAns):false];
                    }

                    if (!$isResult) {
                        if (!$model['qst_hidden'])
                            shuffle($letters);
                        shuffle($numbers);
                    }
                ?>
                <?php // ОТВЕТЫ ПОД НОМЕРАМИ ?>
                <div class="numbers">
                    <?php foreach ($numbers as $i => $num) : ?>
                    <?php
                        if ($isResult) {
                            $edit_id = 0;
                            foreach ($userAns as $qst_id => $ans_id) {
                                if ($num['id'] == $ans_id)
                                    $edit_id = $qst_id;
                            }

                            $userAns[$edit_id] = [
                                'i' => $i+1,
                                'text' => $num['text'],
                                'class' => ($userAns[$edit_id] == $edit_id) ?'':'error',
                            ];
                        }
                        ?>
                    <div id="num-<?=$num['id']?>" class="item ans <?=($num['isActive'])?'active':''?>" number="<?=$num['id']?>">
                        <div class="check"><?=$i+1?></div> <div class="txt"><?=$num['text']?></div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php // ОТВЕТЫ ПОД БУКВАМИ ?>
                <div class="letters">
                    <?php foreach ($letters as $i => $lett) : ?>
                    <div class="letter <?=($isResult)?'active':''?>">
                        <div id="lett-<?=$lett['id']?>" class="item qst <?=($isResult)?$userAns[$lett['id']]['class']:'error'?>" number="<?=$lett['id']?>">
                            <div class="check"><?=$letts[$i]?></div>
                            <div class="txt"><?=$lett['text']?></div>
                        </div>
                        <div class="item ans" number="0">
                            <div class="check"><?=($isResult)?$userAns[$lett['id']]['i']:''?></div>
                            <div class="txt"><?=($isResult)?$userAns[$lett['id']]['text']:''?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <input type="hidden" name="<?=$parents?>[id]" value='<?=$model['id']?>'>
                <input class="ansStr" type="hidden" name="<?=$parents?>[answers]" value='<?=json_encode($json_ans)?>'>
            </div>

            <?php if ($count > 0) : ?>
            <?php // ТАБЛИЦА РЕЗУЛЬТАТОВ ?>
            <table class="tableAnswers">
                <thead class="row rowLetters">
                    <th class="title"><?=$model['qst_name']?></th>
                    <?php for ($i=0; $i < $count; $i++) : ?>
                        <th class="lett"><?=$letts[$i]?></th>
                    <?php endfor; ?>
                </thead>
                <tbody class="row rowNumbers">
                    <td class="title"><?=$model['ans_name']?></td>
                    <?php //for ($i=1; $i == $count; $i++) : ?>
                    <?php foreach ($letters as $i => $lett) : ?>
                        <td id="tbl-ans-<?=$lett['id']?>" class="numb"><?=($isResult)?$userAns[$lett['id']]['i']:''?></td>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div><!-- end content -->
</div><!-- end exam -->
