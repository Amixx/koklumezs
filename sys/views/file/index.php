<?php
use yii\helpers\Html;
$this->title = \Yii::t('app',  'Sheet music');
?>
<div>
    <div class="row" style="margin-top: 10px; margin-bottom: 25px;">
        <?= Html::beginForm([''], 'get') ?>
        <?= Html::input('text', 'note_filter', $note_filter, ['class' => 'content-input']) ?>
        <?= Html::submitButton(\Yii::t('app', 'Search'), ['class' => 'btn btn-orange']) ?>
        <?= Html::a(\Yii::t('app', 'Show all'), [''], ['class' => 'btn btn-orange']) ?>
        <?= Html::endForm() ?>
    </div> 
    <div class="row" style="padding-top: 25px;">
        <?php if ($lecturefiles) { ?>
            <?= $this->render('docs', ['lecturefiles' => $lecturefiles, 'docs' => $docs]); ?>
        <?php } ?>
    </div>
</div>