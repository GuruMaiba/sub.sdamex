<?php
// use app\widgets\ModalWidget;
?>
<?php // ModalWidget::begin(); ?>
<?php // КАКОЙ-ТО HTML КОД ?>
<?php // ModalWidget::end(); ?>

<div id="<?= $modal->blockId ?>" class="modal <?= $modal->blockClass ?>">
    <div class="close"><i class="icon-cancel"></i></div>
    <div class="bigTitle"><?= $modal->title ?></div>
    <div class="cont">
        <div class="desc">
            <?= $modal->desc ?>
        </div>
        <form id="<?= $modal->form['id'] ?>" class="form <?= $modal->form['class'] ?>" autocomplete="off">
            <?php
                $checkFlag = false;
                foreach ($modal->form['inputs'] as $attr) {
                    switch ($attr['type']) {
                        case 'hidden':?>
                            <input type="hidden" name="<?=$attr['name']?>" value="<?=$attr['value']?>">
                            <?php break;

                        case 'input':?>
                            <div class="inputBlock">
                                <input type="text" name="<?=$attr['name']?>" placeholder="<?=$attr['placeholder']?>" value="<?=$attr['value']?>">
                            </div>
                            <?php break;

                        case 'textarea':?>
                            <div class="inputBlock">
                                <textarea name="<?=$attr['name']?>" placeholder="<?=$attr['placeholder']?>"><?=$attr['value']?></textarea>
                            </div>
                            <?php break;

                        case 'checkbox':
                            if (!$checkFlag) :?>
                                <div class="checkboxBlock">
                                    <input id='cbx_<?=$attr['name']?>' class="checkMark" name="<?=$attr['name']?>" type='checkbox' />
                                    <label for='cbx_<?=$attr['name']?>'>
                                        <span></span>
                                        <div class="txt"><i><?=$attr['label']?></i></div>
                                    </label>
                                </div>
                            <?php endif;
                            $checkFlag = true;
                            break;

                        default:
                            break;
                    }
                }
            ?>
        </form>
    </div>
    <div class="buttons">
        <?php foreach ($modal->buttons as $type => $name): ?>
            <div class="item <?= $type ?>"><?= $name ?></div>
        <?php endforeach; ?>
    </div>
</div>

<?php
// $js = <<<JS
//
// JS;
if ($modal->js !== null) {
    $this->registerJs($js);
}
 ?>
