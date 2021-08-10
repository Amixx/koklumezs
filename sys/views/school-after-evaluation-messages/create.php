<?php

use yii\helpers\Html;

$this->title = Yii::t('app', 'Create message');

?>
<div class="settings-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>