<?php
use yii\helpers\Html;

$this->title = \Yii::t('app', 'Edit FAQ') . ': ' . $model->song;
 ['label' => \Yii::t('app', 'FAQs'), 'url' => ['index']];
 ['label' => $model->song, 'url' => ['view', 'id' => $model->id]];
 \Yii::t('app', 'Edit');
?>
<div class="suggestion-update">
    
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>