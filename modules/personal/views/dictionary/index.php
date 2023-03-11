<?php
    use yii\helpers\Url;
 ?>

<div class="page pageDictionary">
    <div class="dictionary">
        <div class="search"> <i class="icon icon-android-search"></i> <input type="text" name="" value="" placeholder="Поиск"> </div>
        <div class="list">
            <div class="item">
                <div class="img"> <img class="ava" src="<?=Url::to("@imgUser/ava.jpg")?>"> </div>
                <div class="word">Dog</div>
                <div class="translate">Собака, Утка, Носорог</div>
            </div>
            <div class="item">
                <div class="img"> <img class="ava" src="<?=Url::to("@imgUser/ava.jpg")?>"> </div>
                <div class="word">Flower</div>
                <div class="translate">Цветок, Цветник, Цветочек</div>
            </div>
        </div>
    </div>
    <div class="wordInfo">
        <img class="ava" src="<?=Url::to("@imgUser/ava.jpg")?>">
        <div class="word">Dog</div>
        <div class="translate">Собака, Утка, Носорог</div>
    </div>
</div>
