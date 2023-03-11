<?php

namespace app\models;

use Yii;
use yii\imagine\Image;
use Imagine\Image\Box;

class AppActiveRecord extends \yii\db\ActiveRecord
{
    public $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public $savePath = '';  // Путь сохранения, НЕ КАРТИНКИ
    public $image;          // Картинка
    public $settings = [    // Настройки
        // 'main' => [         // Главный путь (если единственный)
        //     'path' => '',
        //     'size' => [
        //         'w' => 0,
        //         'h' => 0,
        //     ],
        // ],
        // 'large' => [        // Путь большой (если есть разделение)
        //     'path' => '',
        //     'size' => [
        //         'w' => 0,
        //         'h' => 0,
        //     ],
        // ],
        // 'small' => [        // Путь маленький (если есть разделение)
        //     'path' => '',
        //     'size' => [
        //         'w' => 0,
        //         'h' => 0,
        //     ],
        // ],
    ];

    // Загрузить фаил
    public function fileUpload($file, $oldFile = null) {

        // $name = $file->baseName . '.' . $file->extension;
        do {
            $name = substr(str_shuffle($this->permitted_chars), 0, 16).'.'.$file->extension;
        } while (file_exists($this->savePath.$name));

        $file->saveAs($this->savePath.$name);
        if ($oldFile != null)
            $this->deleteFile($oldFile);

        return $name;
    }

    public function deleteFile($name) {
        if ($name != null && file_exists($this->savePath.$name))
            unlink($this->savePath.$name);
    }

    // Загрузить картинку
    public function imageUpload($oldImg=null) {
        $sett = $this->settings;
        if ($sett == [])
            return false;
        
        $name = $this->image->name; // $this->image->baseName . '.' . $this->image->extension
        $tmp = Yii::getAlias("@imgTeamp");

        while (file_exists("$tmp/$name"))
            $name = 't'.$name;

        $tmp = "$tmp/$name";
        $this->image->saveAs($tmp);

        $flag = false;
        foreach ($sett as $type => $arr) {
            if (!$flag) {
                $flag = true;
                do {
                    $name = substr(str_shuffle($this->permitted_chars), 0, 16).'.'.$this->image->extension;
                } while (file_exists("$arr[path]/$name"));
            }

            $imgWidth = $arr['size']['w'];
            $imgHeight = $arr['size']['h'];
            if (empty($imgWidth) && empty($imgHeight))
                list($imgWidth, $imgHeight) = getimagesize($tmp);

            Image::getImagine()->open($tmp)
                ->thumbnail(new Box($imgWidth,$imgHeight))
                ->save(Yii::getAlias("@webroot/$arr[path]/$name"), ['quality' => 70]);
        }
        unlink($tmp);

        $this->deleteOldImg($oldImg);

        $this->image = null;
        return $name;
    }

    public function deleteOldImg($old) {
        if ($old && $old != 'no_img.jpg') {
            foreach ($this->settings as $type => $arr) {
                if (file_exists("$arr[path]/$old"))
                    unlink("$arr[path]/$old");
            }
        }
    } // end del

    protected function debug($arr) {
        echo '<pre>' . print_r($arr, true) . '</pre>';
    }
}
