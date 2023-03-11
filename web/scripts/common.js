
$(window).on('load', function () {
    $('.preloader').addClass('hide');
    setTimeout(() => {
        $('.preloader').hide(0);
    }, 500);
});

$(window).bind('beforeunload', function(){
    $('.preloader').show(0);
    $('.preloader').removeClass('hide');
});

// локальная дата
var dateNow = new Date();
// получаем разницу часовых поясов
var GMT = dateNow.getTimezoneOffset()/60 * -1;
$("input[name=GMT]").val(GMT);

// localStorage.setItem("GMT", GMT);
if (!$.cookie('work'))
    $.cookie('work', true, { expires : 365, path: '/',  });
if ($.cookie('GMT') != GMT)
    $.cookie('GMT', GMT, { expires : 365, path: '/' });

// Выделяем активный пункт меню
$('.navbar .menu .item').each(function () {

    var link = document.location.href;
    // Вытаскиваем адресную строку и проверяем её на наличие той страницы на которой мы находимся
    if (link.indexOf($(this).attr('href')) > 0) {
        $('.navbar .menu .item').removeClass('active');
        $(this).addClass('active');
    }

});

// Закрытие прелоудера
// $(window).on("load", function () {
//     $('#bgPreloader').fadeOut("slow");
//     window.onbeforeunload = function () { $('#bgPreloader').fadeIn("slow"); }
// });

$(".modalBody").click(function (e) {
    if (e.target.className.indexOf("modalBody") > -1) {
        closeModal();
    }
});

$(".modal .close").click(function () {
    closeModal();
});

$(".modal .cancel").click(function () {
    closeModal();
});

$(".modal .confirm").click(function () {
    closeModal();
});

function openModal(modal) {
    $(".modalBody").css("display", "flex");
    $(modal).css("display", "block").addClass("fadeInDown");
}

// Закрытие модального окна
function closeModal() {
    var modal = $(".modalBody").children(".fadeInDown");
    modal.addClass("fadeOutUp").removeClass("fadeInDown");
    setTimeout(function () {
        $(".modalBody").fadeOut(500);
        if ($('.modalBody .youtube').hasClass('fadeOutUp')) {
            $('.modalBody .youtube iframe').attr('src', '');
        }
        modal.css("display", "none").removeClass("fadeOutUp");

    }, 500);
}

function scrollTop(time = 1000, top = 0) {
    $('html, body').animate({
        scrollTop: top
    }, time);
}

// Глушилка
function globalError(message = null) {
    message = (message == null) ? "Что-то пошло не так!" : message;
    alert(message);
}

function ajaxError(clarification = '', jqXHR = null, message = null) {
    globalError(message);
    console.log( "ОШИБКА AJAX запроса: " + clarification, jqXHR );
}

function changeClassStar(item, num, cls) {
    $(item).each(function (i, e) {
        if (num >= (i+1)) {
            if (cls == 'active')
                $(this).addClass('icon-star').removeClass('icon-star-o');
            $(this).addClass(cls);
        } else {
            if (cls == 'active')
                $(this).addClass('icon-star-o').removeClass('icon-star');
            $(this).removeClass(cls);
        }
    });
}

// Выводим дату в нужном формате
function formatDate(date) {
    var monthNames = [
      "Января", "Февраля", "Марта",
      "Апреля", "Мая", "Июня", "Июля",
      "Августа", "Сентября", "Октября",
      "Ноября", "Декабря"
    ];
  
    var day = date.getDate();
        day = (day <= 9)?'0'+day:''+day;
    var monthIndex = date.getMonth();
    var year = date.getFullYear();
  
    return day + ' ' + monthNames[monthIndex] + ' ' + year;
}

// timer
function setTimer(func=null) {
    let timer = setInterval(() => {
        let sec = parseInt($("#sec").text(), 10);
        --sec;
        if (sec < 0) {
            let min = parseInt($("#min").text(), 10);
            --min;
            if (min < 0) {
                let hour = parseInt($("#hour").text(), 10);
                --hour;

                if (hour < 0) {
                    let day = parseInt($("#day").text(), 10);
                    --day;

                    if (day < 0) {
                        clearInterval(timer);
                        if (typeof func == 'function')
                            func();
                        day =  0;
                        hour = 0;
                    } else
                        hour = 23;

                    $('#day').text((day < 10) ? '0'+day : day);
                    min = 0;
                } else
                    min = 59;

                $('#hour').text((hour < 10) ? '0'+hour : hour);
                sec = 0;
            } else
                sec = 59;
            $('#min').text((min < 10) ? '0'+min : min);
        }
        $('#sec').text((sec < 10) ? '0'+sec : sec);
    }, 1000);
}