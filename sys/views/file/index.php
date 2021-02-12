<?php
use yii\helpers\Html;
$this->title = \Yii::t('app',  'Sheet music');
?>
<div>
    <div>
        <h3><?= \Yii::t('app', 'Sheet music') ?></h3>
        <div class="col-sm-12">
            <?= Html::beginForm([''], 'get') ?>
            <?= Html::input('text', 'note_filter', $note_filter) ?>
            <?= Html::submitButton(\Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
            <?= Html::a(\Yii::t('app', 'Show all'), [''], ['class' => 'btn btn-primary']) ?>
            <?= Html::endForm() ?>
        </div>
    </div> 
    <div class="row" style="padding-top: 25px;">
        <?php if ($lecturefiles) { ?>
            <?= $this->render('docs', ['lecturefiles' => $lecturefiles, 'docs' => $docs]); ?>
        <?php } ?>
    </div>
</div>