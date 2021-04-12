<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Evaluations */

$this->title = 'Izveidot novērtējumu';
['label' => \Yii::t('app',  'Evaluations'), 'url' => ['index']];

?>
<div class="evaluations-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>