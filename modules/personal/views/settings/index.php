<?php
use yii\helpers\{Html, Url};
use app\components\CodeType;
use app\models\promoter\Code;
use app\models\course\Course;
use app\models\webinar\Webinar;

$this->registerCssFile( "/css/jquery.Jcrop.css", ['rel'=>'stylesheet'], 'jcropCSS' );

$isPromoter = Yii::$app->user->can('promoter');
?>

<div class="page pageSettings">
    <div class="pTitle">НАСТРОЙКИ</div>

    <div class="crossPhoto">
        <form id="coords">
            <input type="hidden" name="Coords[X]" id="X" value="0" />
            <input type="hidden" name="Coords[Y]" id="Y" value="0" />
            <input type="hidden" name="Coords[W]" id="W" value="250" />
            <input type="hidden" name="Coords[H]" id="H" value="250" />
        </form>
        <div class="crossPhotoImg">
            <img id="cropbox" src="" />
            <div class="btn avaCancel"><i class="icon-cancel"></i></div>
            <div class="btn avaClip"><i class="icon-ios-checkmark-empty"></i></div>
        </div>
    </div>

<form id="settingForm" method="post" action="personal/settings">
    <?=Html::hiddenInput('_csrf', Yii::$app->getRequest()->getCsrfToken(), []);?>
    <div class="themeBlock main">
        <div class="delimiter">
            <div class="icon"> <i class="icon-user"></i> </div>
            <div class="name">ОБЩИЕ</div>
        </div>
        <div class="ava">
            <img src="<?=Url::to("@uAvaLarge/".$model->ava)?>">
            <input type="file" name="Ava" id="ava" class="inputfile" accept="image/png, image/x-png, image/jpeg, image/jpg" />
            <label for="ava"><i class="icon-download"></i></label>
        </div>
        <div class="allName">
            <div class="settInput username">
                <input type="text" name="UserSettings[username]" value="<?= $model->username ?>" placeholder="Agent007" required autocomplete="off">
                <label>Nickname</label>
                <div class="line"></div>
            </div>
            <div class="settInput first_name">
                <input type="text" name="UserSettings[first_name]" value="<?= $model->name ?>" placeholder="Джеймс" required autocomplete="on">
                <label>Имя</label>
                <div class="line"></div>
            </div>
            <div class="settInput last_name">
                <input type="text" name="UserSettings[last_name]" value="<?= $model->surname ?>" placeholder="Бонд" required autocomplete="on">
                <label>Фамилия</label>
                <div class="line"></div>
            </div>
        </div>
    </div>

    <div class="themeBlock protection">
        <div class="delimiter">
            <div class="icon">
                <i class="icon-lock"></i>
            </div>
            <div class="name">БЕЗОПАСНОСТЬ</div>
        </div>
        <div class="connect">
            <div class="settInput email">
                <input type="text" name="UserSettings[email]" value="<?= $model->email ?>" placeholder="example@sdamex.ru" required autocomplete="on">
                <label>E-mail</label>
                <div class="line"></div>
            </div>
            <div class="settInput phone">
                <input type="text" name="UserSettings[phone]" value="<?= $model->phone ?>" placeholder="+7 (911) 111-11-11" required autocomplete="on">
                <label>Телефон</label>
                <div class="line"></div>
            </div>
        </div>
        <div class="password">
            <?php if ($model->password_hash) : ?>
            <div class="settInput old_pass">
                <input type="password" name="UserSettings[old_pass]" placeholder="Введите текущий пароль..." required autocomplete="on">
                <label>Текущий пароль</label>
                <div class="line"></div>
            </div>
            <?php endif; ?>
            <div class="settInput new_pass">
                <input type="password" name="UserSettings[new_pass]" placeholder="Введите новый пароль..." required autocomplete="off">
                <label>Новый пароль</label>
                <div class="line"></div>
            </div>
            <div class="settInput retype_pass">
                <input type="password" name="UserSettings[retype_pass]" placeholder="Повторите новый пароль..." required autocomplete="off">
                <label>Повтор пароля</label>
                <div class="line"></div>
            </div>
        </div>
    </div>

    <div class="themeBlock invite">
        <div class="delimiter">
            <div class="icon"> <i class="icon-users"></i> </div>
            <div class="name">ИНВАЙТ</div>
        </div>

        <?php if ($isPromoter) : ?>
        <div class="promoter">
            <div class="balance">Баланс: <span class="money"><?= $model->cash ?></span></div>
            <div class="promFooter">
                <?php if (!empty($promoter['invited'])) : ?>
                <div class="invited">
                    <div class="title">ПРИВЛЕЧЁННЫЕ УЧЕНИКИ</div>
                    <div class="list">
                        <?
                            foreach ($promoter['invited'] as $user) : 
                                $cPay = count($user['pay']);
                        ?>
                        <div class="item<?= ($cPay > 0) ? ' active' : '' ?>"><?=$user['username']?> - <?=date('d.m.Y',$user['created_at'])?> (<?=$cPay?>)</div>
                        <? endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($promoter['codes'])) : ?>
                <div class="codes">
                    <div class="title">МОИ ПРОМОКОДЫ</div>
                    <div class="list">
                        <? foreach ($promoter['codes'] as $code) : ?>
                        <span class="item"><?=$code?></span>
                        <? endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php else : ?>
        <div class="code">
            <?php if ($model->invite_code != 'SPENT') : ?>
                <?php if (empty($invite)) : ?>
                <div class="settInput invite_code">
                    <input type="text" name="UserSettings[invite_code]" placeholder="SDAMEX_2020" required autocomplete="off">
                    <label>Инвайт-код</label>
                    <div class="line"></div>
                </div>
                <?php
                    elseif ($invite['end_at'] > time()) :
                    $props = CodeType::getPropsArr();
                    $props = $props[$invite['type']];
                ?>
                <div class="name"><span class="paramount">Инвайт-код |</span> <span class="set"><?=$invite['code']?></span></div>
                <ul class="buns">
                    <hr>
                    <?php
                        // Скидон в процентах
                        if ($props['sale_percent'] > 0)
                            echo "<li>Скидка на покупку курсов: $props[sale_percent]%</li>";

                        // Скидон в рублях
                        if ($props['sale_percent'] > 0) 
                            echo "<li>Скидка на покупку курсов: $props[sale_cost] рублей</li>";

                        // Бесплатные материалы
                        $access = $props['free_access'];
                        if ($access['lessons'] > 0 || $access['courses'] != [] || $access['webinars'] != []) {
                            echo "<li class='present'>Материалы в подарок</li>";
                            
                            // Бесплатные уроки
                            if ($access['lessons'] > 0)
                                echo "<li class='free'>Уроки с учителем: $access[lessons] шт.</li>";

                            // Бесплатные курсы
                            if ($access['courses'] != []) {
                                $courses = Course::find()->select(['id','subject_id','title'])
                                    ->where(['in', 'id', $access['courses']])
                                    ->andWhere(['publish'=>1])->asArray()->all();
                                foreach ((array)$courses as $course) {
                                    $sub = Yii::$app->params['listSubs'][$course['subject_id']];
                                    if ($sub['isActive'])
                                        echo "<li class='free'>Курс: <a href='$sub[link]course/$course[id]' target='_blank'>$course[title]</a></li>";
                                }
                            }

                            // Бесплатные вебинары
                            if ($access['webinars'] != []) {
                                $webinars = Webinar::find()->select(['id','subject_id','title'])
                                    ->where(['in', 'id', $access['webinars']])
                                    ->andWhere(['publish'=>1])->asArray()->all();
                                foreach ((array)$webinars as $webinar) {
                                    $sub = Yii::$app->params['listSubs'][$webinar['subject_id']];
                                    if ($sub['isActive'])
                                        echo "<li class='free'>Вебинар: <a href='$sub[link]webinar/$webinar[id]' target='_blank'>$webinar[title]</a></li>";
                                }
                            }
                        }
                    ?>
                </ul>
                <?php else : ?>
                <div class="default">Инвайт-код просрочен!</div>
                <?php endif; ?>
            <?php else : ?>
                <div class="default">Инвайт-код использован!</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</form>

    <div class="save" title="Применить изменения">
        <div class="notif"> <ul></ul> </div>
        <div class="btn">
            <i class="icon-floppy"></i>
        </div>
    </div>
