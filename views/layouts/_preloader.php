<?
use yii\helpers\Url;
?>
<div class="preloader">
    <svg class="loader" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500">
        <image xlink:href="/<?=Url::to('@imgFolder/wh-or-logo.svg')?>" x="165px" y="150px" height="200" width="200" />
        <circle cx="250" cy="250" r="240"/> 
        <circle cx="250" cy="250" r="215"/>
        <circle cx="250" cy="250" r="190"/>
        <circle cx="250" cy="250" r="165"/>
	</svg>
</div>