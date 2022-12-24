<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lectures */

$this->title = \Yii::t('app', 'Edit tag') . ': ' . $model->value;
['label' => \Yii::t('app',  'Tags'), 'url' => ['index']];
['label' => $model->value, 'url' => ['view', 'id' => $model->id]];
\Yii::t('app',  'Edit');
?>
<div class="lectures-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'tagTypeSelectOptions' => $tagTypeSelectOptions,
    ]) ?>

</div>