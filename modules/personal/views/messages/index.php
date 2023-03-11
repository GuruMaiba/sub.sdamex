<?php
use yii\helpers\Url;
$this->registerCssFile( "/css/jquery.mCustomScrollbar.min.css", ['rel'=>'stylesheet'], 'srollbarCSS' );
?>

<div class="page pageMessages">
    <div class="rooms">
        <div class="mobileBtn"><i class="disc icon-comment-discussion"></i><i class="close icon-cancel"></i></div>
        <div class="search">
            <input class='searchDialog' type="text" placeholder="Поиск">
            <div class="icon"><i class="icon-android-search"></i></div>
        </div>
        <div class="item create<?= ($activeId > 0) ? '' : ' active'?>">
            <div class="bg"></div>
            <div class="nick">Создать диалог</div>
            <div class="plus"><i class="icon-plus-round"></i></div>
        </div>
        <div class="dialogs">
            <?= $this->render('_dialog', [
                    'model' => $dialogs,
                    'activeId' => $activeId,
                    'messages' => $messages
                ]) ?>
        </div>
    </div>
    <div class="chat<?= ($activeId > 0) ? '' : ' create' ?>">
        <div class="info">
            <div class="interlocutor">
                <a class="user" href="/personal/profile/<?=$otherUser['id']?>">
                    <div class="ava"> <img src="<?= ($activeId > 0) ? Url::to("@uAvaSmall/$otherUser[ava]") : Url::to("@uAvaSmall/no_img.jpg")?>"> </div>
                    <span class="nick"><?= ($activeId > 0) ? $otherUser['username'] : '-//-' ?></span>
                </a>
                <input class="inputId" type="hidden" name="user_id">
                <input class="inputNick" type="text" name="user_nick" placeholder="ВВЕДИТЕ НИК ПОЛЬЗОВАТЕЛЯ" autocomplete="off">
                <div class="list"></div>
            </div>
        </div>
        <div class="startChat">
            <div class="btn disable">
                <div class="icon"><i class="icon-mail-1"></i></div>
                <div class="txt">СОЗДАТЬ ДИАЛОГ С ПОЛЬЗОВАТЕЛЕМ <br> <span class="nick">...</span></div>
            </div>
        </div>
        <div class="messages">
            <?php
            if ($activeId > 0)
                echo $this->render('_messages', [
                    'otherUser' => $otherUser,
                    'messages' => $messages,
                    'type' => 'old',
                ]);
            ?>
        </div>
        <div class="bottom">
            <div class="alignment">
                <div class="write">
                    <input class="dialogId" type="hidden" name="dialog_id">
                    <textarea class="textarea" name="message_text" placeholder="Введите Ваше сообщение..."></textarea>
                    <div class="send">
                        <i class="icon-email-plane"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$csrf = Yii::$app->getRequest()->getCsrfToken();

