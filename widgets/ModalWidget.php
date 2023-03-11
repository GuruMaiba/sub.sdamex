<?php
namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;

class ModalWidget extends Widget
{
    public $blockId;
    public $blockClass;
    public $title;
    public $desc;

    // СТРУКТУРА МАССИВА
    // ==================
    // id - Идентификатор формы
    // class - Класс формы
    // ----------
    // inputs - ['typeInput', 'label', 'name', 'placeholder', '???value???']
    // typeInput - input, textarea, checkbox(1)
    // ||||||||||||||||||
    public $form = [];

    public $buttons = [];
    public $js;
    public $isError = false;

    public function init()
    {
        parent::init();
        if (!empty($this->buttons['confirm']) && !empty($this->buttons['send'])) {
            unset($this->buttons['confirm']);
        }
        if ($this->blockId === null && $this->blockClass === null) {
            $this->isError = true;
        }
    }

    public function run()
    {
        if (!$this->isError) {
            return $this->render('modal', [
                'modal' => $this,
            ]);
        }
    }
}
