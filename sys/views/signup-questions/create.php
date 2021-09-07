<?php

use yii\helpers\Html;

$this->title = \Yii::t('app', 'Create question');
['label' => \Yii::t('app', 'Signup questions'), 'url' => ['index']];

?>
<div class="difficulties-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>