$js = <<<JS
    let csrf = '$csrf';
    let dialogId = 0;                   // идентификатор диалога
    let listener = null;                // слушатель
    let delay = 1000;                   // задержка слушателя
    let isActive = $activeId;           // активен ли диалог
    let link = '/personal/messages';    // ссылка для ajax

    $(window).on("load",function(){
        $(".rooms .dialogs").mCustomScrollbar( {
            theme: "dark-thin",
            scrollInertia: 300,
            autoHideScrollbar: true,
            scrollbarPosition: "outside",
        });
    });
    
    $(document).ready(function () {
        if (isActive) {
            dialogId = isActive;
            startListener();
            $(document).scrollTop($(document).height());
        }

        // Поиск диалога
        // ----------------------------------
        $('.searchDialog').keyup(function () {
            let val = $(this).val();
                val = val.toLowerCase();
            if (val != '') {
                $('.rooms .item').each(function (i,e) {
                    let nick = $(this).children('.nick').text();
                        nick = nick.toLowerCase(); 
                    if (nick.indexOf(val) == -1 && !$(this).hasClass('create'))
                        $(this).addClass('hidden');
                    else
                        $(this).removeClass('hidden');
                });
            } else {
                $('.rooms .item').removeClass('hidden');
            }
        });

        // Нажатие на диалог
        // ----------------------------------
        $('.rooms').on('click', '.item', function ( event ) {
            if (!$(this).hasClass('active') && event.target.className.indexOf('iconDel') == -1) {
                $('.rooms .item').removeClass('active');
                $(this).addClass('active');

                if ($(this).hasClass('create')) {
                    $('.chat').addClass('create');
                    stopListener();
                } else {
                    dialogId = $(this).attr('numb');
                    $('.chat .info .user').attr('href', '/personal/profile/'+$(this).attr('userId'));
                    $('.chat .info .ava img').attr( 'src', $(this).children('img').attr('src') );
                    $('.chat .info .nick').text( $(this).children('.nick').text() );

                    $.post(link+'/view-messages', {'_csrf': csrf, 'id': dialogId, 'viewedMess': 0})
                        .done(function ( req, status, jqXHR ) {
                            if (req.error === undefined) {
                                $('.chat .messages').html(req.messages);
                                $('.chat').removeClass('create');
                                $(document).scrollTop($(document).height());
                                updateView();
                                stopListener();
                                startListener();
                            } else {
                                ajaxError(req.error, jqXHR);
                                stopListener();
                            }
                        }).fail(function( jqXHR, status, errorThrown ){
                            ajaxError(errorThrown, jqXHR);
                        });
                }

                $(this).parents('.rooms').removeClass('active');
            }
        });

        // Удаление диалога
        // ----------------------------------
        $('.rooms').on('click', '.item .del', function () {
            let parent = $(this).parent();

            if (confirm('Вы уверены, что хотите удалить этот диалог?')) {
                $.post(link+'/del-dialog', {'_csrf': csrf, 'id': parent.attr('numb')})
                    .done(function ( req, status, jqXHR ) {
                        if (req == 1)
                            parent.remove();
                        else
                            ajaxError(status, jqXHR);
                    }).fail(function( jqXHR, status, errorThrown ){
                        ajaxError(errorThrown, jqXHR);
                    });
            }
        });

        // Вывод найденных пользователей
        // ----------------------------------
        $('.chat .info .inputNick').keyup(function () {
            let val = $(this).val();
            let list = $('.chat .info .list');
            if (val.length > 2) {
                $.post( link+'/user-list', {'_csrf': csrf, 'search': val})
                    .done(function ( req, status, jqXHR ) {
                        if (req !== 0) {
                            list.html(req);
                            list.addClass('active');
                        }
                    }).fail(function( jqXHR, status, errorThrown ){
                        ajaxError(errorThrown, jqXHR);
                    });
            } else {
                list.removeClass('active');
                $('.chat .info .inputId').val(0);
                $('.startChat .btn').addClass('disable');
                $('.startChat .btn .nick').text('...');
            }
        });

        // Выбор собеседника
        // ----------------------------------
        $('.chat .info .list').on('click', '.item', function () {
            if ($(this).hasClass('default'))
                return false;
            
            let txt = $(this).text();
            $('.chat .info .inputId').val($(this).attr('numb'));
            $('.chat .info .inputNick').val(txt);
            $(this).parent().removeClass('active');
            $('.startChat .btn').removeClass('disable');
            $('.startChat .btn .nick').text(txt);
        });

        // Создание диалога
        // ----------------------------------
        $('.startChat .btn').click(function () {
            if ($(this).hasClass('disable'))
                return false;

            let id = $('.chat .info .inputId').val();
            $.post(link+'/create-dialog', {'_csrf': csrf, 'user_id': id})
                .done(function( req, status, jqXHR ) {
                    // console.log(req);
                    if (req.type == 'error') {
                        globalError(req.message);
                    } else if (req.type == 'success') {
                        $('.dialogs').prepend(req.cont);
                        $('#dialog-'+req.id).click();
                    } else if (req.type == 'isset') {
                        $('#dialog-'+req.id).click();
                    }
                }).fail(function( jqXHR, status, errorThrown ){
                    ajaxError(errorThrown, jqXHR);
                });
        });

        // Открыть меню диалогов
        // ----------------------------------
        $('.rooms .mobileBtn').click(function () {
            $(this).parent().toggleClass('active');
        });

        // Слушатель клавиши Enter
        // ----------------------------------
        $('.bottom .write .textarea').keydown(function (e) {
            if (e.which == 13 ) { // e.ctrlKey && 
                e.preventDefault();
                $('.bottom .write .send').click();
            }
        });

        // Отправка сообщения
        // ----------------------------------
        $('.bottom .write .send').click(function () {
            let lastMess = $('.messages .message:last');
            let userId = lastMess.attr('user');
            let date = lastMess.attr('date');

            if (userId === undefined) {
                userId = 0;
                date = 0;
            }

            let text = $('.bottom .textarea').val();
            if (text == '' || dialogId == 0)
                return false;
                
            $.post(link+'/add-message', {
                '_csrf': csrf,
                'id': dialogId,
                'lastUser': userId,
                'lastDate': date,
                'text': text
                }).done(function( req, status, jqXHR ) {
                    console.log(req);
                    if (req.error === undefined) {
                        $('.bottom .textarea').val('');
                        $('.messages').append(req.message);
                        $(document).scrollTop($(document).height());
                    } else {
                        globalError();
                    }
                }).fail(function( jqXHR, status, errorThrown ){
                    ajaxError(errorThrown, jqXHR);
                });
        });
    });

    // Создание слушателя
    function startListener() {
        listener = setTimeout(listenerNewMess, delay);
    }

    // Удаление слушателя
    function stopListener() {
        clearTimeout(listener);
        delay = 1000;
    }

    // Функция слушателя новых сообщений
    function listenerNewMess() {
        let lastMess = $('.messages .message:last');
        let id = lastMess.attr('numb');
        let userId = lastMess.attr('user');
        let date = lastMess.attr('date');
        if (id === undefined) {
            id = 0;
            userId = 0;
            date = 0;
        }

        $.post(link+'/check-message', {'_csrf': csrf, 'id': dialogId, 'lastId': id, 'lastUser': userId, 'lastDate': date})
            .done(function (req) {
                if (req != '0' && req != '-1') {
                    $('.message.notViewed').removeClass('notViewed');
                    $('.messages').append(req);
                    $(document).scrollTop($(document).height());
                    updateView();
                }
                startListener()
            }).fail(function() {
                delay *= 2;
                startListener()
            });
    }

    function updateView() {
        $.post(link+'/update-view', {'_csrf': csrf, 'id': dialogId});
    }
JS;

$this->registerJs($js);
$this->registerJsFile( '@scrLibs/jquery.mCustomScrollbar.concat.min.js', ['depends' => ['app\assets\PersonalAsset'],], 'scrollbarJS' );