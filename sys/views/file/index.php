<?php
use yii\helpers\Html;
$this->title = \Yii::t('app',  'Sheet music');
?>
<div>
    <div class="row search-section">
        <div class="col-md-5 col-xs-12">  
            <?= Html::beginForm([''], 'get') ?>
            <div class="display-flex">
                <?= Html::input('text', 'note_filter', $note_filter, ['class' => 'content-input']) ?>
                <?= Html::submitButton(\Yii::t('app', 'Search'), ['class' => 'btn btn-orange']) ?>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div> 
    <div class="file-files">
        <?php if ($lecturefiles) { ?>
            <?= $this->render('docs', ['lecturefiles' => $lecturefiles, 'docs' => $docs]); ?>
        <?php } ?>
    </div>
</div>