<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\User;
use app\models\exam\{Exercise};

class AppController extends Controller {

    protected function debug($arr) {
        echo '<pre>' . print_r($arr, true) . '</pre>';
    }

    public function meta($title, $desc, $key) {
        return [
            'title' => $title,
            'desc' => $desc,
            'keywords' => $key,
        ];
    }

    public function getSubject($inx=0) {
        if ($inx > 0) {
            foreach (Yii::$app->params['listSubs'] as $name => $val) {
                if ($val['id'] == $inx)
                    return $val;
            }
        }
        return null;
    }

    public function addTagTranslate($text) {
        $th = $this;

        if (strpos($text, '<') === false) {
            return $this->tagWrap($text);
        } else {
            return preg_replace_callback(
                '/\>([\s\S]*?)\</',
                function ($matches) {
                    return $this->tagWrap($matches[0]);
                },
                $text
            );
        }
    }

    public function tagWrap($text) {
        return preg_replace_callback(
            '/[a-zA-Z`]{2,}/',
            function ($matches) {
                return '<translate>'.$matches[0].'</translate>';
            },
            $text
        );
    }

    public function delTagTranslate($text) {
        return str_replace(['<translate>','</translate>'],'',$text);
    }

    public function ruMonth($str) {
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

    public function workTimeArr($day, $time, $gmt) {
        $arr = [];
        $arr['numDay'] = date('w', $day);
        $arr['numDay'] = ($arr['numDay'] == 0) ? 7 : $arr['numDay'];
        $time = explode(':',$time);
        $arr['h'] = (int)$time[0]-$gmt;
        $arr['m'] = (int)$time[1];
        $day -= ($day%(24*60*60));
        $day += ($arr['h']*60*60) + ($arr['m']*60);
        $arr['day'] = $day;

        return $arr;
    }

    public function exeStatUpdate($stat_u, $stat_n, $checkWrite = false, $reqArr = false)
    {
        $sub = \Yii::$app->params['subInx'];

        foreach ($stat_n['sections'] as $sec_id => $sec) {
            foreach ($sec['exercises'] as $exe_id => $exe) {
                if ($stat_n['id'] < 1 || $sec_id < 1 || $exe_id < 1)
                    continue;
                // получаем статистику текущего задания
                $exe_stat = $stat_u[$sub]['exams']['list'][$stat_n['id']]['sections'][$sec_id]['exercises'][$exe_id];
                // если у этого задания ещё нет статистики
                if (!$exe_stat)
                    $exe_stat = User::DEF_STAT_EXE;

                // добавляем id пройденного задания
                if (($exe['corr'] || $exe['skip']) && $exe['task'] > 0 && !in_array($exe['task'], $exe_stat['completed_list']))
                    $exe_stat['completed_list'][] = $exe['task'];

                if (!$exe['skip']) {
                    // обновляем каунтеры
                    $stat_u[$sub]['exams']['count_corr'] += $exe['corr'];
                    $stat_u[$sub]['exams']['count_err'] += !$exe['corr'];
                    if ($checkWrite && !$exe['corr'])
                        $exe_stat['completed_list'] = array_diff($exe_stat['completed_list'], [$exe['task']]);
                    $exe_stat = $this->statChange($exe_stat, $exe['corr']);
                }

                // обновляем статистику
                $stat_u[$sub]['exams']['list'][$stat_n['id']]['sections'][$sec_id]['exercises'][$exe_id] = $exe_stat;
            }
        }

        if ($stat_n['themes']) {
            $themes = $stat_u[$sub]['exams']['list'][$stat_n['id']]['themes'];
            foreach ($stat_n['themes']['corr'] as $th_id) {
                $th_stat = $themes[$th_id];
                if (!$th_stat)
                    $th_stat = User::DEF_STAT_THEME;

                $themes[$th_id] = $this->statChange($th_stat, 1);
            }
            foreach ($stat_n['themes']['err'] as $th_id) {
                $th_stat = $themes[$th_id];
                if (!$th_stat)
                    $th_stat = User::DEF_STAT_THEME;
    
                $themes[$th_id] = $this->statChange($th_stat, 0);
            }
            $stat_u[$sub]['exams']['list'][$stat_n['id']]['themes'] = $themes;
        }
        
        // возвращаем в json представление
        if ($reqArr)
            return $stat_u;
        else
            return json_encode($stat_u);
    }

    public function statChange($stat, $corr)
    {
        // обновляем каунтеры
        $stat['count_corr'] += $corr;
        $stat['count_err'] += ($corr)?0:1;

        // добавляем в последние задания, текущее
        $stat['last'][] = $corr;
        // если их больше максимального количества для статистики
        while (count($stat['last']) > Exercise::STAT_MAX_EXE)
            array_shift($stat['last']); // тогда самый ранний элемент удаляется

        // создаём каунтеры для нахождения процента
        $countArr = 0;
        $countTrue = 0;
        // перебираем массив последних ответов
        foreach ($stat['last'] as $value) {
            $countArr++; // добавляем 1 в общее число
            $countTrue += $value; // добавляем 1/0 в зависимости от ранних ответов пользователя
        }
        // находим процент от числа
        $stat['percent_last'] = (int)($countTrue/$countArr * 100);
        $stat['percent_all'] = (int)($stat['count_corr']/($stat['count_corr']+$stat['count_err']) * 100);

        return $stat;
    }

    public function frameLinkCreation($link)
    {
        if (strripos($link, '/embed/')) {
            return $link;
        } else if (strripos($link, 'youtube')) {
            $link = explode('?v=', $link);

            if (count($link) < 2)
                return '';

            $link = array_pop($link);
            $link = explode('&', $link);

            $link = "https://www.youtube.com/embed/$link[0]";
        } else if (strripos($link, 'youtu.be')) {
            $link = explode('/', $link);
            if (count($link) < 2)
                return '';

            $link = array_pop($link);
            $link = explode('?', $link);

            $link = "https://www.youtube.com/embed/$link[0]";
        } else if (strripos($link, 'vimeo')) {
            $link = explode('/', $link);
            if (count($link) < 2)
                return '';

            $link = array_pop($link);
            $link = explode('#', $link);

            $link = "https://player.vimeo.com/video/$link[0]";
        } else
            $link = '';

        return $link;
    }
}
