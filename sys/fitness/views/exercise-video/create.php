<?php

use yii\helpers\Html;

$this->title = \Yii::t('app',  'Create exercise video');

?>
<div class="lectures-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>