<?php

namespace app\models\webinar;

use Yii;
use app\models\User;
use app\models\course\Course;
use app\models\exam\test\Test;
use app\models\exam\write\Write;

/**
 * This is the model class for table "webinar".
 *
 * @property int $id
 * @property int $author_id
 * @property int $examtest_id
 * @property int $examwrite_id
 * @property string $live_link
 * @property string $video_link
 * @property string $title
 * @property string $desc
 * @property string $cost
 * @property int $free
 * @property int $public
 * @property int $start
 * @property int $end
 * @property int $start_at
 *
 * @property Test $examtest
 * @property Write $examwrite
 * @property User $user
 */
class Webinar extends \app\models\AppActiveRecord
{
    public const COUNT_VIEW_COMM = 30;
    public const COUNT_VIEW_CHAT = 30;
    public $strDate;
    public $links           = [];
    public $offsetChat      = 0;
    public $offsetComment   = 0;
    public $countMembers;
    public $countCooments;

    public static function tableName()
    {
        return 'webinar';
    }

    public function rules()
    {
        return [
            [['author_id', 'examtest_id', 'examwrite_id', 'start', 'end', 'start_at', 'views'], 'integer'],
            [['title'], 'required'],
            [['desc', 'ava'], 'string'],
            [['cost'], 'number'],
            [['publish'], 'boolean'],
            [['image'], 'file', 'extensions' => 'png, jpg'], // 'skipOnEmpty' => false,
            [['live_link', 'video_link', 'title'], 'string', 'max' => 255],
            [['examtest_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::className(), 'targetAttribute' => ['examtest_id' => 'id']],
            [['examwrite_id'], 'exist', 'skipOnError' => true, 'targetClass' => Write::className(), 'targetAttribute' => ['examwrite_id' => 'id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Пользователь',
            'examtest_id' => 'Тест',
            'examwrite_id' => 'Практическое задание',
            'image' => 'Картинка',
            'live_link' => 'Прямой эфир',
            'video_link' => 'Видео',
            'title' => 'Заголовок',
            'desc' => 'Описание',
            'links' => 'Материалы',
            'cost' => 'Стоимость',
            'publish' => 'Опубликовать',
            'start' => 'Начался',
            'end' => 'Закончился',
            'strDate' => 'Дата начала',
            'views' => 'Просмотры',
        ];
    }

    public function getOffset($countLoad, $newComment)
    {
        return ($countLoad * self::COUNT_VIEW_COMM) + $newComment;
    }

    public function updateOffsetComment($countLoad, $newComment)
    {
        return $this->offsetComment = $this->getOffset($countLoad, $newComment);
    }

    public function updateOffsetChat($countChat, $countLoad = 1, $newComment = 0)
    {
        return $this->offsetChat = $countChat - $this->getOffset($countLoad, $newComment);
    }

    public function getTest()
    {
        return $this->hasOne(Test::className(), ['id' => 'examtest_id']);
    }

    public function getWrite()
    {
        return $this->hasOne(Write::className(), ['id' => 'examwrite_id']);
    }

    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    public function getMembers()
    {
        return $this->hasMany(Member::className(), ['webinar_id' => 'id'])
        ->asArray();
    }

    public function getCourses()
    {
        return $this->hasMany(Course::className(), ['id' => 'course_id'])
            ->viaTable('course_webinar', ['webinar_id' => 'id']);
    }

    // public function getChatMessages()
    // {
    //     return $this->hasMany(Comment::className(), ['webinar_id' => 'id'])
    //         ->where(['chat_or_comment'=>true])
    //         ->limit(self::COUNT_VIEW_COMM)
    //         ->offset($this->offsetChat)
    //         ->asArray();
    // }

    // public function getChatMessCount()
    // {
    //     return $this->hasMany(Comment::className(), ['webinar_id' => 'id'])
    //         ->having(['chat_or_comment'=>true])
    //         ->count();
    // }

    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['webinar_id' => 'id']);
    }

    public function getCommentslim()
    {
        return $this->hasMany(Comment::className(), ['webinar_id' => 'id'])
            ->orderBy(['create_at' => SORT_DESC])
            ->limit(self::COUNT_VIEW_COMM)
            ->offset($this->offsetComment)
            ->asArray();
    }

    // public function getCommentsCount()
    // {
    //     return $this->hasMany(Comment::className(), ['webinar_id' => 'id'])
    //         ->having(['chat_or_comment'=>false])
    //         ->count();
    // }
}
