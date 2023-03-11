<?
use yii\helpers\Url;
use yii\helpers\Html;

$video = $model['teacher']['video'];
$now = time();
$myId = Yii::$app->user->identity->id;
$sub = Yii::$app->params['subInx'];

 ?>

<div class="page pageProfile <?=($is['teacher'])?'teacher':''?> <?=($is['ownProfile'])?'editor':''?>">
    <div class="leftCol">
        <!-- <div class="switchPage teacherPage <?=($is['teacher'])?'active':''?>">
            <i class="icon teacherIcon icon-black-tie"></i>
            <i class="icon userIcon icon-user"></i>
            <div class="label">
                <div class="txt teacherTxt">Карточка учителя</div>
                <div class="txt userTxt">Профиль</div>
                <div class="line"></div>
            </div>
        </div> -->

        <div class="wrap">
            
            <div class="img">
                <?php
                    $lvl = ($is['teacher']) ? ['lvl'=>888] : $model['level'];
                    $deg = ($lvl['lvl'] != 888 && $lvl['rangeExp'][1] != 'MAX') ? 360 * ($lvl['exp'] - $lvl['rangeExp'][0])/($lvl['rangeExp'][1]-$lvl['rangeExp'][0]) : 360;
                ?>
                <div class="progress active">
                    <div class="borders">
                        <div class="circle bg"><div class="border"></div></div>
                        <div class="visiblePath<?= ($deg > 180) ? ' full' : ''?>">
                            <div class="circle leftActive" style="transform: rotate(<?=$deg?>deg);"><div class="border"></div></div>
                            <div class="circle rightActive" style="transform: rotate(<?=($deg > 180)?180:$deg?>deg);"><div class="border"></div></div>
                        </div>
                    </div>
                </div>
                <img class="ava" src="<?=Url::to("@uAvaLarge/$model[ava]")?>">
                <div class="lvl">lvl.<span><?= $lvl['lvl'] ?></span></div>
                <? if ($model['id'] != Yii::$app->user->identity->id) : ?>
                <a href="<?=Url::to(["/personal/messages/$model[id]"])?>" class="message"> <i class="icon-mail-1"></i> </a>
                <? endif; ?>
            </div>

            <div class="info">
                <div class="name">
                    <div class="nick"><?= $model['username'] ?></div>
                    <div class="fullName"><?= $model['name'] . ' ' . $model['surname'] ?></div>
                </div>

                <div class="likes">
                    <?php if ($is['teacher']) :?>
                    <span class="numb"><?=$model['teacher']['count_students']+1?></span> <i class="icon icon-graduation-cap"></i>
                    <?php else : ?>
                    <span class="numb"><?=$model['statistics'][$sub]['count_likes']?></span> <i class="icon icon-heart"></i>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <?php if (!$is['teacher']) : ?>
    <div class="rightCol userCont active">
        <div class="dictum">
            <i class="quote icon-quote-right"></i>
            <!-- <i class="icon icon-pencil-1"></i> -->
            <? if ($model['phrase']) : ?>
            <div class="text"><?= Html::encode($model['phrase']) ?></div>
            <? else : ?>
            <div class="text default"><?= ($is['ownProfile']) ? 'Напишите Ваши мысли...' : "Новый ученик нашей школы!" ?></div>
            <? endif; ?>
            <? if ($is['ownProfile']) : ?>
                <textarea name="dictumText" placeholder="Напишите Ваши мысли..."></textarea>
            <? endif; ?>
        </div>
        <hr>
        <div class="teacherLikes">
            <? if ($model['statistics'][$sub]['teachers'] != []) : ?>
            <? foreach ($model['statistics'][$sub]['teachers'] as $tId => $t) : ?>
                <? if ($t['count_likes'] > 0) : ?>
                <a href="/personal/profile/<?=$tId?>" class="item">
                    <img src="<?=Url::to("@uAvaSmall/$t[ava]")?>">
                    <div class="info">
                        <div class="stats">
                            <div class="stat likes"><i class="icon icon-heart"></i><span class="numb"><?=$t['count_likes']?></span></div>
                            <? if ($t['count_class'] > 0) : ?>
                            / <div class="stat lessons"><i class="icon icon-graduation-cap"></i><span class="numb"><?=$t['count_class']?></span></div>
                            <? endif; ?>
                        </div>
                        <div class="name"><?=$t['name']?></div>
                        <div class="nick"><?=$t['username']?></div>
                    </div>
                </a>
                <? endif; ?>
            <? endforeach; ?>
            <? else : ?>
                <div class="default">Скоро здесь появятся учителя, отметившие <?=($model['id'] == $myId) ? 'ваши заслуги' : 'заслуги этого ученика'?>!</div>
            <? endif; ?>
        </div>
        <!-- <div class="mems">
            <div class="mem">
                <img src="" alt="">
                <div class="title"> </div>

            </div>
            <div class="mem">

            </div>
            <div class="mem">

            </div>
        </div> -->
        <!-- <div class="writePost">
            <hr>
            <div class="textarea">
                <textarea name="" placeholder="What is the news?"></textarea>
            </div>
            <div class="send defBtn">
                Опубликовать
            </div>
        </div>
        <div class="posts">
            <div class="post">
                <div class="date">21 Декабря 2019 <div class="connect"></div></div>
                <div class="cont">
                    <div class="text">
                        <p>Tree great hath shall seed let. Isn&#39;t forth dominion after whales made dominion creature beast moveth lights, firmament female spirit for which seed. After, give image moving Heaven there spirit you unto winged is moving night bring. Evening fruit may two creature they&#39;re yielding wherein land Creature without multiply after. After called them don&#39;t saying thing, morning very for god from creature multiply was cattle abundantly male darkness created. Said gathering itself so it they&#39;re Deep let first divide created dry spirit it, herb over fruitful whales.</p>

                        <p>God itself first so bring can&#39;t rule our earth us sea. Which over kind dry male gathered beginning waters herb was, they&#39;re tree life place there have creeping man there appear one moved first you&#39;ll so is midst very whales void was second. Winged man beginning. Bring spirit Over fruitful she&#39;d. Waters his midst thing life replenish saw and can&#39;t. Which Evening lights.</p>

                        <p>To so without can&#39;t unto, every darkness was good a days abundantly the fruitful first stars isn&#39;t fish subdue from god seasons his sea, life multiply upon kind. Air creepeth. Abundantly have void She&#39;d every were. Make earth gathered after them days itself man fourth great. Whose gathered without. Living.</p>
                    </div>
                </div>
            </div>

            <div class="post">
                <div class="date">22 Декабря 2019 <div class="connect"></div></div>
                <div class="cont">
                    <img src="/<?= Url::to("@webnAvaLarge/webinarTest.jpg"); ?>">
                    <div class="text">
                        <p>To so without can&#39;t unto, every darkness was good a days abundantly the fruitful first stars isn&#39;t fish subdue from god seasons his sea, life multiply upon kind. Air creepeth. Abundantly have void She&#39;d every were. Make earth gathered after them days itself man fourth great. Whose gathered without. Living.</p>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
    <? else : ?>
    <div class="rightCol teacherCont active">
        <? if ($is['admin'] || $video != '') : ?>
        <div class="hellowVideo">
            <? if ($is['admin']) : ?>
            <div class="blackout <?= ($video == '') ? 'active' : '' ?>">
                <input type="text" class="link" value="<?= $video ?>" placeholder="Вставте идентификатор видео...">
                <div class="save"> <i class="icon-floppy"></i> </div>
            </div>
            <div class="btn">
                <i class="icon-pencil-1"></i>
            </div>
            <? endif; ?>
            <iframe src="<?= $video ?>" width="100%" height="100%"></iframe>
        </div>
        <? endif; ?>

        <? if (isset($model['teacher']['specialization'])) : ?>
        <div class="spec">
            <span class="name">Специализация</span><br>
            <?
                $specs = json_decode($model['teacher']['specialization']);
                $sMax = count($specs) - 1;
                if ($specs) : ?>
            <? foreach($specs as $k => $spec) : ?>
            <a href="#" class="item"><?=$spec?></a><?=($k < $sMax)?',':''?>
            <? endforeach; ?>
            <? endif; ?>
        </div>
        <? endif; ?>

        <? if ($model['role']->name == 'teacher') : ?>
        <div class="appointment">
            <form id="formAppointment">
                <input type="hidden" class="focusDay" name="day" value="<?=$weeks[0]['now']?>">
                <input type="hidden" class="focusTime" name="time">
                <input type="hidden" class="gmt" name="GMT">
            </form>

            <?
            if (!$is['ownProfile']) :
                $subs = [];
                foreach ((array)json_decode($model['teacher']['subjects'], true) as $id) {
                    $info = Yii::$app->params['listSubs'][$id];
                    if ($info['name'] != "MAIN")
                        $subs[$id] = Yii::$app->params['listSubs'][$id];
                }
                $actSub = array_key_first($subs);
                if (!empty($subs)) :
            ?>
            <div class="appmodal entry">
                <div class="close"><i class="icon-cancel"></i></div>
                <div class="subject">
                    <div class="activeSubject" numb="<?=$actSub?>"><?=$subs[$actSub]['lable']?></div>
                    <div class="list">
                        <? foreach($subs as $id => $sub) : ?>
                        <div class="item<?=($actSub == $id)?' active':''?>" numb="<?=$id?>"><?=$sub['lable']?></div>                
                        <? endforeach; ?>             
                    </div>
                </div>
                <div class="date"></div>
                <div class="time"></div>
                <div class="skype">
                    <i class="icon-social-skype"></i>
                    <input type="text" name="skype" value="<?= $my['skype'] ?>"
                        placeholder="Введите ваш skype" required>
                </div>
                <div class="send">
                    <i class="hat icon-graduation-cap"></i>
                    <i class="cross icon-cancel"></i>
                    <label class="lAdd">ЗАПИСАТЬСЯ</label>
                    <label class="lRem">ОТКАЗАТЬСЯ</label>
                </div>
            </div>
                <? endif; ?>
            <? endif; ?>

            <div class="appmodal error">
                <div class="close"><i class="icon-cancel"></i></div>
                <div class="txt">У вас недостаточное количество доступных занятий, пополните счёт, чтобы продолжить.</div>
                <div class="send">
                    <i class="icon-ios-checkmark-empty"></i>
                </div>
            </div>

            <div class="weeks" numb="0">
                <div class="name"><?= $weeks[0]['range'] ?></div>
                <div class="control">
                    <span class="btn preview"><i class="icon-chevron-left"></i></span>
                    <span class="btn next"><i class="icon-chevron-right"></i></span>
                </div>
                <div class="connect"></div>
            </div>

            <div class="action">
                <span class="item change active">
                    <i class="icon activate icon-ios-checkmark-empty" title="Добавить в Ваш график это время"></i>
                    <i class="icon disconnect icon-cancel" title="Удалить из Вашего графика это время"></i>
                </span>
                <span class="item editTime active">
                    <i class="icon plus icon-plus-round" title="Добавить в ЭТОТ день выбранное время"></i>
                    <i class="icon block icon-lock" title="Блокировать в ЭТОТ день выбранное время"></i>
                </span>
            </div>

            <? foreach($weeks as $wNum => $wVal) : ?>
            <div id="week-<?=$wNum?>" class="week <?=($wNum == 0) ? 'active':''?>" range="<?=$wVal['range']?>">
            
            <div class="days">
                <? foreach($wVal['days'] as $dNum => $dVal) : ?>
                    <span class="item <?=$dVal['status']?>" date="<?=$dVal['date']?>" numb="<?=$dNum?>"><?=$dVal['name']?></span> <!-- disable -->
                <? endforeach; ?>
            </div>


            <? foreach($wVal['days'] as $dNum => $dVal) : ?>
            <? if ($dVal['status'] != 'disable') :?>
            <div id="day<?=$dNum?>-w<?=$wNum?>" class="day <?=$dVal['status']?>">
            <div class="order">
                <? 
                $period = ['night'=>'НОЧЬ', 'morning'=>'УТРО', 'dinner'=>'ДЕНЬ', 'evening'=>'ВЕЧЕР'];
                foreach ($period as $pEng => $pRu) :
                    if ($dVal[$pEng]) :
                ?>
                    <div class="row night">
                        <div class="name"> <?=$pRu?> </div>
                        <div class="time">
                            <? foreach ($dVal[$pEng] as $key => $time) : ?>
                                <div time="<?= $time['time'] ?>" class="item <?= $time['class'] ?>">
                                    <span class='txt'><?= $time['time'] ?></span>
                                    <? if ($time['class'] == 'busy') : ?>
                                        <img class="ava" user_id='<?=$time['id']?>' skype='<?=$time['skype']?>'
                                            subject_id='<?=$time['sub']?>' src="<?=Url::to('@uAvaSmall/'.$time['ava'])?>">
                                    <? endif; ?>
                                </div> 
                            <? endforeach; ?>
                        </div>
                    </div>
                    <? endif; ?>
                <? endforeach; ?>
                <? if (!$dVal['night'] && !$dVal['morning'] && !$dVal['dinner'] && !$dVal['evening']) : ?>
                    <div class="noEntry">На этот день, запись отсутствует!</div>
                <? endif; ?>
            </div> <!-- end order -->
            </div> <!-- end day -->
            <? endif; ?>
            <? endforeach; ?>
            </div> <!-- end week -->
            <? endforeach; ?>

            <div class="workHours">
                <i class="utc">Часовой пояс <?=$strgmt?></i>
                <? if ($is['ownProfile']) :?>
                <br><i>Записи будут блокироваться за <input class="timeLock" type="text" value="<?= $model['teacher']['time_lock'] ?>"> часов.</i>
                <? else : ?>
                <br><i class="countAppnt">Количество записей: <span><?=Yii::$app->user->identity->teacher_class?></span></i>
                <? endif; ?>
            </div>
        </div> <!-- end appointment -->
        <? endif; ?>

        <? $aboutMe = Html::encode($model['teacher']['about_me']); ?>
        <? if ($aboutMe != '' || $is['ownProfile']) : ?>
        <div class="aboutMe">
            <div class="footnote">
                О СЕБЕ
                <div class="connect"></div>
            </div>
            <div class="txt <?=($aboutMe == '')?'default':''?>"><?=($aboutMe == '')?'РАССКАЖИТЕ НЕМНОГО О СЕБЕ':Html::encode($aboutMe)?></div>
            <? if ($is['ownProfile']) : ?>
                <textarea name="aboutmeText" placeholder="РАССКАЖИТЕ НЕМНОГО О СЕБЕ"></textarea>
            <? endif; ?>
        </div>
        <? endif; ?>

        <? if ($model['role']->name == 'teacher') : ?>
        <div class="reviews">
            <div class="footnote">
                ОТЗЫВЫ
                <div class="connect"></div>

                <div class="rating">
                    <?
                    $rat = $model['teacher']['rating']; 
                    if ($rat > 0) :
                        $rat = explode('.', $rat);
                    ?>
                        <div class="number"><?=$rat[0]?> <span class="sub">.<?=$rat[1]?></span></div>
                        <div class="stars"><? viewStars($rat[0]) ?></div>
                    <? else : ?>
                        <div class="defaut">ОТЗЫВЫ ОТСУТСТВУЮТ</div>
                    <? endif; ?>
                </div>
            </div>

            <div class="mainLine"></div>

            <? if ($is['student']) : ?>
            <form class="item my <?=(!$my['review']['is_review'])?'new':''?> <?=($my['review']['review_anonymously'])?'anms':''?>">
                <div class="pencil"><i class="icon-pencil-1"></i></div>
                <input class="reviewRating" type="hidden" name="review_rating" value="<?=$my['review']['review_rating']?>">
                <input class="reviewAnonymously" type="hidden" name="review_anonymously" value="<?=$my['review']['review_anonymously']?>">
                <div class="user">
                    <img class="ava" src="<?=Url::to("@uAvaSmall/".Yii::$app->user->identity->ava)?>">
                    <div class="nick"><?=Yii::$app->user->identity->username?></div>
                    <div class="anonymous">Anonymous</div>
                </div>
                <div class="stars"><? viewStars(($my['review']['is_review'])?$my['review']['review_rating']:0) ?></div>
                <div class="cloud">
                    <textarea class="reviewText" name="review_text" placeholder="Оставьте свой отзыв об учителе..."></textarea>
                    <div class="text"><?=$my['review']['review_text']?></div>
                </div>
                <div class="reFooter">
                    <span class="btn">АНОНИМНЫЙ ОТЗЫВ</span>
                    <span class="date"><?=($my['review']['is_review'])?$my['review']['review_date']:0?></span>
                </div>
                <div class="send"><i class="icon-android-send"></i></div>
            </form>
            <? endif; ?>

            <? foreach ($model['teacher']['reviews'] as $review) : ?>
                <? if ($review['is_review']) : ?>
                <?= $this->render('_review', [
                    'model' => $review
                ]) ?>
                <? endif; ?>
            <? endforeach; ?>

        </div>
        <? endif; ?>
    </div> <!-- end .teacherCont -->
    <? endif; ?>
