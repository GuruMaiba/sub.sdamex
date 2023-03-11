<?php

function debug($arr) {
    echo '<pre>' . print_r($arr, true) . '</pre>';
}

function ruMonth($str) {
    $monthEn = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    $monthRu = [
        'Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня',
        'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'
    ];
    return str_replace($monthEn, $monthRu, $str);
}

function viewStars($num) {
    for ($i=1; $i <= 5; $i++) {
        $class = 'icon-star';
        if ($i > $num) {
            if ( ($i-0.5) <= $num )
                $class .= '-half';
            $class .= '-o';
        }
        $class .= ($class == 'icon-star') ? ' active' : '';
        echo '<i class="icon '.$class.'" numb="'.$i.'"></i>';
    }
}