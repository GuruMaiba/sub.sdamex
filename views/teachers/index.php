<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\ModalWidget;

/* @var $this yii\web\View */

// $this->params['customImg'] = Url::to("@imgFolder/teachers.jpg");
// $this->title = 'Учителя';

?>

<?php // Передача блока в layout
    $this->beginBlock('modals');
?>
   <div id="youtube" class="modal youtube">
       <iframe src=""></iframe>
   </div>
<?php $this->endBlock(); ?>

<div class="pageTop topTeachers">
    <div class="pageTitle">
        <h1 class="title">Преподаватели</h1>
    </div>
</div>

<div class="pageTeachers">
    <?php if ($model != []): ?>
    <div class="teachersList">
        <?php foreach ($model as $teacher) : ?>
            <a href="/personal/profile/<?=$teacher['user_id']?>" class="teacher">
                <div class="dignity"><?=$teacher['dignity']?></div>
                <div class="img">
                    <img class="ava" src="<?= Url::to("@uAvaLarge/".$teacher['user']['ava']) ?>">
                    <?php if ($teacher['video']): ?>
                    <div class="video" link="<?=$teacher['video']?>"><i class="icon icon-youtube-play"></i></div>
                    <?php endif; ?>
                </div>
                <div class="fullName">
                    <div class="firstName"> <?=$teacher['user']['name']?> </div>
                    <div class="middleName"> <?=$teacher['user']['username']?> </div>
                </div>
                <div class="spec">
                    <?php 
                        $spec = json_decode($teacher['specialization'], true);
                        $end = end($spec);
                        foreach ($spec as $val) :
                    ?>
                        <span><?=$val?></span><?=($end == $val)?'':','?>
                    <?php endforeach; ?>
                </div>
                <div class="bottom">
                    <div class="more">ПОДРОБНЕЕ</div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <?php else : ?>
        <div class="default">Учителя в скором времени будут добавлены, благодарим за понимание!</div>
    <?php endif; ?>
</div>

<?php

$js = <<<JS
    $(document).ready(function() {
        $('.teachersList').on('click', '.video', function(e) {
            e.preventDefault();
            let link = $(this).attr('link');
            $('#youtube iframe').attr('src', link);
            openModal('#youtube');
        });
    });
JS;

$this->registerJs($js);
?>