</div>

<?php
$csrf = Yii::$app->getRequest()->getCsrfToken();

$js = <<<JS
    var csrf = '$csrf';
    $(document).ready(function() {
        var backup = $(".crossPhoto").html();

        $('.phone input').mask('+0 (000) 000-00-00');

        $('#ava').change(function () {
            let file = this.files[0];
            let exp = file.type.toString().toLowerCase();

            if (file) {
                let reader = new FileReader();

                reader.addEventListener("load", function () {
                    $(".crossPhoto").html(backup);
                    let img = new Image;

                    img.onload = function () {
                        if (file.size < 10000000 && (exp.indexOf("image") !== -1)) {
                            if (img.height < 200 || img.width < 200) {
                                alert("Ваше изображение должно быть больше 200px со всех сторон!");
                            } else if (img.height == img.width) {
                                $('#X').val(0);
                                $('#Y').val(0);
                                $('#W').val(img.width);
                                $('#H').val(img.height);
                                $(".avaClip").click();
                            } else {
                                $("#cropbox").attr('src', reader.result);
                                $('#cropbox').Jcrop({
                                    onSelect: updateCoords,
                                    aspectRatio: 1 / 1,
                                    setSelect: [0, 0, 200, 200],
                                    boxHeight: $("#cropbox").height(),
                                    boxWidth: $("#cropbox").width()
                                });
                                $(".crossPhoto").addClass('show');
                            }
                        }
                    };
                    img.src = reader.result;
                }, false);

                reader.readAsDataURL(file);
            }
        });

        $("body").on("click", ".avaCancel", function () {
            $('.crossPhoto').removeClass('show');
            $('#ava').val(null);
            setTimeout(function () {
                $(".crossPhoto").html(backup);
            }, 500);
        });

        $("body").on("click", ".avaClip", function () {
            let th = $(this),
                file = $('#ava').prop('files')[0],
                data = new FormData(document.forms.coords); //document.forms.coords
                data.append('ava',file);
                data.append('_csrf',csrf);

            $.ajax({
                url : '/personal/settings/change-ava',
                type : 'POST',
                data : data,
                cache : false,
                dataType : 'json',
                processData : false, // отключаем обработку передаваемых данных
                contentType : false, // отключаем установку заголовка типа запроса
                // функция успешного ответа сервера
                success : function( data, status, jqXHR ){
                    // console.log(data);
                    // ОК - файлы загружены
                    if( typeof data.error === 'undefined' ){
                        $('#settingForm .ava img').attr('src', data.success.large);
                        $('.submenu .user .info .ava').attr('src', data.success.small);
                        showNotif('<li>Персональная фотография успешно изменена!</li>', false);
                    }
                    // ошибка
                    else {
                        showNotif('<li>'+data.error+'</li>', true);
                    }
                    $('.avaCancel').click();
                },
                // функция ошибки ответа сервера
                error : function( jqXHR, status, errorThrown ){
                    ajaxError(errorThrown, jqXHR);
                }

            });
        });

        $('.save .btn').click(function (e) {
            let th = $(this);
            if (th.hasClass('disable'))
                return false;
            
            th.addClass('disable');
            if (mainCheck()) {
                $('.settInput').removeClass('err');
                let data = $('#settingForm').serialize();
                let invite = $('.invite_code input').val();
                // console.log();

                $.post( '/personal/settings/change', data)
                    .done(function( data, status, jqXHR ) {
                        if ( typeof data.error === 'undefined' ) {
                            showNotif('<li>'+data.success+'</li>', false);
                            if (invite != undefined && invite.length > 0)
                                setTimeout(() => document.location.reload(), 3000);
                        } else {
                            let cont = '';
                            for (let key in data.error) {
                                $('.'+key).addClass('err');
                                cont += '<li>'+data.error[key]+'</li>';
                            }
                            showNotif(cont, true);
                        }
                    })
                    .fail(function( jqXHR, status, errorThrown ){
                        ajaxError(errorThrown, jqXHR);
                    });
            }
            setTimeout(() => th.removeClass('disable'), 3000);
        });

        // $('input[name=Email]').change(function () {
        //     var val = $(this).val(),
        //         lable = "<i class=\"icon-mail-1\"></i> E-mail";

        //     if ( (val.indexOf(dog) > -1 && val.indexOf(".") > -1) || val == "" )
        //         $(this).siblings(".inputLable").html(lable);
        //     else
        //         $(this).siblings(".inputLable").html(lable + " <span class='error'>Пример: gj@gmail.com</span>");
        // });
    });

    function updateCoords(c) {
        $('#X').val(c.x);
        $('#Y').val(c.y);
        $('#W').val(c.w - 1);
        $('#H').val(c.h - 1);
    };

    function mainCheck() {
        $('.notif').removeClass('show');
        return true;
    }

    function showNotif(cont, isErr) {
        let notif = $('.notif');
        notif.removeClass('show');
        if (isErr) {
            notif.addClass('err');
        } else {
            notif.removeClass('err');
            setTimeout(() => notif.removeClass('show'), 3000);
        }
        notif.children('ul').html(cont);
        notif.addClass('show');
    }
JS;

$this->registerJs($js);
$this->registerJsFile( '@scrLibs/jquery.Jcrop.js', ['depends' => ['app\assets\PersonalAsset'],], 'jcropJS' );
$this->registerJsFile( '@scrLibs/jquery.mask.min.js', ['depends' => ['app\assets\PersonalAsset'],], 'maskJS' );
?>