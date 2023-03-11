<?php
use yii\helpers\Url;
use app\widgets\ModalWidget;

$this->title = 'SDAMenglish';
 ?>

<div class="page pageEvents">
    <div class="days">
        <?php if ($model['past']) : ?>
        <div class="day pastDay">
            <div class="number">Прошедшие</div>
            <div class="list">
                <?php foreach ($model['past'] as $date => $day) : ?>
                <?php foreach ($day as $key => $event) : ?>
                    <?= $this->render('_event', [
                        'isTeacher' => $is['teacher'],
                        'isPast' => true,
                        'event' => $event,
                        'date' => $date
                    ]) ?>
                <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($model['future']) : ?>
        <?php foreach ($model['future'] as $date => $day) : ?>
        <div class="day">
            <div class="number"><?=$date?></div>
            <div class="list">
            <?php foreach ($day as $key => $event) : ?>
                <?= $this->render('_event', [
                    'isTeacher' => $is['teacher'],
                    'isPast' => false,
                    'event' => $event,
                    'date' => $date
                ]) ?>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else : ?>
            <div class="cap">У вас нет активных событий!</div>
        <?php endif; ?>

        <!-- <a href="#" class="event webinar">
            <div class="time">
                21:00 - 23:00
            </div>
            <div class="img">
                <div class="blackout"> </div>
                <img src="/<?=1//Url::to("@webnAvaLarge/webinarTest.jpg")?>">
            </div>
            <div class="title">
                Как выучить 5.000 слов за 3 часа!
            </div>
        </a> -->

    </div><!-- end list -->
</div><!-- end page -->

<?php
$csrf = Yii::$app->getRequest()->getCsrfToken();
$js = <<<JS
    var csrf = '$csrf';
    var activeEvent = null;
    $(document).ready(function () {
        $('.event .status .item').click(function () {
            activeEvent = $(this).parents('.event');
            let id = activeEvent.attr('numb');
            if (activeEvent.hasClass('disable')) { return false; }
            let form = $('#formDiffLesson');
            if ($(this).hasClass('succ')) {
                activeEvent.addClass('disable');
                $.ajax({
                    url: "events/event-status",
                    type: "POST",
                    data: {
                        '_csrf': csrf,
                        'id': id,
                        'type': 'succ'
                    },
                    success: function(req) {
                        if (req == 1) {
                            activeEvent.remove();
                            if (!$('.pastDay .list').is('.ajax_block')) {
                                $('.pastDay').remove();
                            }
                        } else {
                            globalError();
                        }
                    }
                });
            } else if ($(this).hasClass('diff')) {
                $('.errorMessage .bigTitle').text('ВОЗНИКЛИ ТРУДНОСТИ');
                form.children('input[name=id]').val(id);
                form.children('input[name=type]').val('diff');
                openModal('.errorMessage');
            } else {
                $('.errorMessage .bigTitle').text('ЗАНЯТИЕ НЕ СОСТОЯЛОСЬ');
                form.children('input[name=id]').val(id);
                form.children('input[name=type]').val('err');
                openModal('.errorMessage');
            }
        });

        $('.modalBody .errorMessage .send').click(function () {
            let data = `_csrf=${csrf}&`+$('#formDiffLesson').serialize();
            let th = $(this);
            if (!th.hasClass('disable')) {
                th.addClass('disable');
                $.ajax({
                    url: "events/event-status",
                    type: "POST",
                    data: data,
                    success: function(req) {
                        console.log(req);
                        th.removeClass('disable');
                        if (req == 1) {
                            activeEvent.remove();
                            if (!$('.pastDay .list').is('.ajax_block')) {
                                $('.pastDay').remove();
                            }
                        } else {
                            globalError();
                        }
                        closeModal();
                    }
                });
            }
        });
    });
JS;
$this->registerJs($js);
 ?>

<?php // Передача блока в layout
    $this->beginBlock('modals');
?>
<?= ModalWidget::widget([
    'blockClass' => 'errorMessage',
    'title'=>'ОПИШИТЕ ПРОБЛЕМУ',
    'desc'=>'Пожалуйста отправьте нам описание того, что на ваш счёт явилось проблемой.',
    'form' => [
        'id' => 'formDiffLesson',
        'inputs' => [
            [
                'type' => 'hidden',
                'name'=>'id',
            ],
            [
                'type' => 'hidden',
                'name'=>'type',
            ],
            [
                'type' => 'textarea',
                'label' => 'Описание',
                'placeholder'=>'Введите Вашу притензию...',
                'name'=>'text',
            ]
        ],
    ],
    'buttons' => [
        'cancel' => 'Отмена',
        'send' => 'Отправить',
    ],
    ]) ?>
<?php $this->endBlock(); ?>