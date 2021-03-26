<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Userlectureevaluations */

$this->title = \Yii::t('app',  'Create student evaluation');
['label' => \Yii::t('app',  'Student evaluations'), 'url' => ['index']];

?>
<div class="userlectureevaluations-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>