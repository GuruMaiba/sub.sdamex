<?php
 if ($model) :
    $course = (!empty($course)) ? $course : $model['module']['course'];
?>
    <div class="video">
        <?php if ($model['video'] != '') : ?>
            <iframe src="<?=$model['video']?>?modestbranding=1&rel=0&controls=1&showinfo=0" frameborder="0" allowfullscreen></iframe>
        <?php else : ?>
            <div class="def"><i class="icon-youtube-play"></i> <span>Мы работаем над этим видео, оно уже совсем скоро появится!</span></div>
        <?php endif; ?>
    </div>
    <div class="title"><?=$model['title']?></div>
    <div class="desc"><?=$model['desc']?></div>
    <?php $links = json_decode($model['links'], true); ?>
    <?php if ($links != []) : ?>
    <div class="links">
        <?php foreach ($links as $link) : ?>
            <a href="<?=$link['link']?>"><?=$link['name']?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($model['test']) || !empty($model['write'])) : ?>
    <div class="exams">
        <?php if (!empty($model['test'])) : ?>
        <div class="exam test <?=($stats['test']['completed'])?'completed':''?>">
            <?php
                $qstCount = count($model['test']['questions']);
                $deg = 360 * ($stats['test']['points']/$qstCount);
            ?>
            <div class="link" numb="<?=$model['test']['id']?>" type="test">
                <div class="repeat"><?=($stats['test']['completed']) ? 'Попробовать снова' : 'Выполнить'?></div>
                <span class="name">Тестовое задание</span>
                <div class="border"> </div>
            </div>
            <div class="brd"></div>
            <div class="progress active<?= ($deg == 360) ? ' done' : '' ?>">
                <div class="borders">
                    <div class="circle bg"><div class="border"></div></div>
                    <div class="visiblePath<?= ($deg > 180) ? ' full' : ''?>">
                        <div class="circle leftActive" style="transform: rotate(<?=$deg?>deg);"><div class="border"></div></div>
                        <div class="circle rightActive" style="transform: rotate(<?=($deg > 180)?180:$deg?>deg);"><div class="border"></div></div>
                    </div>
                </div>
            </div>
            <div class="points<?= ($deg == 360) ? ' done' : '' ?>">
                <span class="myPoints"><?=(empty($stats['test']['points']))?0:$stats['test']['points']?></span>
                <span class="needPoints"><?=$qstCount?></span>
            </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($model['write'])) : ?>
        <div class="exam write">
            <?php
                // if (!empty($model['write'])) :
                $reply = $model['write']['answers'][0];
            ?>
                <div class="link<?= (isset($reply) && !$reply['check']) ? ' check' : '' ?>" numb="<?=$model['write']['id']?>" type="write">
                    <div class="repeat"><?=(isset($reply)) ? (($reply['check']) ? 'Попробовать снова' : 'Проверяется') : 'Выполнить'?></div>
                    <span class="name">Письменное задание</span>
                    <div class="border"></div>
                </div>
                <?php if ($reply['check']) : ?>
                    <div class="brd"></div>
                    <div class="progress<?= ($stats['write']['right']) ? ' right' : ' wrong'?>">
                        <?php if ($stats['write']['right']) : ?>
                            <i class="icon-graduation-cap"></i>
                        <?php else : ?>
                            <i class="icon-cancel"></i>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="testing"></div>
    <?php endif; ?>

    <?php if ($nextAcc) : ?>
    <div class="nextLes<?= ($stats['end'] || empty($model['test'])) ? '' : ' hide disable' ?><?= (!$nextAcc) ? ' notStudent' : '' ?>">
        <hr>
        Следующий урок
    </div>
    <?php else : ?>
    <div class="includSub<?= (empty($model['test'])) ? '' : ' hide' ?>">
        <div class="buyMess">Для продолжения необходимо приобрести подписку!</div>
        <h2 class="title">В ПОДПИСКУ ВХОДИТ</h2>
        <ul>
            <li><i class="icon-rocket-1"></i> Доступ к Модулям и Урокам, которые включает в себя Курс!</li>
            <li><i class="icon-rocket-1"></i> Возможность прорешать специально подготовленные задания, на закрепление темы Урока!</li>
            <li><i class="icon-rocket-1"></i> Доступ ко всем вебинарам, прикреплённым к этому курсу.</li>
            <li><i class="icon-rocket-1"></i> Возможность выполнять практические работы, с дальнейшей проверкой учителя.</li>
            <li><i class="icon-rocket-1"></i> При выполнение заданий из раздела "Тестирование", Вы будете видеть свою статистику, топы сильных и слабых тем.</li>
        </ul>
        <div class="buyCourse">
            <a href="<?=Yii::$app->params['listSubs'][1]['link']."pay/course/$course[id]"?>" class="btn">ПОДПИСАТЬСЯ</a>
            <div class="cost">
                <div class="sale"><span class="number"><?=$course['cost']?></span> <span class="rub">руб</span><span class="month">/мес</span></div>
                <?php if ($course['old_cost'] > $course['cost']) : ?>
                <div class="old"><?=$course['old_cost']?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
<?php else : ?>
    <div class="video">
        <div class="def"><i class="icon-youtube-play"></i> <span>Урок не найден или недоступен, проверьте выполнение тестовых заданий!</span></div>
    </div>
<?php endif; ?>