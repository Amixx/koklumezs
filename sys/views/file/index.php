<?php
use yii\helpers\Html;
$this->title = \Yii::t('app',  'Sheet music');
?>
<div>
    <div class="row search-section">
        <?= Html::beginForm([''], 'get') ?>
        <?= Html::input('text', 'note_filter', $note_filter, ['class' => 'content-input']) ?>
        <?= Html::submitButton(\Yii::t('app', 'Search'), ['class' => 'btn btn-orange search-button']) ?>
        <?= Html::endForm() ?>
    </div> 
    <div class="file-files">
        <?php if ($lecturefiles) { ?>
            <?= $this->render('docs', ['lecturefiles' => $lecturefiles]); ?>
        <?php } ?>
    </div>
</div>