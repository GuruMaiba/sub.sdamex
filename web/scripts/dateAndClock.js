function dateLottery() {
    // Создаем две переменные с названиями месяцев и названиями дней.
    var monthNames = ["Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря"];
    var dayNames = ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"]

    // Создаем объект newDate()
    // new Date(year, month[, day[, hour[, minute[, second[, millisecond]]]]]);
    var dateLottery = new Date(@dateLott.Year, @(dateLott.Month - 1), @dateLott.Day, @dateLott.Hour, @dateLott.Minute, 0);
    // текущая дата по гринвичу
    var nowUTC = new Date(@DateTime.UtcNow.Year, @(DateTime.UtcNow.Month - 1), @DateTime.UtcNow.Day, @DateTime.UtcNow.Hour, @DateTime.UtcNow.Minute, @DateTime.UtcNow.Second);
    // разница дат в гринвиче
    var dateOdds = dateLottery.getTime() - nowUTC.getTime();

    // "Достаем" текущую дату из объекта Date
    dateLottery.setDate(dateLottery.getDate());
    // прибавляем к часам разницу...
    var hour = dateLottery.getHours() + GMT;
    var min = dateLottery.getMinutes();

    if (hour > 23) { hour = hour - 24; }
    else if (hour < 0) { hour = 24 + hour; }
    hour = (hour < 10) ? "0" + hour : hour;
    min = (min < 10) ? "0" + min : min;
}

// Получаем день недели, число месяц (в словесном представление) год час:минуту
$('#Date').html(dayNames[dateLottery.getDay()] + ", " +
    dateLottery.getDate() + ' ' +
    monthNames[dateLottery.getMonth()] + ' ' +
    dateLottery.getFullYear() + ' ' +
    hour + ':' + min);

if (countUserForLottery == 0 || countUserForLottery <= parseInt($("#countUser").text())) {
    var dateLotteryInterval = setInterval(function () {

        // Текущая дата
        dateNow = new Date();
        dateNow.setHours(dateNow.getUTCHours());
        // Разница даты текущей и даты проведения
        dateOdds = dateLottery.getTime() - dateNow.getTime();

        if (dateOdds > 0) {
            // Вычисляем количество каждой еденицы
            var day = dateOdds / (24 * 3600 * 1000) | 0;
            var hours = (dateOdds % (24 * 3600 * 1000)) / (3600 * 1000) | 0;
            var minutes = ((dateOdds % (24 * 3600 * 1000)) % (3600 * 1000)) / (60 * 1000) | 0;
            var seconds = (((dateOdds % (24 * 3600 * 1000)) % (3600 * 1000)) % (60 * 1000)) / 1000 | 0;

            // Добавляем ноль в начало цифры, которые до 10
            $("#sec").html((seconds < 10 ? "0" : "") + seconds);
            $("#min").html((minutes < 10 ? "0" : "") + minutes);
            $("#hours").html((hours < 10 ? "0" : "") + hours);
            $("#day").html((day < 10 ? "0" : "") + day);
        } else {
            // Выставляем везде 00
            $("#sec").html("00");
            $("#min").html("00");
            $("#hours").html("00");
            $("#day").html("00");

            @if (!editLott)
            {<text>$(".drawDefaultHolding").fadeIn(0);</text>}
            else
            {<text>$(".drawHoldingLink").css("display", "block");</text>}

            clearInterval(dateLotteryInterval);

            @if (!editLott)
            {
                <text>
                    // Проверяем начало розыгрыша
                    var checkBeginLott = setInterval(function () {
                        $.ajax({
                            url: "/Lottery/CheckBeginLott",
                            type: "POST",
                            data: {
                                LotteryId: lotteryId
                            },
                            success: function (data) {
                                if (data == "1") {
                                    $(".drawDefaultHolding").fadeOut(0);
                                    $(".drawHoldingLink").css("display", "block");
                                } else if (data != "0") {
                                    $("#updateWinnerBlock").html(data);
                                }
                            }
                        });
                    }, 5000);
                </text>
            }
        }

    }, 1000);
}
