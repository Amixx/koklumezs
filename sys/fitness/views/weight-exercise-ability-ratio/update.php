<?php

use yii\helpers\Html;

$this->title = \Yii::t('app', 'Edit exercise video');
?>
<div class="lectures-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>