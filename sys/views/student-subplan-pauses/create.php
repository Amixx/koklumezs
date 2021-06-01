<?php

use yii\helpers\Html;

$this->title = \Yii::t('app', 'Create plan pause');
['label' => \Yii::t('app', 'Plan pauses'), 'url' => ['index']];

?>
<div class="difficulties-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'studentSubPlans' => $studentSubPlans,
    ]) ?>
</div>