<?php

namespace app\models\exam\write;

use Yii;
use yii\httpclient\Client;
use app\models\{User, Teacher, AppActiveRecord};

/**
 * This is the model class for table "examwrite_answer".
 *
 * @property int $id
 * @property int $examresult_id
 * @property int $question_id
 * @property int $user_id
 * @property string $text
 *
 * @property User $user
 */
class Reply extends AppActiveRecord
{
    public $name = null;
    public $answerFiles = [];
    public const FILE_SIZE = 10*1024*1024;
    public const FILE_TYPES = [
        'jpg', 'jpeg', 'png', 'doc', 'docx', 'docm', 'txt', 'zip', 'rar'
    ];
    public const COUNT_FILES = 10;
    public const COUNT_CHARS = 4000;

    public static function tableName()
    {
        return 'examwrite_answer';
    }

    public function rules()
    {
        return [
            [['examwrite_id', 'user_id'], 'required'],
            [['examwrite_id', 'user_id', 'teacher_id', 'exp', 'points'], 'integer'],
            // [['answerFiles'], 'file', 'skipOnEmpty' => false, 'maxSize' => 1024*1024, 'maxFiles' => 10],
            [['text', 'archive_file', 'teacher_comment'], 'string'],
            [['check'], 'boolean'],
            [['user_id'], 'exist', 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['examwrite_id'], 'exist', 'targetClass' => Write::className(), 'targetAttribute' => ['examwrite_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'teacher_comment' => 'Комментарий учителя',
            'exp' => 'Количество опыта',
            'points' => 'Количество баллов в полном экзамене',
        ];
    }

    // public function uploadAnswerFiles()
    // {
    //     $files = [];
    //     foreach ($this->answerFiles as $key => $file) {
    //         $name = explode('.',$file['name']);
    //         do {
    //             $name = substr(str_shuffle($this->permitted_chars), 0, 16).'.'.array_pop($name);
    //         } while (file_exists(Yii::getAlias("@fileWrites/$name")));
    
    //         if (move_uploaded_file( $file['tmp_name'], Yii::getAlias("@fileWrites/$name") ))
    //             $files[] = $name;
    //     }
    //     $this->files = json_encode($files);
    //     return true;
    // }

    public function createZip()
    {
        do {
            $name = substr(str_shuffle($this->permitted_chars), 0, 16).'.zip';
        } while (file_exists(Yii::getAlias("@fileWrites/$name")));

        $zip = new \ZipArchive();
        $path = Yii::getAlias("@fileWrites/$name");
        if ($zip->open($path, \ZipArchive::CREATE) !== TRUE)
            return false;

        foreach($this->answerFiles as $file)
            $zip->addFile($file['tmp_name'], $file['name']);
        $zip->close();

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl(Yii::$app->params['listSubs'][1]['link'].'site/save-file')
            ->setData([ 'secretKey' => Yii::$app->params['secretKey'], ])
            ->addFile('file', $path)
            ->send();

        if ($response->isOk && $response->data['req'] != 0) {
            unlink($path);
            $name = $response->data['req'];
        }

        return $this->archive_file = $name;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getTeacher()
    {
        return $this->hasOne(Teacher::className(), ['user_id' => 'teacher_id']);
    }

    public function getWrite()
    {
        return $this->hasOne(Write::className(), ['id' => 'examwrite_id']);
    }
}
