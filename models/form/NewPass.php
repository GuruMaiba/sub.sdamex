<?php

namespace app\models\form;

use yii\base\Model;

class NewPass extends Model
{
    public $newPass;
    public $retypePass;

    public function rules()
    {
        return [
            [['newPass', 'retypePass'], 'required'],
            [['newPass', 'retypePass'], 'string', 'min' => 6],
            ['retypePass', 'compare', 'compareAttribute' => 'newPass'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'newPass' => 'Новый пароль',
            'retypePass' => 'Повтор пароля',
        ];
    }
}

 ?>
