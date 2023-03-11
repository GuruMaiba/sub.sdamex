<?php
    use yii\helpers\{Url, Html};
 ?>

<div class="page pageCheck">
    <?php if ($examResults) : ?>
    <div class="fullexams">
        <h1 class="pTitle">Выполненные экзамены</h1>
        <div class="list">
            <?php foreach ($examResults as $result) : ?>
            <?php $ans = json_decode($result['answers'], true); ?>
            <a href="/exam/<?=$result['fullexam_id']?>?result=<?=$result['id']?>" class="exam">
                <div class="cont">
                    <div class="name"><?=$result['fullexam']['name']?></div>
                    <?php if ($result['check']) : ?>
                    <div class="comment"><?=Html::encode($result['teacher_comment'])?></div>
                    <?php else : ?>
                    <div class="comment def">Учитель в скором времени проверит Вашу работу!</div>
                    <?php endif; ?>
                </div>
                <div class="results">
                    <div class="item exp">
                        <label>Опыт</label><br>
                        <span class="current"><?=$ans['user_exp']?></span><span class="all">/<?=$ans['max_exp']?></span>
                    </div>
                    <div class="item points">
                        <label>Баллы</label><br>
                        <span class="current"><?=$ans['user_points']?></span><span class="all">/<?=$ans['max_points']?></span>
                    </div>
                    <div class="item mark">
                        <?php if ($result['check']) : ?>
                            <span class="numb"><?=$ans['mark']?></span><br>
                        <?php else : ?>
                            <span class="numb def">-</span><br>
                        <?php endif; ?>
                        <label>Оценка</label>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($practicalReplies) : ?>
    <div class="practicals">
    <h1 class="pTitle">Выполненные практические</h1>
        <div class="list">
            <?php foreach ($practicalReplies as $replies) : ?>
            <div class="exam">
                <div class="cont">
                    <div class="name">
                        <?php
                        if ($replies['write']['exercise_id'] > 0) {
                            echo 'Тестирование | '.$replies['write']['exercise']['section']['fullexam']['name']
                                .' - '.$replies['write']['exercise']['name'];
                            $subject = Yii::$app->params['listSubs'][$replies['write']['exercise']['section']['fullexam']['subject_id']];
                        } else if ($replies['write']['lesson_id'] > 0) {
                            echo 'Курс | '.$replies['write']['lesson']['module']['course']['title']
                                .' - '.$replies['write']['lesson']['title'];
                            $subject = Yii::$app->params['listSubs'][$replies['write']['lesson']['module']['course']['subject_id']];
                        } ?>
                    </div>

                    <?php if ($replies['check']) : ?>
                        <div class="comment"><?=$replies['teacher_comment']?> <span class='cop'>- Учитель</span></div>
                    <?php else : ?>
                        <div class="comment def">Учитель в скором времени проверит Вашу работу!</div>
                    <?php endif; ?>

                    <div class="reply">
                        <div class="task"><?=$replies['write']['task']?></div>
                        <div class="text"><?=$replies['write']['text']?></div>
                        <div class="answer">
                            <?php if ($replies['text']) : ?>
                                <div class="txt"><?=$replies['text']?></div>
                            <?php endif; ?>
                            <?php if ($replies['archive_file'] && !$replies['check']) : ?>
                                <a href="<?=$subject['link'].Url::to('@fileWrites/').$replies['archiveFile']?>" class="download">Архив с выполненной работой.</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="results">
                    <div class="item exp">
                        <label>Опыт</label><br>
                        <span class="current <?=($replies['check'])?'':'def'?>"><?=($replies['check'])?$replies['exp']:'-'?></span>
                        <span class="all">/<?=$replies['write']['exp']?></span>
                    </div>
                    <?php if ($replies['write']['exercise_id'] > 0) : ?>
                    <div class="item points">
                        <label>Баллы</label><br>
                        <span class="current <?=($replies['check'])?'':'def'?>"><?=($replies['check'])?$replies['points']:'-'?></span>
                        <span class="all">/<?=$replies['write']['exercise']['fullexam_points']?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php if (!$practicalReplies && !$examResults) : ?>
        <div class="cap">У вас нет выполненных практических упражнений!</div>
    <?php endif; ?>
</div>

<?php

$js = <<<JS
    $('.practicals .exam').click(function () {
        $(this).children('.cont').children('.reply').slideToggle(300);
    });
JS;

$this->registerJs($js);

?>