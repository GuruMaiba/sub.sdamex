<?php
 namespace app\components;

use Yii;
use yii\rbac\Rule;

class AuthorPostRule extends Rule
{
    public $name = 'isAuthor';

    public function execute($id, $item, $params) {
        return isset($params['post']) ? $params['post']->user_id == $id : false;
    }
}
