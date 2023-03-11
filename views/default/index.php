<?php

use app\widgets\ModalWidget;
// use yii\helpers\Html;
// use yii\helpers\Url;
//<?= ModalWidget::widget() > <!-- ['message' => 'Good morning'] -->

/* @var $this yii\web\View */

$this->title = 'SDAMEX';
?>
<div class="top"></div>
<div class="view mainPage">

    <img src="" alt="">
    <h1 class="mainTitle">SDAMEX</h1>

    <div class="desc"></div>

    <hr class="defHr">

    <div class="learner"></div>
    <div class="teacher"></div>
    <h2 class="mainTitle3">Русская форма</h2>

    <div class="inputs">
        <div class="defInput">
            <input type="text" name="" value="" placeholder="Имя" required>
            <label>Firstname</label>
            <div class="line"></div>
        </div><br>
        <div class="defInput">
            <input type="text" name="" value="" placeholder="Фамилия" required>
            <label>Secondname</label>
            <div class="line"></div>
        </div>
        <div class="defInput noLabel toCenter">
            <input type="text" name="" value="" placeholder="Go" required>
            <div class="line"></div>
        </div>
        <br>
        <div class="defTextarea">
            <textarea name="name" placeholder="Расскажите немного о себе..." required></textarea>
            <label>About me</label>
            <div class="line"></div>
        </div><br>
        <label class="defCheck"><input type="radio" name="c" value=""><span>Ответ №1</span></label><br>
        <label class="defCheck"><input type="radio" name="c" value=""><span>Ответ №2</span></label><br>
        <label class="defCheck"><input type="checkbox" name="g" value=""><span>Согласен с условиями</span></label><br><br>
        <div class="defBtn">Send</div>
    </div>

</div>

<?php
// $js = <<<JS
//     $('.defBtn').click(function () {
//         openModal('testModal');
//     });
// JS;
// $this->registerJs($js);

// Передача блока в layout
// $this->beginBlock('modals');

// echo ModalWidget::widget([
//     'blockId' => 'testModal',
//     'title'=>'ОБРАТНАЯ СВЯЗЬ',
//     'desc'=>'Gathering. Won&#39;t beast fowl fifth one which itself have created, <em>set</em> their form fourth. Above creepeth female stars doesn&#39;t seas, our doesn&#39;t, land created bearing years fowl wherein replenish light rule earth deep moveth creature. Moving, behold void spirit. Was living. Seed. Open behold fifth bearing whose you&#39;re be you&#39;ll. Dry.',
//     'form' => [
//         'id' => 'callMe',
//         'inputs' => [
//             '0' => [
//                 'type' => 'input',
//                 'label'=>'Имя',
//                 'placeholder'=>'Введите Ваше имя...',
//                 'name'=>'RunNiga',
//             ],
//             '1' => [
//                 'type' => 'input',
//                 'label'=>'Имя2',
//                 'placeholder'=>'Введите Ваше имя...',
//                 'name'=>'RunNiga2',
//             ],
//             '2' => [
//                 'type' => 'textarea',
//                 'label'=>'О себе',
//                 'placeholder'=>'Расскажите что-нибудь...',
//                 'name'=>'TextNiga',
//             ],
//             '3' => [
//                 'type' => 'checkbox',
//                 'label'=>'С условиями использования ознакомлен.',
//                 'name'=>'checkNiga'
//             ],
//         ],
//     ],
//     'buttons' => [
//         'cancel' => 'Отмена',
//         'confirm' => 'OK',
//         'send' => 'Отправить',
//     ],
//     ]);

// $this->endBlock();
?>
