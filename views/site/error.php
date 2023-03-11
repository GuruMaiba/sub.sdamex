<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Url;
use yii\helpers\Html;
use app\assets\ErrorAsset;

ErrorAsset::register($this);
$btnView = true;
$code = $exception->statusCode;
switch ($code) {
    case 404:
        $name = 'Страница не найдена ';
        $message = 'Мы добрались до самых потайных уголков сайта, но так и не нашли эту страницу!';
        break;

    case 403:
        $name = 'Отказ в обработке запроса ';
        break;

    case 500:
        $name = 'Ошибка сервера ';
        $message = 'Кажется Вы сломали наш сайт, попробуйте Ваш запрос позднее или обратитесь в службу поддержки!';
        break;

    case 400:
        $name = 'Неверный запрос ';
        $message = 'Пожалуйста, проверьте Ваш запрос, ведь сервер обнаружил синтаксическую ошибку!';
        break;

    case 401:
        $name = 'Отсутствует авторизация ';
        $message = 'Для доступа к этой странице, необходимо авторизоваться на сайте, спасибо за понимание!';
        break;

    case 402:
        $name = 'Необходима оплата ';
        $message = 'Для доступа к этой странице, необходимо приобрести подписку, спасибо за понимание!';
        break;

    case 503:
        $name = 'Сервер временно не работает ';
        $message = 'Сервер временно приостановлен, но в скором времени обязательно продолжит свою работу!';
        $btnView = false;
        break;

    case 504:
        $name = 'Время ответа сервера истекло ';
        $message = 'Мы ждали как могли, но сервер не отвечает!';
        $btnView = false;
        break;
    
    default:
        $name .= ' ';
        $code = 500;
        break;
}

$this->title = $name.'| '.Yii::$app->params['shortName'];
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Url::to(['/logo.ico'])]);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name = "robots" content = "noindex, nofollow">
    <?= Html::csrfMetaTags() ?>
    <title><?= $this->title ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

    <nav>
        <div class="menu">
            <div class="menu_logo">
                <a href="/" class="website_name">SDAMEX</a>
            </div>
            <div class="menu_links">
                <a href="https://vk.com/sdamex_ru" class="link">Вконтакте</a>
                <!-- <a href="" class="link">projects</a>
                <a href="" class="link">contacts</a> -->
            </div>
            <div class="menu_icon">
                <span class="icon"></span>
            </div>
        </div>
    </nav>

    <section class="wrapper">

        <div class="container">

            <div id="scene" class="scene" data-hover-only="false">

                <div class="circle" data-depth="1.2"></div>

                <div class="one" data-depth="0.9">
                    <div class="content">
                        <span class="piece"></span>
                        <span class="piece"></span>
                        <span class="piece"></span>
                        <img src="/<?=Url::to('@imgFolder/wh-or-logo.svg')?>">
                    </div>
                </div>

                <div class="two" data-depth="0.60">
                    <div class="content">
                        <span class="piece"></span>
                        <span class="piece"></span>
                        <span class="piece"></span>
                    </div>
                </div>

                <div class="three" data-depth="0.40">
                    <div class="content">
                        <span class="piece"></span>
                        <span class="piece"></span>
                        <span class="piece"></span>
                    </div>
                </div>

                <p class="code" data-depth="0.50"><?= $code ?></p>
                <p class="code" data-depth="0.10"><?= $code ?></p>

            </div>

            <div class="text">
                <article>
                    <p><?= nl2br(Html::encode($message)) ?></p>
                    <?php if ($btnView) : ?>
                    <button onclick="history.back();">Вернуться!</button>
                    <?php endif; ?>
                </article>
            </div>

        </div>
    </section>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<script type="text/javascript">
    // Parallax Code
    var scene = document.getElementById('scene');
    var parallax = new Parallax(scene);
</script>