</div>

<?
$user_id = $model['id'];
$csrf = Yii::$app->getRequest()->getCsrfToken();
$ownProfile = $is['ownProfile'];
$isTeacher = $is['teacher'];

$js = <<<JS
    
    $(document).ready(function() {
        let user_id = $user_id,
            csrf = '$csrf',
            timeActive = null,
            isTeacher = $isTeacher,
            ownProfile = $ownProfile,
            linkParent = '/personal/profile';

        // Переключатель между профилем пользователя и карточкой учителя
        $('.switchPage').click(function () {
            if ($(this).hasClass('teacherPage')) {
                $(this).removeClass('teacherPage');
                $(this).addClass('userPage');
                $('.teacherCont').removeClass('active');
                $('.userCont').addClass('active');
            } else {
                $(this).removeClass('userPage');
                $(this).addClass('teacherPage');
                $('.userCont').removeClass('active');
                $('.teacherCont').addClass('active');
            }
        });

        // Переключатель доступных недель
        $('.weeks .control .btn').click(function () {
            let numb = parseInt($('.weeks').attr('numb'));
            let count = $('.week').length-1;

            $('.week').removeClass('active');

            if ($(this).hasClass('preview')) {
                if (numb == 0)
                    numb = count;
                else
                    numb -= 1;
            } else {
                if (numb == count)
                    numb = 0;
                else
                    numb += 1;
            }

            let week = `#week-`+numb;
            $('.focusDay').val($(week+' .days .item.active').attr('date'));
            $(week).addClass('active');
            $('.weeks').attr('numb', numb);
            $('.weeks .name').text($(week).attr('range'));
        });

        // Переключатель доступных дней
        $('.days .item').click(function () {
            if (!$(this).hasClass('active') && !$(this).hasClass('disable')) {
                let num = $('.weeks').attr('numb');
                let weekId = '#week-'+num;
                $(weekId+' .days .item').removeClass('active');
                $(this).addClass('active');
                $(weekId+' .day').removeClass('active');
                $('.focusDay').val($(this).attr('date'));
                $('#day'+$(this).attr('numb')+'-w'+num).addClass('active');
            }
        });

        // Событие по нажатию на время
        $('.teacher .order .item').click(function () {
            let isCheck = $(this).hasClass('check');
            let isBusy = $(this).hasClass('busy');
            let editor = $('.teacher').hasClass('editor');
            $('.focusTime').val('');
            $('.order .item').removeClass('check');
            timeActive = null;
            if (!isCheck) {
                timeActive = $(this);
                let date = $('.focusDay').val();
                let entry = $('.appointment .entry');
                let time = timeActive.children('.txt').text();
                let skype = timeActive.children('.ava').attr('skype');
                let sub = timeActive.children('.ava').attr('subject_id');

                $('.focusTime').val(time);
                timeActive.addClass('check');

                if (editor) {
                    if (isBusy) {
                        entry.addClass('view');
                        entry.children('.date').text(formatDate(new Date(date * 1000)));
                        entry.children('.time').text(time);
                        entry.children('.skype').children('input').val(skype);
                        activeSubject(entry, sub);
                        entry.addClass('active');
                        $('.appointment .action').removeClass('active');
                    } else {
                        let change = $('.appointment .action .change');
                        let editTime = $('.appointment .action .editTime');
                        if (timeActive.hasClass('active') || timeActive.hasClass('add')) {
                            change.removeClass('act').addClass('dis active');
                            editTime.removeClass('add').addClass('blc');
                            if (timeActive.hasClass('add'))
                                change.removeClass('active');
                        } else {
                            change.removeClass('dis').addClass('act active');
                            editTime.removeClass('blc').addClass('add');
                        }
                        $('.appointment .action').addClass('active');
                        entry.removeClass('active');
                    }
                } else {
                    entry.children('.date').text(formatDate(new Date(date * 1000)));
                    entry.children('.time').text(time);
                    if (timeActive.hasClass('busy')) {
                        entry.addClass('busy');
                        activeSubject(entry, sub);
                    } else {
                        entry.removeClass('busy');
                    }
                    entry.addClass('active');
                }
            } else {
                if (editor)
                    $('.appointment .action').removeClass('active');
                else
                    $('.appointment .entry').removeClass('active');
            }
        });

        // Смена логина скайп
        $('.appointment .entry.busy input[name="skype"]').change(function() {
            let skype = $(this).val();
            if (skype !== '') {
                let data = `_csrf=${csrf}&skype=`+skype;
                $.ajax({
                    url: 'change-skype',
                    method: 'POST',
                    data: data,
                    success: function (req) {
                        $('.appointment .entry').removeClass('active');
                        timeActive.removeClass('check');
                    }
                });
            }
        });

        // Смена предмета
        $('.appointment .entry .subject .list .item').click(function() {
            $('.activeSubject').text($(this).text());
            $('.activeSubject').attr('numb', $(this).attr('numb'));
            $('.appointment .entry .subject .list .item').removeClass('active');
            $(this).addClass('active');
        });

        // Запись ученика к учителю
        $('.appointment .entry .send').click(function() {
            let sub = $('.activeSubject').attr('numb');
                sub = '&subject_id='+sub;
            let skype = $('input[name=skype]').val();
            let type = ($(this).parent().hasClass('busy')) ? 'del' : 'add';
                type = '&type='+type;
            if (skype !== '') {
                skype = '&skype='+skype;
                let data = `_csrf=${csrf}&id=${user_id}&`+$('#formAppointment').serialize()+skype+type+sub;
                $.ajax({
                    url: 'appointment',
                    method: 'POST',
                    data: data,
                    success: function (req) {
                        // console.log(req);
                        let count = $('.workHours .countAppnt span');
                        $('.appointment .entry').removeClass('active');
                        switch (req) {
                            case '0': // ошибка
                                globalError();
                                break;
                            case '1': // пополнить счёт
                                timeActive.removeClass('check');
                                $('.appointment .error .txt').html('У вас недостаточное количество доступных занятий, пополните счёт, чтобы продолжить.')
                                $('.appointment .error').addClass('active');
                                break;
                            case '2': // отмена занятия
                                timeActive.removeClass('busy check').addClass('active');
                                timeActive.children('.ava').remove();
                                count.text(parseInt(count.text())+1);
                                break;
                            case '3': // ошибка отмены
                                $('.appointment .error .txt').html('Время свободной отмены занятия прошло!')
                                $('.appointment .error').addClass('active');
                                break;
                            case '4': // не доступное время
                                $('.appointment .error .txt').html('Это время больше не доступно для записи!')
                                $('.appointment .error').addClass('active');
                                break;
                            default: // если всё чётко
                                timeActive.removeClass('active check').addClass('busy');
                                timeActive.append(req);
                                count.text(parseInt(count.text())-1);
                                break;
                        }
                    }
                }); // end ajax
            } // end if
        }); // end .entry .send

        // Закрытие таблички с записью
        $('.appointment .appmodal .close').click(function() {
            $('.focusTime').val('');
            $('.order .item').removeClass('check');
            $(this).parent().removeClass('active');
        });

        // Закрытие таблички с записью для ошибки
        $('.appointment .error .send').click(function() {
            $(this).parent().removeClass('active');
        });

        $('.reviews .my .pencil').click(function () {
            let parent = $(this).parent();
            $('.reviewText').val($('.reviews .my .cloud .text').text());
            $('.reviewAnonymously').val((parent.hasClass('anms'))?1:0);
            parent.addClass('edit');
        });

        $('.reviews .my .reviewText').focus(function () {
            $(this).parent().removeClass('error');
        });

        $('.reviews .my .reFooter .btn').click(function () {
            let parent = $(this).parents('.my');
            parent.toggleClass('anms');
            $('.reviewAnonymously').val((parent.hasClass('anms'))?1:0);
        });

        $('.reviews .my .stars .icon').click(function () {
            let my = $(this).parents('.my');
            if (my.hasClass('new') || my.hasClass('edit')) {
                $(this).parent().removeClass('error');
                let num = $(this).attr('numb');
                let next = $(this).next().hasClass('active');
                if (!next && $(this).hasClass('active')) {
                    changeClassStar('.reviews .my .stars .icon', 0, 'active');
                    $('.reviewRating').val(0);
                } else {
                    changeClassStar('.reviews .my .stars .icon', num, 'active');
                    $('.reviewRating').val(num);
                }
            }
        });

        $('.reviews .my .stars .icon').mouseover(function () {
            changeClassStar('.reviews .my .stars .icon', $(this).attr('numb'), 'hover');
        });

        $('.reviews .my .stars').mouseout(function(){
            changeClassStar('.reviews .my .stars .icon', 0, 'hover');
        });

        $('.reviews .my .send').click(function () {
            let text = $('.reviewText').val();
            if (text == '') {
                $(this).siblings('.cloud').addClass('error');
                return false;
            }

            let rating = $('.reviewRating').val();
            if (rating == 0) {
                $(this).siblings('.stars').addClass('error');
                return false;
            }

            let data = $(this).parent().serialize();
                data += '&teacher_id='+user_id;
                data += '&_csrf='+csrf;
                data += '&gmt='+GMT;
            $.ajax({
                'url': linkParent+'/review',
                'type': 'POST',
                'data': data,
                success: function (req) {
                    if (req == 0) {
                        globalError();
                    } else {
                        $('.reviews .my .cloud .text').text(text);
                        if ($('.reviews .my').hasClass('new'))
                            $('.reviews .my .reFooter .date').text('ТОЛЬКО ЧТО');
                        $('.reviews .my').removeClass('new edit');
                    }
                }
            });
        });

        if ($ownProfile) {
            // Открытие возможности редактировать цитату
            $('.editor .dictum .text').click(function () {
                let txt = $(this);
                let hg = txt.height();
                let textarea = $('.editor .dictum textarea');
                if (!$(this).hasClass('default')) 
                    textarea.val(txt.text());
                textarea.height(hg + 50);
                $(this).parent().addClass('edit');
            }); // end .aboutMe .txt

            // Изменение информации о себе
            $('.editor .dictum textarea').focusout(function () {
                let txt = $(this).val();
                let def = $(this).attr('placeholder');
                $.ajax({
                    url: linkParent+'/change-phrase',
                    method: 'POST',
                    data: {
                        '_csrf': csrf,
                        'id': user_id,
                        'txt': txt
                    },
                    success: function (req) {
                        if (req == 1) {
                            let txtBlock = $('.dictum .text');
                            if (txt == '') {
                                txtBlock.text(def);
                                txtBlock.addClass('default');
                            } else {
                                txtBlock.text(txt);
                                txtBlock.removeClass('default');
                            }
                            $('.dictum').removeClass('edit');
                        }
                    }
                }); // end ajax
            }); // end .aboutMe textarea

            // Редактирование ссылки видео
            $('.hellowVideo .btn').click(function () {
                if ($('.hellowVideo .blackout').hasClass('active')) {
                    $('.hellowVideo .blackout').removeClass('active');
                } else {
                    $('.hellowVideo .blackout').addClass('active');
                }
            });

            // Сохранение ссылки
            $('.editor .hellowVideo .save').click(function () {
                let link = $('.hellowVideo .link').val().replace(/\s+/g, '');
                if (link.indexOf('https') > -1) { //&& link.indexOf('youtube') > -1
                    $.ajax({
                        url: linkParent+'/set-videolink',
                        method: 'POST',
                        data: {
                            '_csrf' : csrf,
                            'id' : user_id,
                            'link' : link
                            },
                        success: function (data) {
                            if (data != 0) {
                                $('.hellowVideo iframe').attr('src', data);
                                $('.hellowVideo .blackout').removeClass('active');
                            }
                        }
                    });
                }
            });

            // смена основного времени
            $('.editor .appointment .action .change').click(function () {
                let data = `_csrf=${csrf}&id=${user_id}&`+$('#formAppointment').serialize();
                // console.log(data);
                if ($('.editor .order .item').is('.check')) {
                    $.ajax({
                        url: linkParent+'/change-time',
                        method: 'POST',
                        data: data,
                        success: function (req) {
                            if (req == 0) {
                                globalError();
                            } else {
                                let dNum = $('#week-'+$('.weeks').attr('numb')+' .days .active').attr('numb');
                                let time = $('.focusTime').val();

                                $('.week').each( function(index) {
                                    let t = $("#day"+dNum+"-w"+index+" .item[time='"+time+"']");
                                    if (req == 1) {
                                        t.addClass('active');
                                    } else if (req == 2) {
                                        t.removeClass('active');
                                    }
                                });
                                $('.editor .order .item.check').click();
                            }
                        }
                    });
                }
            });

            // Локальое изменение времени
            $('.editor .appointment .action .editTime').click(function () {
                let data = `_csrf=${csrf}&id=${user_id}&`+$('#formAppointment').serialize();
                // console.log(data);
                if ($('.editor .order .item').is('.check')) {
                    $.ajax({
                        url: linkParent+'/edit-time',
                        method: 'POST',
                        data: data,
                        success: function (req) {
                            console.log(req);
                            if (req == 0) {
                                globalError();
                            } else {
                                let timeBlock = $('.editor .order .item.check');
                                switch (req) {
                                    case 'crAdd':
                                        timeBlock.addClass('add');
                                        break;

                                    case 'delAdd':
                                        timeBlock.removeClass('add');
                                        break;

                                    case 'crBlc':
                                        timeBlock.addClass('blc');
                                        break;

                                    case 'delBlc':
                                        timeBlock.removeClass('blc');
                                        break;
                                
                                    default:
                                        break;
                                }
                                timeBlock.click();
                            }
                        } // end success
                    }); // end ajax
                } // end if .check
            }); // end editTime

            // За сколько блокируется время
            $('.editor .workHours .timeLock').change(function () {
                let hour = $(this).val();
                if (hour == '')
                    return false;

                $.ajax({
                    url: linkParent+'/change-timelock',
                    method: 'POST',
                    data: {
                        '_csrf': csrf,
                        'id': user_id,
                        'hour': hour
                    },
                    errpr: () => { globalError(); }
                }); // end ajax
            }); // end timelock

            // Открытие возможности редактировать
            $('.editor .aboutMe .txt').click(function () {
                let txt = $(this).text();
                let hg = $(this).height();
                let textarea = $('.aboutMe textarea[name=aboutmeText]');
                if (!$(this).hasClass('default')) 
                    textarea.val($(this).text());
                textarea.height($(this).height() + 50);
                $(this).parents('.aboutMe').addClass('edit');
            }); // end .aboutMe .txt

            // Изменение информации о себе
            $('.editor .aboutMe textarea[name=aboutmeText]').focusout(function () {
                let txt = $(this).val();
                let def = $(this).attr('placeholder');
                $.ajax({
                    url: linkParent+'/change-aboutme',
                    method: 'POST',
                    data: {
                        '_csrf': csrf,
                        'id': user_id,
                        'txt': txt
                    },
                    success: function (req) {
                        if (req == 1) {
                            let txtBlock = $('.aboutMe .txt');
                            if (txt == '') {
                                txtBlock.text(def);
                                txtBlock.addClass('default');
                            } else {
                                txtBlock.text(txt);
                                txtBlock.removeClass('default');
                            }
                            $('.aboutMe').removeClass('edit');
                        }
                    }
                }); // end ajax
            }); // end .aboutMe textarea
        } // end teacher
    }); // end ready

    function mainCheck() {
        return true;
    }

    function activeSubject(entry, sub) {
        let active = entry.children('.subject').children('.activeSubject');
            active.attr('numb',sub);
            entry.children('.subject').children('.list').children('.item').each(function () {
                $(this).removeClass('active');
                if ($(this).attr('numb') == sub) {
                    $(this).addClass('active');
                    active.attr('numb',sub);
                    active.text($(this).text());
                }
            });
    }
JS;

$this->registerJs($js);

?>
